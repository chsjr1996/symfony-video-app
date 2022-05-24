<?php

namespace App\Tests\Controllers\Admin;

use App\Entity\User;
use App\Tests\TestsHelperTrait;
use App\Tests\WebTestCase;
use Doctrine\ORM\EntityManager;

class UserControllerTest extends WebTestCase
{
    use TestsHelperTrait;

    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
    }

    public function testUserDeleteAccount(): void
    {
        $this->loginAsUser(false);
        $this->client->request('GET', '/admin/my_profile');
        $this->client->submitForm('delete account');

        $user = $this->entityManager->getRepository(User::class)->find(3);
        $this->assertNull($user);
    }

    public function testUserChangeName(): void
    {
        $this->loginAsUser(false);
        $this->client->request('GET', '/admin/my_profile');
        $this->client->submitForm('Save', [
            'user[name]' => 'name',
            'user[last_name]' => 'last_name',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
        ]);

        /** @var User */
        $user = $this->entityManager->getRepository(User::class)->find(3);
        $this->assertSame('name', $user->getName());
    }

    public function testAdminDeleteAnUserAccount(): void
    {
        $userIdToDelete = 4;
        $this->loginAsUser();
        $this->client->request('DELETE', '/admin/users/' . $userIdToDelete);

        $user = $this->entityManager->getRepository(User::class)->find($userIdToDelete);
        $this->assertNull($user);
    }
}
