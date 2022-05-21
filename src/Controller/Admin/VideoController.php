<?php

namespace App\Controller\Admin;

use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/videos')]
class VideoController extends AbstractController
{
    public function __construct(private VideoRepository $videoRepository)
    {
    }

    #[Route('/su/create', name: 'admin_videos_create')]
    public function create(): Response
    {
        return $this->render('admin/video/form.html.twig');
    }

    #[Route('/', name: 'admin_videos_list')]
    public function index(): Response
    {
        $videos = [];

        if ($this->isGranted('ROLE_ADMIN')) {
            $videos = $this->videoRepository->findAll();
        } else {
            $videos = $this->getUser()->getLikedVideos();
        }

        return $this->render('admin/video/index.html.twig', [
            'videos' => $videos,
        ]);
    }
}
