<?php

namespace App\Controller\Front;

use App\Entity\Comment;
use App\Entity\Video;
use App\Service\Implementations\CategoryService;
use App\Service\Implementations\CategoryTreeFrontPage;
use App\Service\Implementations\VideoAuthService;
use App\Service\Implementations\VideoService;
use App\Service\Interfaces\CacheInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    public function __construct(
        private VideoService $videoService,
        private VideoAuthService $videoAuthService,
        private CategoryService $categoryService,
        private CacheInterface $cache
    ) {
    }

    #[Route('/search-results/{page}', name: 'search_results', methods: ['GET'], defaults: ['page' => "1"])]
    public function searchResults(Request $request, $page): Response
    {
        $videos = $this->videoService->findByTitle($request->get('query', ''), $page, $request->get('sortby', ''));

        return $this->render('front/search_results.html.twig', [
            'videos' => $videos,
            'query' => $request->get('query'),
            'video_non_members' => $this->videoAuthService->checkSubscription(),
        ]);
    }

    #[Route('/video-list/category/{categoryname},{id}/{page}', name: 'video_list', defaults: ['page' => '1'])]
    public function videoList(Request $request, $id, $page): Response
    {
        $sortBy = $request->get('sortby', '');
        $cachedView = $this->cache->getItem('video_list' . $id . $page . $sortBy);
        $cachedView->expiresAfter(60);

        if (!$cachedView->isHit()) {
            $categories = $this->categoryService->getCategoryTreeIds($id);
            $categoryIds = $categories->currentCategoryTreeIds;
            $videos = $this->videoService->findByChildIds($categoryIds, $page, $sortBy);

            $renderedView = $this->render('front/videolist.html.twig', [
                'subcategories' => $categories,
                'videos' => $videos,
                'video_non_members' => $this->videoAuthService->checkSubscription(),
            ]);

            $cachedView->set($renderedView);
            $this->cache->save($cachedView);
        }

        return $cachedView->get();
    }

    #[Route('/video-details/{id}', name: 'video_details')]
    public function videoDetails($id): Response
    {
        return $this->render('front/video_details.html.twig', [
            'video' => $this->videoService->showVideoDetails($id),
            'video_non_members' => $this->videoAuthService->checkSubscription(),
        ]);
    }

    #[Route('/new-comment/{video}', name: 'new_comment', methods: ['POST'])]
    public function newComment(Request $request, Video $video): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $content = $request->request->get('comment');
        $this->videoService->addComment($content, $video, $this->getUser());

        return $this->redirectToRoute('video_details', [
            'id' => $video->getId(),
        ]);
    }

    #[Route('/delete-comment/{comment}', name: 'delete_comment', methods: ['DELETE', 'GET'])]
    #[Security('user.getId() == comment.getOwner().getId()')]
    public function deleteComment(Request $request, Comment $comment): Response
    {
        $success = $this->videoService->removeComment($comment);
        $flashType = $success ? 'success' : 'danger';
        $flashMessage = $success ? 'Your comment was deleted!' : 'Error on delete your comment, try again later!';
        $this->addFlash($flashType, $flashMessage);

        return $this->redirect($request->headers->get('referer'));
    }
}
