<?php

namespace App\Tests\Controllers\Admin;

use App\Tests\TestsHelperTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerSecurityTest extends WebTestCase
{
    use TestsHelperTrait;

    private KernelBrowser $client;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->container = static::getContainer();
    }

    /**
     * @dataProvider getUrlsForRegularUsers
     */
    public function testAccessDeniedForRegularUsers(string $httpMethod, string $url): void
    {
        $this->loginAsUser($this->container, $this->client, false);
        $this->client->request($httpMethod, $url);
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminSu()
    {
        $this->loginAsUser($this->container, $this->client);
        $crawler = $this->client->request('GET', '/admin/su/categories');
        $this->assertSame('Categories list', $crawler->filter('h2')->text());
    }

    private function getUrlsForRegularUsers()
    {
        yield ['GET', '/admin/su/categories'];
        yield ['GET', '/admin/su/edit-category/1'];
        yield ['GET', '/admin/su/delete-category/1'];
        yield ['GET', '/admin/su/users'];
        yield ['GET', '/admin/su/upload-video'];
    }
}
