<?php

namespace App\Tests\Controllers\Admin;

use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerCategoriesTest extends WebTestCase
{
    private KernelBrowser $client;

    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $container = static::getContainer();
        $this->client->disableReboot();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
    }

    public function testTextOnPage(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');

        $this->assertSame('Categories list', $crawler->filter('h2')->text());
        $this->assertStringContainsString('Electronics', $this->client->getResponse()->getContent());
    }

    public function testNumberOfItems(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');
        $this->assertCount(21, $crawler->filter('option'));
    }

    public function testNewCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');

        $form =  $crawler->selectButton('Add')->form([
            'category[parent]' => 1,
            'category[name]' => 'Other Electronics',
        ]);

        $this->client->submit($form);

        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findOneBy(['name' => 'Other Electronics']);

        $this->assertNotNull($category);
        $this->assertSame('Other Electronics', $category->getName());
    }

    public function testEditCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/edit-category/1');
        $form = $crawler->selectButton('Save')->form([
            'category[parent]' => 0,
            'category[name]' => 'Electronics 2',
        ]);
        $this->client->submit($form);

        $category = $this->entityManager
            ->getRepository(Category::class)
            ->find(1);

        $this->assertSame('Electronics 2', $category->getName());
    }

    public function testDeleteCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/delete-category/1');
        $category = $this->entityManager
            ->getRepository(Category::class)
            ->find(1);
        $this->assertNull($category);
    }
}
