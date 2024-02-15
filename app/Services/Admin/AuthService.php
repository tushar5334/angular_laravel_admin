<?php

/**
 * @uses Auth Related Functions
 * @author Tushar Patil <tushar5334@gmail.com>
 * @return
 */

namespace App\Services\Admin;

use App\Libraries\General;
use App\Models\Admin\User;
use Exception;
use Illuminate\Support\Facades\DB;

class AuthService
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService =  $emailService;
    }

    /**
     * @author Tushar Patil <tushar5334@gmail.com>
     *
     * @uses Create user
     *
     * @return bool
     */

    public function createUser(array $request): bool
    {
        try {
            DB::transaction(function () use ($request) {
                $user = User::create($request);
            }, 1);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @author Tushar Patil <tushar5334@gmail.com>
     *
     * @uses Send email notification to user
     *
     * @return bool
     */

    public function forgotPassword(array $request): bool
    {
        $user = User::WHERE('email', $request['email'])->first();
        if (!$user) {
            return false;
        }

        $autoResetToken = General::generate_token();
        $user->token = $autoResetToken;
        $user->save();

        $this->emailService->sendForgotPasswordEmailNotification($user, $autoResetToken);

        return true;
    }
}
