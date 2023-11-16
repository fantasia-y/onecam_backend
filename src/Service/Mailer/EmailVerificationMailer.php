<?php

namespace App\Service\Mailer;

use App\Entity\Auth\User;

class EmailVerificationMailer extends BaseMailer
{
    public function sendVerificationEmail(User $user): void
    {
        $email = $this->createMail(
            [$user->getEmail()],
            $user->getAuthCode() . ' is your verification code',
            'email/verification/verification',
            [
                'code' => $user->getAuthCode()
            ]
        );
        $this->send($email);
    }
}