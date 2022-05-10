<?php

namespace App\Tests;

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
}
