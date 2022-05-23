<?php

namespace App\Tests\Controllers\Common;

use App\Entity\Subscription;
use App\Entity\Video;
use App\Tests\TestsHelperTrait;
use App\Tests\WebTestCase;
use Doctrine\ORM\EntityManager;

class SubscriptionControllerTest extends WebTestCase
{
    use TestsHelperTrait;

    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @dataProvider getUrlsWithVideo
     */
    public function testLoggedInUserDoesNotSeeTextForNoMembers($url): void
    {
        $this->loginAsUser(false);
        $this->client->request('GET', $url);
        $this->assertStringNotContainsString(
            'Video for <b>MEMBERS</b> only.',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * @dataProvider getUrlsWithVideo
     */
    public function testNotLoggedInUsersSeesTextForNoMembers($url): void
    {
        $this->client->request('GET', $url);
        $this->assertStringContainsString(
            'Video for <b>MEMBERS</b> only.',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * @dataProvider getUrlsWithVideo2
     */
    public function testNotLoggedInUserSeesVideosForNoMembers($url): void
    {
        $this->client->request('GET', $url);
        $expectedVimeoUrl = Video::VIDEO_PATH . Video::VIDEO_FOR_NON_MEMBER;
        $this->assertStringContainsString(
            $expectedVimeoUrl,
            $this->client->getResponse()->getContent()
        );
    }

    public function testExpiredSubscription(): void
    {
        /** @var Subscription */
        $subscription = $this->entityManager
            ->getRepository(Subscription::class)
            ->find(2);

        $invalidDate = (new \DateTime())->modify('-1 day');
        $subscription->setValidTo($invalidDate);

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        $this->client->request('GET', '/video-list/category/movies,4');

        $this->assertStringContainsString(
            'Video for <b>MEMBERS</b> only.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testDeleteSubscription(): void
    {
        $this->loginAsUser(false);
        $crawler = $this->client->request('GET', '/admin/my_profile');
        $link = $crawler->filter('a:contains("cancel plan")')->link();
        $this->client->click($link);

        $this->client->request('GET', '/video-list/category/toys,2');
        $this->assertStringContainsString(
            'Video for <b>MEMBERS</b> only.',
            $this->client->getResponse()->getContent()
        );
    }

    private function getUrlsWithVideo(): \Generator
    {
        yield ['/video-list/category/toys,2/2'];
        yield ['/search-results?query=movies'];
    }

    private function getUrlsWithVideo2(): \Generator
    {
        yield ['/video-list/category/toys,2/2'];
        yield ['/video-list/category/movies,4'];
        yield ['/search-results?query=movies'];
        yield ['/video-details/2#video_comments'];
    }
}
