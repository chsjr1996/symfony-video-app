<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Video;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @todo Use service to manage resources (more clean controller)
 */
#[Route('likes')]
class LikesController extends AbstractController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route('/{video}/{action}', name: 'like_control', methods: ['POST'])]
    public function toggleLike(Video $video, string $action): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $actions = [
            'like' => 'addLike',
            'undo_like' => 'undoLike',
            'dislike' => 'addDislike',
            'undo_dislike' => 'undoDislike',
        ];

        if (!in_array($action, array_keys($actions))) {
            return $this->json(['action' => 'error', 'msg' => 'action not found'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        $actionMethod = $actions[$action];
        $result = $this->$actionMethod($video, $user);

        return $this->json(['action' => $result, 'id' => $video->getId()]);
    }

    private function addLike(Video $video, User $user): string
    {
        $user->addLikedVideo($video);
        $this->userRepository->add($user, true);
        return 'liked';
    }

    private function addDislike(Video $video, User $user): string
    {
        $user->addDislikedVideo($video);
        $this->userRepository->add($user, true);
        return 'disliked';
    }

    private function undoLike(Video $video, User $user): string
    {
        $user->removeLikedVideo($video);
        $this->userRepository->add($user, true);
        return 'undo liked';
    }

    private function undoDislike(Video $video, User $user): string
    {
        $user->removeDislikedVideo($video);
        $this->userRepository->add($user, true);
        return 'undo disliked';
    }
}
