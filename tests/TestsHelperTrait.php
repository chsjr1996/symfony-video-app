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

    public function loginAsUser(bool $isAdmin = true): void
    {
        $this->validateAttribute('client', KernelBrowser::class);
        $this->validateAttribute('container', ContainerInterface::class);

        /** @var UserRepository */
        $userRepository =  $this->container->get(UserRepository::class);
        $userId = $isAdmin ? 1 : 3;
        $this->client->loginUser($userRepository->find($userId));
    }

    private function validateAttribute(string $attributeName, string $instanceOfClassName)
    {
        if (!$this->$attributeName || !($this->$attributeName instanceof $instanceOfClassName) ) {
            throw new \Exception("{$attributeName} attribute is invalid!");
        }
    }
}
