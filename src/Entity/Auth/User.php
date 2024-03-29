<?php

namespace App\Entity\Auth;

use App\Interfaces\ImageStorage;
use App\Repository\Auth\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table('users')]
class User extends ImageStorage implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['Private'])]
    private ?string $email = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $displayname = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['Private'])]
    private ?bool $emailVerified = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['Private'])]
    private ?bool $setupDone = null;

    #[ORM\Column(length: 6, nullable: true)]
    #[Exclude]
    private ?string $authCode = null;

    #[ORM\Column]
    #[Exclude]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    #[Exclude]
    private ?string $password = null;

    #[ORM\OneToOne(targetEntity: NotificationSettings::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'notification_settings_id', referencedColumnName: 'id')]
    #[Groups(['Private'])]
    private ?NotificationSettings $notificationSettings = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(?Uuid $uuid): User
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDisplayname(): ?string
    {
        return $this->displayname;
    }

    public function setDisplayname(?string $displayname): void
    {
        $this->displayname = $displayname;
    }

    public function getEmailVerified(): ?bool
    {
        return $this->emailVerified;
    }

    public function setEmailVerified(?bool $emailVerified): void
    {
        $this->emailVerified = $emailVerified;
    }

    public function getSetupDone(): ?bool
    {
        return $this->setupDone;
    }

    public function setSetupDone(?bool $setupDone): void
    {
        $this->setupDone = $setupDone;
    }

    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }

    public function setAuthCode(?string $authCode): void
    {
        $this->authCode = $authCode;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getNotificationSettings(): ?NotificationSettings
    {
        return $this->notificationSettings;
    }

    public function setNotificationSettings(?NotificationSettings $notificationSettings): User
    {
        $this->notificationSettings = $notificationSettings;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {}
}
