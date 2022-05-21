<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * @return User[]
     */
    public function all(): array
    {
        return $this->userRepository->findAll();
    }
}
