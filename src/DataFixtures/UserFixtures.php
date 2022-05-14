<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private const DEFAULT_PASSWORD = 'password123';

    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$name, $lastName, $email, $apiKey, $roles]) {
            $user = new User();
            $user->setName($name);
            $user->setLastName($lastName);
            $user->setEmail($email);
            $user->setPassword($this->passwordEncoder->hashPassword($user, self::DEFAULT_PASSWORD));
            $user->setVideoApiKey($apiKey);
            $user->setRoles($roles);
            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            ['Anakin', 'Skywalker', 'vader@darth.com', 'jd8dehdh', ['ROLE_ADMIN']],
            ['Leia', 'Organa', 'leia@commander.com', null, ['ROLE_ADMIN']],
            ['Luke', 'Skywalker', 'luke@jedi.com', null, ['ROLE_USER']],
        ];
    }
}
