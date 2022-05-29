<?php

namespace App\Service\Implementations;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use App\Repository\CommentRepository;
use App\Repository\VideoRepository;
use App\Service\Interfaces\VideoUploaderInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class VideoService
{
    public function __construct(
        private VideoRepository $videoRepository,
        private CommentRepository $commentRepository,
    ) {
    }

    /**
     * 
     */
    public function saveLocally(
        Request $request,
        Video $video,
        FormInterface $form,
        VideoUploaderInterface $videoUploader
    ): bool {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $video->getUploadedVideo();
            $uploadedVideo = $videoUploader->upload($file);
            $fileName = $uploadedVideo->getFileName();
            $fileOriginalName = $uploadedVideo->getOriginalFileName();

            $video->setPath(Video::UPLOAD_FOLDER . '/' . $fileName);
            $video->setTitle($fileOriginalName);

            $this->videoRepository->add($video, true);

            return true;
        }

        return false;
    }

    public function removeLocally(Video $video, VideoUploaderInterface $videoUploader): bool
    {
        try {
            $videoPath = $video->getPath();

            if (!strpos($videoPath, 'uploads/videos')) {
                throw new \Exception('This is not local video...');
            }

            $this->videoRepository->remove($video, true);

            if (!$videoUploader->delete($videoPath)) {
                throw new \Exception('Video cannot be removed from file system.');
            }

            return true;
        } catch (\Exception $ex) {
            // TODO: Log
            return false;
        }
    }

    public function updateCategory(Video $video, Category $category): bool
    {
        try {
            if (is_null($category)) {
                throw new \Exception('Category cannot be null in this action!');
            }

            $video->setCategory($category);
            $this->videoRepository->add($video, true);

            return true;
        } catch (\Exception $ex) {
            // TODO: Log
            return false;
        }
    }

    /**
     * @return Video[]
     */
    public function all(): array
    {
        return $this->videoRepository->findBy([], ['title' => 'ASC']);
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
        array $categoryIds,
        int $page,
        string $sortBy
    ) {

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

    public function removeComment(Comment $comment): bool
    {
        try {
            $this->commentRepository->remove($comment, true);
            return true;
        } catch (\Exception $ex) {
            // TODO: Log
            return false;
        }
    }
}
