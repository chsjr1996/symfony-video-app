<?php

namespace App\Tests\Controllers\Admin;

use App\Entity\Category;
use App\Tests\TestsHelperTrait;
use App\Tests\WebTestCase;
use Doctrine\ORM\EntityManager;

class CategoriesControllerTest extends WebTestCase
{
    use TestsHelperTrait;

    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loginAsUser();

        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
    }

    public function testTextOnPage(): void
    {
        $crawler = $this->client->request('GET', '/en/admin/su/categories/');

        $this->assertSame('Categories list', $crawler->filter('h2')->text());
        $this->assertStringContainsString('Electronics', $this->client->getResponse()->getContent());
    }

    public function testNewCategory(): void
    {
        $this->client->request('GET', '/en/admin/su/categories/create');
        $this->client->submitForm('Save', [
            'category[parent]' => 1,
            'category[name]' => 'Other Electronics',
        ]);

        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findOneBy(['name' => 'Other Electronics']);

        $this->assertNotNull($category);
        $this->assertSame('Other Electronics', $category->getName());
    }

    public function testEditCategory(): void
    {
        $this->client->request('GET', '/en/admin/su/categories/edit/1');
        $this->client->submitForm('Save', [
            'category[parent]' => 0,
            'category[name]' => 'Electronics 2',
        ]);

        $category = $this->entityManager
            ->getRepository(Category::class)
            ->find(1);

        $this->assertSame('Electronics 2', $category->getName());
    }

    public function testDeleteCategory(): void
    {
        $this->client->request('DELETE', '/en/admin/su/categories/1');
        $category = $this->entityManager
            ->getRepository(Category::class)
            ->find(1);
        $this->assertNull($category);
    }
}
