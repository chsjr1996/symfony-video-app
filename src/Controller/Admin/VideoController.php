<?php

namespace App\Controller\Admin;

use App\Service\VideoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class VideoController extends AbstractController
{
    public function __construct(private VideoService $videoService)
    {
    }

    #[Route('/su/videos/create', name: 'admin_videos_create')]
    public function create(): Response
    {
        return $this->render('admin/video/form.html.twig');
    }

    #[Route('/videos', name: 'admin_videos_list')]
    public function index(): Response
    {
        $videos = $this->isGranted('ROLE_ADMIN')
            ? $this->videoService->all()
            : $this->videoService->allLiked($this->getUser());

        return $this->render('admin/video/index.html.twig', [
            'videos' => $videos,
        ]);
    }
}
