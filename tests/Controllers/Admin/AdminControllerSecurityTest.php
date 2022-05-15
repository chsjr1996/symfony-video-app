<?php

namespace App\Tests\Controllers\Admin;

use App\Tests\TestsHelperTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerSecurityTest extends WebTestCase
{
    use TestsHelperTrait;

    /**
     * @dataProvider getUrlsForRegularUsers
     */
    public function testAccessDeniedForRegularUsers(string $httpMethod, string $url): void
    {
        $this->loginAsUser(false);
        $this->client->request($httpMethod, $url);
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminSu()
    {
        $this->loginAsUser();
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
