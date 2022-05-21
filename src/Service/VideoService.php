<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Video;
use App\Repository\VideoRepository;
use Doctrine\Common\Collections\Collection;

class VideoService
{
    public function __construct(private VideoRepository $videoRepository)
    {
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
}
