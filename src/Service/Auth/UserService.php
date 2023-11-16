<?php

namespace App\Service\Auth;

use App\Entity\Auth\User;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;
    private EmailVerificationHelper $verificationHelper;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        EmailVerificationHelper $verificationHelper
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
        $this->verificationHelper = $verificationHelper;
    }

    /**
     * @throws Exception
     */
    private function createNewUser(bool $emailVerified = false): User
    {
        $user = new User();
        $user->setEmailVerified($emailVerified);
        $user->setSetupDone(false);
        $this->verificationHelper->generateAuthCode($user);
        return $user;
    }

    /**
     * @throws Exception
     */
    public function createUserFromRequest(Request $request): User
    {
        $data = json_decode($request->getContent());

        $user = $this->createNewUser();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data->password);

        $user
            ->setEmail($data->email)
            ->setPassword($hashedPassword);

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @throws Exception
     */
    public function createUserForEmail(string $email): User
    {
        $user = $this->createNewUser(true);

        $user->setEmail($email);
        $user->setEmailVerified(true);

        $this->userRepository->save($user);

        return $user;
    }
}