<?php

namespace App\Tests\Controllers\Front;

use App\Tests\TestsHelperTrait;
use App\Tests\WebTestCase;

class VideoControllerTest extends WebTestCase
{
    use TestsHelperTrait;

    public function testNoResults(): void
    {
        $this->client->followRedirects();
        $this->client->request('GET', '/');
        $crawler = $this->client->submitForm('Search video', [
            'query' => 'aaa',
        ], 'GET');

        $this->assertStringContainsString('No results were found', $crawler->filter('h1')->text());
    }

    public function testResultsFound(): void
    {
        $this->client->followRedirects();
        $this->client->request('GET', '/');
        $crawler = $this->client->submitForm('Search video', [
            'query' => 'movies',
        ], 'GET');

        $this->assertGreaterThan(4, $crawler->filter('h3')->count());
    }

    public function testSorting(): void
    {
        $this->client->followRedirects();
        $this->client->request('GET', '/');
        $crawler = $this->client->submitForm('Search video', [
            'query' => 'movies',
        ], 'GET');

        $form = $crawler->filter('#form-sorting')->form([
            'sortby' => 'DESC',
        ]);
        $crawler = $this->client->submit($form);

        $this->assertEquals('Movies 9', $crawler->filter('h3')->first()->text());
    }

    public function testNotLoggedInUser(): void
    {
        $this->client->followRedirects();
        $this->client->request('GET', '/video-details/16');
        $this->client->submitForm('Add', [
            'comment' => 'Hello!',
        ]);
        $this->assertStringContainsString('Please sign in', $this->client->getResponse()->getContent());
    }

    public function testNewCommentAndNumberOfComments(): void
    {
        $newComment = "Nice video!!!";
        $videoId = 16;

        $this->loginAsUser(false);
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/video-list/category/toys,2');
        $commentsQty = $crawler->filter("a#video_{$videoId}_CommentsQty")->text();
        $this->assertSame('Comments (0)', $commentsQty);

        $this->client->request('GET', '/video-details/' . $videoId);
        $this->client->submitForm('Add', [
            'comment' => $newComment,
        ]);
        $this->assertStringContainsString($newComment, $this->client->getResponse()->getContent());

        $crawler = $this->client->request('GET', '/video-list/category/toys,2');
        $commentsQty = $crawler->filter("a#video_{$videoId}_CommentsQty")->text();
        $this->assertSame('Comments (1)', $commentsQty);
    }
}
