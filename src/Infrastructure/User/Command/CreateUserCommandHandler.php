<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Command;

use App\Application\Shared\Command\CommandHandlerInterface;
use App\Domain\User\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class CreateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        if(!$command->getUser()) return;

        $user = new User();
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
        $user->setActivationKey($this->generateHash(new DateTime(), $user->getEmail()));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function generateHash(DateTime $date, string $email): string
    {
        return "TTJ" . $date->format('Ymd') . strtoupper(md5($date->format('Y-m-d H:i:s') . $email));
    }
}