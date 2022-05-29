<?php

namespace App\Tests\Controllers\Front;

use App\Tests\TestsHelperTrait;
use App\Tests\WebTestCase;

class LikesControllerTest extends WebTestCase
{
    use TestsHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAsUser(false);
        $this->clearCache();
    }

    public function testLike(): void
    {
        $videoIdToTest = 11;
        $videoCategoryToTest = 'movies,4';
        $this->client->request('POST', "/en/likes/{$videoIdToTest}/like");
        $this->clearCache();
        $crawler = $this->client->request('GET', "/en/video-list/category/{$videoCategoryToTest}");
        $this->assertSame('3', $crawler->filter("small.number-of-likes-{$videoIdToTest}")->text());
    }

    public function testDislike(): void
    {
        $videoIdToTest = 11;
        $videoCategoryToTest = 'movies,4';
        $this->client->request('POST', "/en/likes/{$videoIdToTest}/dislike");
        $this->clearCache();
        $crawler = $this->client->request('GET', "/en/video-list/category/{$videoCategoryToTest}");
        $this->assertSame('1', $crawler->filter("small.number-of-dislikes-{$videoIdToTest}")->text());
    }

    public function testNumberOfLikedVideos1(): void
    {
        $crawler = $this->client->request('GET', '/en/admin/videos');
        $this->assertEquals(2, $crawler->filter('tr')->count());

        $this->client->request('POST', "/en/likes/11/like");
        $crawler = $this->client->request('GET', '/en/admin/videos');
        $this->assertEquals(3, $crawler->filter('tr')->count());
    }

    public function testNumberOfLikedVideos2(): void
    {
        $crawler = $this->client->request('GET', '/en/admin/videos');
        $this->assertEquals(2, $crawler->filter('tr')->count());

        $this->client->request('POST', '/en/likes/12/undo_like');
        $crawler = $this->client->request('GET', '/en/admin/videos');
        $this->assertEquals(1, $crawler->filter('tr')->count());
    }
}
