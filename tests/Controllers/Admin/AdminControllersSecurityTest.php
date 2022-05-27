<?php

namespace App\Tests\Controllers\Admin;

use App\Tests\TestsHelperTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminControllersSecurityTest extends WebTestCase
{
    use TestsHelperTrait;

    /**
     * @dataProvider getUrlsForRegularUsers
     */
    public function testAccessDeniedForRegularUsers(string $httpMethod, string $url): void
    {
        $this->expectException(AccessDeniedException::class);
        $this->loginAsUser(false);
        $this->client->catchExceptions(false);
        $this->client->request($httpMethod, $url);
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminSu()
    {
        $this->loginAsUser();
        $crawler = $this->client->request('GET', '/admin/su/categories/');
        $this->assertSame('Categories list', $crawler->filter('h2')->text());
    }

    private function getUrlsForRegularUsers()
    {
        yield ['GET', '/admin/su/categories/'];
        yield ['GET', '/admin/su/categories/edit/1'];
        yield ['DELETE', '/admin/su/categories/1'];
        yield ['GET', '/admin/su/users'];
        yield ['GET', '/admin/su/videos/upload'];
        yield ['GET', '/admin/su/videos/upload-locally'];
    }
}
