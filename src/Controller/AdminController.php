<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Utils\CategoryTreeAdminList;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    #[Route('/', name: 'admin_main_page')]
    public function index(): Response
    {
        return $this->render('admin/my_profile.html.twig');
    }

    #[Route('/categories', name: 'categories', methods: ['GET', 'POST'])]
    public function categories(CategoryTreeAdminList $categories, Request $request): Response
    {
        $categories->getCategoryList($categories->buildTree());

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $isInvalid = null;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryFields = $request->request->all('category');
            $parent = $this->categoryRepository->find($categoryFields['parent']);

            $category->setName($categoryFields['name']);
            $category->setParent($parent);

            $this->categoryRepository->add($category, true);
            return $this->redirectToRoute('categories');

        } else if ($request->isMethod('POST')) {
            $isInvalid = ' is-invalid';
        }

        return $this->render('admin/categories.html.twig', [
            'categories' => $categories->categoryListHtml,
            'form' => $form->createView(),
            'is_invalid' => $isInvalid,
        ]);
    }

    #[Route('/edit-category/{id}', name: 'edit_category')]
    public function editCategory(Category $category): Response
    {
        return $this->render('admin/edit_category.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/delete-category/{id}', name: 'delete_category')]
    public function deleteCategory(Category $category): Response
    {
        $this->categoryRepository->remove($category, true);
        return $this->redirectToRoute('categories');
    }

    #[Route('/videos', name: 'videos')]
    public function videos(): Response
    {
        return $this->render('admin/videos.html.twig');
    }

    #[Route('/upload-video', name: 'upload_video')]
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    #[Route('/users', name: 'users')]
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null): Response
    {
        $categories->getCategoryList($categories->buildTree());
        return $this->render('admin/partials/_all_categories.html.twig', [
            'categories' => $categories->categoryListArray,
            'editedCategory' => $editedCategory,
        ]);
    }
}
