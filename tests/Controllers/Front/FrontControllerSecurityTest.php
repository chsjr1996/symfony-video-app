<?php

namespace App\Tests\Controllers\Front;

use App\Tests\WebTestCase;

class FrontControllerSecurityTest extends WebTestCase
{
    /**
     * @dataProvider getSecureUrls
     */
    public function testSecureUrls(string $url): void
    {
        $this->client->followRedirects(true);
        $this->client->request('GET', $url);
        // TODO: Follow redirects is not work here...
        // $this->assertResponseRedirects('/login');
        $this->assertEquals('http://localhost/login', $this->client->getRequest()->getUri());
    }

    public function testVideoForMembersOnly()
    {
        $this->client->request('GET', '/video-list/category/movies,4');
        $this->assertStringContainsString('Video for <b>MEMBERS</b> only.', $this->client->getResponse()->getContent());
    }

    private function getSecureUrls()
    {
        yield ['/admin/videos'];
        yield ['/admin'];
        yield ['/admin/su/categories'];
        yield ['/admin/su/delete-category/1'];
    }
}
