<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->commentData() as [$content, $userId, $videoId, $createdAt]) {
            $user = $manager->getRepository(User::class)->find($userId);
            $video = $manager->getRepository(Video::class)->find($videoId);

            $comment = new Comment();
            $comment->setContent($content);
            $comment->setOwner($user);
            $comment->setVideo($video);
            $comment->setCreatedAtForFixtures(new \DateTimeImmutable($createdAt));

            $manager->persist($comment);
        }

        $manager->flush();
    }

    private function commentData(): array
    {
        return [
            ['I like this video because...', 1, 10, '2022-05-15 10:19:00'],
            ['I doesn\'t like this video because...', 2, 10, '2022-05-15 10:19:00'],
            ['Crazy!!!', 3, 10, '2022-05-15 10:19:00'],
            ['hahahaha!', 1, 13, '2022-05-15 10:19:00'],
            ['Very cool!!!', 2, 14, '2022-05-15 10:19:00'],
            ['Incredible :)', 3, 15, '2022-05-15 10:19:00'],
        ];
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            VideoFixtures::class,
        ];
    }
}
