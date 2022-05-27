<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use App\Form\VideoType;
use App\Service\Implementations\VideoService;
use App\Service\Interfaces\VideoUploaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class VideoController extends AbstractController
{
    public function __construct(private VideoService $videoService)
    {
    }

    #[Route('/su/videos/upload', name: 'admin_videos_upload')]
    public function upload(Request $request): Response
    {
        return $this->render('admin/video/form.html.twig');
    }

    #[Route('/su/videos/upload-locally', name: 'admin_videos_upload_locally')]
    public function uploadLocally(Request $request, VideoUploaderInterface $videoUploader): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);

        if ($this->videoService->saveLocally($request, $video, $form, $videoUploader)) {
            return $this->redirectToRoute('admin_videos_list');
        }

        return $this->render('admin/video/form_locally.html.twig', [
            'form' => $form->createView(),
        ]);
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

    #[Route('/{video}', name: 'delete_locally', methods: ['DELETE'])]
    public function deleteLocally(Video $video, VideoUploaderInterface $videoUploader): Response
    {
        $success = $this->videoService->removeLocally($video, $videoUploader);
        $flashType = $success ? 'success' : 'danger';
        $flashMessage = $success
            ? 'The video was successfully deleted!'
            : 'We were not to able to delete. Check the video.';

        $this->addFlash($flashType, $flashMessage);

        return $this->redirectToRoute('admin_videos_list');
    }
}
