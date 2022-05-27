<?php

namespace App\Controller\Front;

use App\Service\Implementations\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    public function __construct(private CategoryService $categoryService)
    {
    }

    #[Route('/', name: 'front_main_page', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    public function mainCategories(): Response
    {
        $categories = $this->categoryService->listMainCategories();
        return $this->render('front/_partials/_main_categories.html.twig', compact('categories'));
    }
}
