<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Front\Auth\ForgotPasswordRequest;
use App\Models\User;
use App\Libraries\General;
use App\Jobs\SendEmailJob;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display Forgot password form
     *
     * @return \Illuminate\Http\Response
     */
    public function showForgotPasswordForm()
    {
        return view('front.auth.forgot_password');
    }

    /**
     * Submit Forgot password form
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postForgotPassword(ForgotPasswordRequest $request)
    {
        $postData = $request->all();

        try {
            $userData = User::WHERE('email', $postData['email'])->first();
            if (isset($userData) && !empty($userData) && ($userData->user_type == "SuperAdmin")) {
                $autoResetToken = General::generate_token();
                $userData->token = $autoResetToken;
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
                        'reset_url' => route('front.get.reset-password', $autoResetToken), //url('/').'/admin/reset-password/'.$autoResetToken,
                        'message' => 'You are receiving this email because we received a password reset request for your account.',
                    )
                );
                $mailSubject = 'Reset Password - ' . getenv("PROJECT_NAME");
                $templatePath = 'front.email_template.reset_password';

                $emailJobData = [
                    'mailTo' => $mailTo,
                    'mailSubject' => $mailSubject,
                    'emailData' => $data,
                    'templatePath' => $templatePath
                ];

                dispatch(new SendEmailJob($emailJobData))->delay(now()->addSeconds(2));

                return redirect(route('front.get.login'))->with('success', 'Reset password link has been sent to your email address successfully!');
            } else {
                return back()->with('error', 'Email not found!');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
