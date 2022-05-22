<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\CategoryService;
use App\Utils\CategoryTreeAdminList;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/su/categories')]
class CategoryController extends AbstractController
{
    public function __construct(private CategoryService $categoryService)
    {
    }

    #[Route('/create', name: 'admin_categories_create', methods: ['GET'])]
    public function create(Request $request): Response
    {
        $formView = $this->createForm(CategoryType::class, null, [
            'action' => $this->generateUrl('admin_categories_store'),
        ])->createView();

        return $this->render('admin/category/form.html.twig', [
            'form' => $formView,
            'is_invalid' => ' ' . $request->get('isInvalid', ''),
        ]);
    }

    #[Route('/store', name: 'admin_categories_store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        if ($this->categoryService->save($request, $category, $form)) {
            return $this->redirectToRoute('admin_categories_list');
        }

        return $this->redirectToRoute('admin_categories_create', [
            'isInvalid' => 'is-invalid',
        ]);
    }

    #[Route('/', name: 'admin_categories_list', methods: ['GET'])]
    public function index(Request $request, CategoryTreeAdminList $categories): Response
    {
        $categories->getCategoryList($categories->buildTree());

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories->categoryListHtml,
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_categories_edit', methods: ['GET'])]
    public function edit(Request $request, Category $category): Response
    {
        $formView = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('admin_categories_update', ['id' => $category->getId()]),
            'method' => 'PUT'
        ])->createView();

        return $this->render('admin/category/form.html.twig', [
            'category' => $category,
            'form' => $formView,
            'is_invalid' => ' ' . $request->get('isInvalid', ''),
        ]);
    }

    #[Route('/update/{id}', name: 'admin_categories_update', methods: ['PUT'])]
    public function update(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category, [
            'method' => 'PUT',
        ]);

        if ($this->categoryService->save($request, $category, $form)) {
            return $this->redirectToRoute('admin_categories_list');
        }

        return $this->redirectToRoute('admin_categories_edit', [
            'id' => $category->getId(),
            'isInvalid' => 'is-invalid',
        ]);
    }

    #[Route('/{id}', name: 'admin_categories_delete', methods: ['DELETE'])]
    public function delete(Category $category): Response
    {
        $this->categoryService->remove($category);
        return $this->redirectToRoute('admin_categories_list');
    }

    public function renderCategoriesList(CategoryTreeAdminOptionList $categories, $editedCategory = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categories->getCategoryList($categories->buildTree());
        return $this->render('admin/_partials/_all_categories.html.twig', [
            'categories' => $categories->categoryListArray,
            'editedCategory' => $editedCategory,
        ]);
    }
}
