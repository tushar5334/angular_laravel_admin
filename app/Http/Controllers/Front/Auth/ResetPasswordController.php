<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\Auth\ResetPasswordRequest;
use Illuminate\Http\Request;
use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */
    public function __construct()
    {
        $this->middleware('guest');
    }
    /**
     * Display Reset password form
     *
     * @param  Token  $token
     * @return \Illuminate\Http\Response
     */
    public function showResetPasswordForm($token)
    {
        return view('front.auth.reset_password')->with('token', $token);
    }

    /**
     * Submit Reset password form
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Token  $token
     * @return \Illuminate\Http\Response
     */
    public function postResetPassword(ResetPasswordRequest $request, $token)
    {

        $postData = $request->all();
        try {
            $userData = User::WHERE('token', $postData['token'])->first();
            if (isset($userData) && !empty($userData)) {
                if (isset($postData['password']) && $postData['password']) {
                    $userData->password = Hash::make($postData['password']);
                }
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
                        'login_url' => route('front.get.login'),
                        'message' => 'Password has been reseted sucessfully.',
                    )
                );
                $mailSubject = 'Password Reset Successfully - ' . getenv("PROJECT_NAME");
                $templatePath = 'front.email_template.update_password';

                $emailJobData = [
                    'mailTo' => $mailTo,
                    'mailSubject' => $mailSubject,
                    'emailData' => $data,
                    'templatePath' => $templatePath
                ];

                dispatch(new SendEmailJob($emailJobData))->delay(now()->addSeconds(2));

                return redirect(route('front.get.login'))->with('success', 'Password has been reset sucessfully!');
            } else {
                return back()->with('error', 'Email not found!');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Something Went Wrong.');
        }
    }
}
