<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Requests\Admin\Auth\RegisterRequest;
use App\Http\Requests\Admin\Auth\ForgotPasswordRequest;
use App\Http\Requests\Admin\Auth\ResetPasswordRequest;
use App\Models\Admin\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Jobs\SendEmailJob;
use App\Services\Admin\AuthService;
use App\Services\Admin\EmailService;

class AuthController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function csrfToken(Request $request)
    {
        return response(['token' => csrf_token()]);
    }

    public function login(LoginRequest $request)
    {
        // Check if a user with the specified email exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Wrong email or password'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // If a user with the email was found - check if the specified password
        // belongs to this user
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Wrong email or password'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = $user->createToken('web-app');

        // Get the data from the response
        $data['user'] = ['name' => $user->name, 'email' => $user->email, 'display_picture' => $user->display_picture];
        $data['token'] = $token->plainTextToken;
        return ['data' => $data, 'message' => 'Welcome to admin 😃'];
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request, AuthService $authService)
    {
        $isUserCreated = $authService->createUser($request->validated());
        if ($isUserCreated) {
            return response(['message' => 'Registration has been done successfully'], Response::HTTP_OK);
        }
        return response(['message' => 'Something went wrong, Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function postForgotPassword(ForgotPasswordRequest $request, AuthService $authService)
    {
        try {
            $user = $authService->forgotPassword($request->validated());

            if (!$user) {
                return response(['message' => 'Email not found.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response(['message' => 'Reset password link has been sent to your email address successfully!'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response(['message' => 'Something went wrong, Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Submit Reset password form
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Token  $token
     * @return \Illuminate\Http\Response
     */
    public function postResetPassword(ResetPasswordRequest $request)
    {
        try {
            $requestData = $request->all();
            $userData = User::WHERE('token', $requestData['reset_token'])->first();
            if (empty($userData)) {
                return response(['message' => 'Something went wrong, Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $userData->password = $requestData['password'];
            $userData->token = NULL;
            $userData->save();

            $mailTo['to'] = array(
                array(
                    'email' => $userData->email,
                    'display_name' => $userData->name
                )
            );
            $data = array(
                'siteurl' => getenv('APP_URL'),
                'mailcontent' => array(
                    'name' => $userData->name,
                    'login_url' => url('/app/admin/login'),
                    'message' => 'Password has been reset sucessfully.',
                )
            );
            $mailSubject = 'Password Reset Successfully - ' . getenv("PROJECT_NAME");
            $templatePath = 'admin.email_template.update_password';

            $emailJobData = [
                'mailTo' => $mailTo,
                'mailSubject' => $mailSubject,
                'emailData' => $data,
                'templatePath' => $templatePath
            ];

            dispatch(new SendEmailJob($emailJobData))->delay(now()->addSeconds(2));

            return response(['message' => 'Password has been reset sucessfully.'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response(['message' => 'Something went wrong, Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function me(Request $request)
    {
        return response(['user' => $request->user()], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();
        return response(['message' => 'You have been logged out successfully.'], Response::HTTP_OK);
    }

    public function logoutAll(Request $request)
    {
        // Revoke all tokens...
        $user = request()->user();
        $user->tokens()->delete();
    }

    public function logoutBytokenId(Request $request)
    {
        // Revoke a specific token...
        $tokenId = '4|U4fKEXqRhniuwTsFV6csvGdivrltn0xUr71nikI6';
        $user = request()->user();
        $user->tokens()->where('id', $tokenId)->delete();
    }
}
