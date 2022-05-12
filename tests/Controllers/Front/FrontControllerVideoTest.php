<?php

namespace App\Tests\Controllers\Front;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerVideoTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
    }

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
}
