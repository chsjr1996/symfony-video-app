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
        $localePrefix = '/en';
        $this->client->followRedirects(true);
        $this->client->request('GET', $localePrefix . $url);
        // TODO: Follow redirects is not work here...
        // $this->assertResponseRedirects('/login');
        $this->assertEquals("http://localhost{$localePrefix}/login", $this->client->getRequest()->getUri());
    }

    public function testVideoForMembersOnly()
    {
        $this->client->request('GET', '/en/video-list/category/movies,4');
        $this->assertStringContainsString('Video for <b>MEMBERS</b> only.', $this->client->getResponse()->getContent());
    }

    private function getSecureUrls()
    {
        yield ['/admin/videos'];
        yield ['/admin/users/3'];
        yield ['/admin/su/categories'];
    }
}
