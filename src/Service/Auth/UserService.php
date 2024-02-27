<?php

namespace App\Service\Auth;

use App\Entity\Auth\NotificationSettings;
use App\Entity\Auth\User;
use App\Enum\FilterPrefix;
use App\Enum\FilterType;
use App\Repository\Auth\UserRepository;
use App\Service\Image\ImageService;
use Exception;
use League\Flysystem\FilesystemException;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserService
{
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;
    private EmailVerificationHelper $verificationHelper;
    private Security $security;
    private ImageService $imageService;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        EmailVerificationHelper $verificationHelper,
        Security $security,
        ImageService $imageService
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
        $this->verificationHelper = $verificationHelper;
        $this->security = $security;
        $this->imageService = $imageService;
    }

    /**
     * @throws Exception
     */
    private function createNewUser(): User
    {
        $user = new User();
        $user->setUuid(Uuid::v4());
        $user->setEmailVerified(true);
        $user->setSetupDone(false);
        $user->setUrls(["" => ""]);
        $this->verificationHelper->generateAuthCode($user);
        $user->setNotificationSettings(new NotificationSettings());
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
        $user = $this->createNewUser();

        $user->setEmail($email);
        $user->setEmailVerified(true);

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @throws Exception
     */
    public function createUserForGoogleUser(GoogleUser $googleUser): User
    {
        $user = $this->createNewUser();

        $user->setEmail($googleUser->getEmail());
        $user->setEmailVerified(true);
        $user->setUrls([
            FilterType::NONE->value => $googleUser->getAvatar(),
            FilterType::THUMBNAIL->value => $googleUser->getAvatar()
        ]);

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @throws FilesystemException
     */
    public function finishOnboarding(string $displayname, ?string $image): User
    {
        $this->userRepository->beginTransaction();

        try {
            /** @var User $user */
            $user = $this->security->getUser();

            $user->setDisplayname($displayname);
            $user->setSetupDone(true);

            if ($user->getImageName() !== null) {
                $this->imageService->deleteImage($user->getImageName(), $user, FilterPrefix::USER);
            }

            if ($image !== null) {
                $this->imageService->warmupCache($image, $user, FilterPrefix::USER);
            } else {
                $user->setUrls([
                    FilterType::NONE->value => 'https://ui-avatars.com/api/?name=' . $displayname .'&size=256',
                    FilterType::THUMBNAIL->value => 'https://ui-avatars.com/api/?name=' . $displayname .'&size=256'
                ]);
            }

            $this->userRepository->save($user);
            $this->userRepository->commit();

            return $user;
        } catch (Exception $exception) {
            $this->userRepository->rollback();

            throw $exception;
        }
    }

    /**
     * @throws FilesystemException
     */
    public function updateImage(User $preSubmit, User $postSubmit): void
    {
        if ($postSubmit->getImageName() !== $preSubmit->getImageName()) {
            if ($preSubmit->getImageName() !== null) {
                $this->imageService->deleteImage($preSubmit->getImageName(), $preSubmit, FilterPrefix::USER);
            }
            $this->imageService->warmupCache($postSubmit->getImageName(), $postSubmit, FilterPrefix::USER);
        }
    }
}
