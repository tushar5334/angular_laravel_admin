<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sendMailableData;
    public static $sendMailLocal = [

        [
            'email' => 'tushar5334@gmail.com',
            'display_name' => 'Laravel Boilerplate'
        ]
    ];

    public static $localAdminEmail = [
        [
            'email' => 'tushar5334@gmail.com',
            'display_name' => 'Laravel Boilerplate'
        ]
    ];

    public static $developmentSendMail = [
        [
            'email' => 'tushar5334@gmail.com',
            'display_name' => 'Laravel Boilerplate'
        ],
    ];

    public static $developmentAdminEmail = [
        [
            'email' => 'tushar5334@gmail.com',
            'display_name' => 'Laravel Boilerplate'
        ],
    ];

    public static $productionAdminEmail = [
        [
            'email' => 'tushar5334@gmail.com',
            'display_name' => 'Laravel Boilerplate'
        ]
    ];

    public static $developerErrorLogEmail = [
        [
            'email' => 'tushar5334@gmail.com',
            'display_name' => 'Laravel Boilerplate'
        ]
    ];
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $sendMailableData)
    {
        $this->sendMailableData = $sendMailableData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        self::sendEmail($this->sendMailableData['mailSubject'], $this->sendMailableData['templatePath'], $this->sendMailableData['mailTo'], $this->sendMailableData['emailData']);
    }

    public static function sendEmail($subject, $templatePath, $mails = array(), $data = array(), $filePath = '', $attachFileName = '')
    {
        // Development
        if (getenv('APP_ENV') == 'development') {
            $mails['to'] = (isset($mails['to']) && count($mails['to']) > 0) ? self::$developmentSendMail : [];
            $mails['cc'] = (isset($mails['cc']) && count($mails['cc']) > 0) ? self::$developmentSendMail : [];
            $mails['bcc'] = (isset($mails['bcc']) && count($mails['bcc']) > 0) ? self::$developmentSendMail : [];
            $mails['bcc'] = array_merge($mails['bcc'], self::$developmentAdminEmail);

            // Production
        } else if (getenv('APP_ENV') == 'production') {
            if (isset($mails['bcc']) && count($mails['bcc']) > 0) {
                $mails['bcc'] = array_merge($mails['bcc'], self::$productionAdminEmail);
            } else {
                $mails['bcc'] = self::$productionAdminEmail;
            }

            // Local
        } else if (getenv('APP_ENV') != 'production') {
            $mails['to'] = (isset($mails['to']) && count($mails['to']) > 0) ? self::$sendMailLocal : [];
            $mails['cc'] = (isset($mails['cc']) && count($mails['cc']) > 0) ? self::$sendMailLocal : [];
            $mails['bcc'] = (isset($mails['bcc']) && count($mails['bcc']) > 0) ? self::$sendMailLocal : [];
            $mails['bcc'] = array_merge($mails['bcc'], self::$localAdminEmail);
        }

        try {
            Mail::send($templatePath, $data, function ($message) use ($mails, $subject, $filePath, $attachFileName) {
                $mailto = (isset($mails['to']) && count($mails['to']) > 0) ? $mails['to'] : [];
                $mailcc = (isset($mails['cc']) && count($mails['cc']) > 0) ? $mails['cc'] : [];
                $mailbcc = (isset($mails['bcc']) && count($mails['bcc']) > 0) ? $mails['bcc'] : [];
                $message = $message->subject($subject);
                foreach ($mailto as $key => $value) {
                    $display_name = (isset($value['display_name']) && !empty($value['display_name'])) ? $value['display_name'] : getenv('DEFAULT_COMPANY_NAME');
                    $message = $message->to($value['email'], $display_name);
                }
                foreach ($mailcc as $key => $value) {
                    $display_name = (isset($value['display_name']) && !empty($value['display_name'])) ? $value['display_name'] : getenv('DEFAULT_COMPANY_NAME');
                    $message = $message->cc($value['email'], $display_name);
                }

                foreach ($mailbcc as $key => $value) {
                    $display_name = (isset($value['display_name']) && !empty($value['display_name'])) ? $value['display_name'] : getenv('DEFAULT_COMPANY_NAME');
                    $message = $message->bcc($value['email'], $display_name);
                }
                if ($filePath) {
                    $message->attach($filePath, ['as' => $attachFileName]);
                }
            });

            return true;
        } catch (\Exception $e) {

            // $errorMsg = trim($e->getMessage());
            // if($e->getFile()){
            //  $errorMsg.= "<br />ERROR FILE : ".$e->getFile();
            // }
            // if($e->getLine()){
            //  $errorMsg.= "<br />ERROR LINE NO : ".$e->getLine();
            // }

            return false;
        }
    }
}
