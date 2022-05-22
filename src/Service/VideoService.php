<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use App\Repository\CommentRepository;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use Doctrine\Common\Collections\Collection;

class VideoService
{
    public function __construct(
        private VideoRepository $videoRepository,
        private CommentRepository $commentRepository,
    ) {
    }

    /**
     * @return Video[]
     */
    public function all(): array
    {
        return $this->videoRepository->findAll();
    }

    /**
     * @return Collection<int, Video>
     */
    public function allLiked(User $user): Collection
    {
        return $user->getLikedVideos();
    }

    public function showVideoDetails(int $id): Video
    {
        return $this->videoRepository->videoDetails($id);
    }

    public function findByTitle(string $title, int $page, string $sortBy)
    {
        if (!$title) {
            return null;
        }

        $videos = $this->videoRepository->findByTitle($title, $page, $sortBy);

        if (!$videos->getItems()) {
            $videos = null;
        }

        return $videos;
    }

    public function findByChildIds(
        CategoryTreeFrontPage $categories,
        int $id,
        int $page,
        string $sortBy
    ) {
        $categories->getCategoryListAndParent($id);
        $categoryIds = $categories->getChildIds($id);
        array_push($categoryIds, (int) $id);

        return $this->videoRepository->findByChildIds($categoryIds, $page, $sortBy);
    }

    public function addComment(string $content, Video $video, User $user): void
    {
        if (!empty(trim($content))) {
            $comment = new Comment();
            $comment->setContent($content);
            $comment->setOwner($user);
            $comment->setVideo($video);
            $this->commentRepository->add($comment, true);
        }
    }
}
