<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait TestsHelperTrait
{
    public function setProtectedProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new \ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
        $reflectionProperty->setAccessible(false);
    }

    public function loginAsAdmin(ContainerInterface $container, KernelBrowser $client)
    {
        /** @var UserRepository */
        $userRepository =  $container->get(UserRepository::class);
        $user = $userRepository->find(1);
        $client->loginUser($user);
    }
}
