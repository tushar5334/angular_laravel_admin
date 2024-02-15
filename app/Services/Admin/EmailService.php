<?php

/**
 * @uses Email Notifications Related Functions
 * @author Tushar Patil <tushar5334@gmail.com>
 * @return
 */

namespace App\Services\Admin;

use App\Jobs\SendEmailJob;
use App\Models\Admin\User;

class EmailService
{

    /**
     * @author Tushar Patil <tushar5334@gmail.com>
     *
     * @uses Send forgot password email notification to user
     *
     * @return bool
     */

    public function sendForgotPasswordEmailNotification(User $data, string $autoResetToken): bool
    {
        $mailTo['to'] = array(
            array(
                'email' => $data->email,
                'display_name' => $data->name
            )
        );
        $data = array(
            'siteurl' => getenv('APP_URL'),
            'mailcontent' => array(
                'name' => $data->name,
                'reset_url' => url('/') . '/app/admin/reset-password/' . $autoResetToken,
                'message' => 'You are receiving this email because we received a password reset request for your account.',
            )
        );
        $mailSubject = 'Reset Password - ' . getenv("PROJECT_NAME");
        $templatePath = 'admin.email_template.reset_password';

        $emailJobData = [
            'mailTo' => $mailTo,
            'mailSubject' => $mailSubject,
            'emailData' => $data,
            'templatePath' => $templatePath
        ];

        dispatch(new SendEmailJob($emailJobData))->delay(now()->addSeconds(2));
        return true;
    }
}
