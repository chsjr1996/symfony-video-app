<?php

namespace App\Controller\Front;

use App\Entity\Video;
use App\Service\Implementations\CategoryTreeFrontPage;
use App\Service\Implementations\VideoAuthService;
use App\Service\Implementations\VideoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    public function __construct(
        private VideoService $videoService,
        private VideoAuthService $videoAuthService,
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
    public function videoList(Request $request, $id, $page, CategoryTreeFrontPage $categories): Response
    {
        $videos = $this->videoService->findByChildIds($categories, $id, $page, $request->get('sortby', ''));

        return $this->render('front/videolist.html.twig', [
            'subcategories' => $categories,
            'videos' => $videos,
            'video_non_members' => $this->videoAuthService->checkSubscription(),
        ]);
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
}
