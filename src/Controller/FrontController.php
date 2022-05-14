<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @todo Refactor: isolate Front modules in specific files
 */
class FrontController extends AbstractController
{
    #[Route('/', name: 'main_page')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    #[Route('/search-results/{page}', name: 'search_results', methods: ['GET'], defaults: ['page' => "1"])]
    public function searchResults(Request $request, $page, VideoRepository $videoRepository): Response
    {
        $videos = null;

        if ($query = $request->get('query')) {
            $videos = $videoRepository->findByTitle($query, $page, $request->get('sortby'));

            if (!$videos->getItems()) {
                $videos = null;
            }
        }

        return $this->render('front/search_results.html.twig', [
            'videos' => $videos,
            'query' => $query,
        ]);
    }

    #[Route('/video-list/category/{categoryname},{id}/{page}', name: 'video_list', defaults: ['page' => '1'])]
    public function videoList(
        $id,
        $page,
        Request $request,
        VideoRepository $videoRepository,
        CategoryTreeFrontPage $categories
    ): Response {
        $categories->getCategoryListAndParent($id);
        $categoryIds = $categories->getChildIds($id);
        array_push($categoryIds, (int) $id);

        $videos = $videoRepository->findByChildIds(
            $categoryIds,
            (int) $page,
            $request->get('sortby')
        );

        return $this->render('front/videolist.html.twig', [
            'subcategories' => $categories,
            'videos' => $videos,
        ]);
    }

    #[Route('/video-details', name: 'video_details')]
    public function videoDetails(): Response
    {
        return $this->render('front/video_details.html.twig');
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig');
    }

    #[Route('/register', name: 'register')]
    public function register(): Response
    {
        return $this->render('front/register.html.twig');
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('front/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // Symfony's managed route
        throw new \Exception('This should never be reacher!');
    }

    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
        return $this->render('front/payment.html.twig');
    }

    public function mainCategories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy(
            ['parent' => null],
            ['name' => 'ASC']
        );

        return $this->render('front/partials/_main_categories.html.twig', compact('categories'));
    }
}
