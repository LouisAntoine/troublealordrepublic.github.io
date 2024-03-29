<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\UserStatusEnum;
use Exception;
use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\MailerService;

final class UserService
{
    private const DEFAULT_STATUS = UserStatusEnum::USER_STATUS_PENDING;
    private const DEFAULT_ROLES = ['ROLE_USER'];

    public function __construct(
        private User $user,
        private UserPasswordHasherInterface $passwordHasher,
        private MailerService $mailerService
    )
    {
        $this->mailerService = $mailerService;
    }

    public function createUser(): User
    {
        $this->setHashedPassword();
        $this->setDefaultStatus();
        $this->setDefaultRoles();
        $this->setValidationToken();

        $this->mailerService->sendEmail(
            [
                'emailTo' => $this->user->getEmail(),
                'lastnameTo' => $this->user->getLastname(),
                'firstnameTo' => $this->user->getFirstname(),
                'validationToken' => $this->user->getValidationToken(),
            ],
            MailerService::VERIFY_ACCOUNT_TEMPLATE_ID
        );

        return $this->user;
    }

    public function updateUser(bool $isUpdatingEmail): User
    {
        if($this->isUpdatingPassword()) {
            $this->setHashedPassword();
        }
        if ($isUpdatingEmail) {
            $this->setDefaultStatus();
            $this->setValidationToken();
        }

        return $this->user;
    }

    public function validateAccount(): User
    {
        $this->user->setStatus(UserStatusEnum::USER_STATUS_ACTIVE);
        $this->user->setValidationToken(null);

        return $this->user;
    }

    private function setHashedPassword(): void
    {
        $this->user->setPassword(
            $this->passwordHasher->hashPassword(
                $this->user,
                $this->user->getPlainPassword()
            ));
        $this->user->eraseCredentials();
    }

    private function setDefaultStatus(): void
    {
        $this->user->setStatus(self::DEFAULT_STATUS);
    }

    private function setDefaultRoles(): void
    {
        $this->user->setRoles(self::DEFAULT_ROLES);
    }

    private function setValidationToken(): void
    {
        try {
            $this->user->setValidationToken(bin2hex(random_bytes(32)));
        } catch (Exception $e) {
            throw new InvalidArgumentException('Une erreur est survenue lors de la génération du token.');
        }
    }

    private function isUpdatingPassword(): bool
    {
        return $this->user->getPlainPassword() !== null && $this->user->getVerifyPassword() !== null;
    }
}