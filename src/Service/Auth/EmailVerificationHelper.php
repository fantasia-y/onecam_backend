<?php

namespace App\Service\Auth;

use App\Entity\Auth\User;
use App\Repository\UserRepository;
use App\Service\Mailer\EmailVerificationMailer;
use Exception;

class EmailVerificationHelper
{
    private EmailVerificationMailer $mailer;
    private UserRepository $userRepository;

    public function __construct(EmailVerificationMailer $mailer, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws Exception
     */
    private function generateCode(): int
    {
        $min = 10 ** (6 - 1);
        $max = 10 ** 6 - 1;
        return random_int($min, $max);
    }

    /**
     * @throws Exception
     */
    public function generateAuthCode(User $user): void
    {
        $code = $this->generateCode();
        $user->setAuthCode($code);
    }

    public function sendAuthCode(User $user): void
    {
        if ($user->getAuthCode() !== null) {
            $this->mailer->sendVerificationEmail($user);
        }
    }

    public function verifyAuthCode(User $user, string $code): bool
    {
        if (strcmp($user->getAuthCode(), $code) === 0) {
            $user->setEmailVerified(true);
            $user->setAuthCode(null);
            $this->userRepository->save($user);
            return true;
        }
        return false;
    }
}