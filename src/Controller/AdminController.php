<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeAdminList;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @todo Refactor: isolate Admin modules in specific files
 */
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

    #[Route('/su/categories', name: 'categories', methods: ['GET', 'POST'])]
    public function categories(CategoryTreeAdminList $categories, Request $request): Response
    {
        $categories->getCategoryList($categories->buildTree());

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $isInvalid = null;

        if ($this->saveCategory($form, $request, $category)) {
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

    #[Route('/su/edit-category/{id}', name: 'edit_category', methods: ['GET', 'POST'])]
    public function editCategory(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $isInvalid = null;

        if ($this->saveCategory($form, $request, $category)) {
            return $this->redirectToRoute('categories');
        } else if ($request->isMethod('POST')) {
            $isInvalid = ' is-invalid';
        }

        return $this->render('admin/edit_category.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'is_invalid' => $isInvalid,
        ]);
    }

    #[Route('/su/delete-category/{id}', name: 'delete_category')]
    public function deleteCategory(Category $category): Response
    {
        $this->categoryRepository->remove($category, true);
        return $this->redirectToRoute('categories');
    }

    #[Route('/videos', name: 'videos')]
    public function videos(VideoRepository $videoRepository): Response
    {
        $videos = [];

        if ($this->isGranted('ROLE_ADMIN')) {
            $videos = $videoRepository->findAll();
        } else {
            $videos = $this->getUser()->getLikedVideos();
        }

        return $this->render('admin/videos.html.twig', [
            'videos' => $videos,
        ]);
    }

    #[Route('/su/upload-video', name: 'upload_video')]
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    #[Route('/su/users', name: 'users')]
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categories->getCategoryList($categories->buildTree());
        return $this->render('admin/partials/_all_categories.html.twig', [
            'categories' => $categories->categoryListArray,
            'editedCategory' => $editedCategory,
        ]);
    }

    /**
     * @todo fix: edit_category exception caused by empty category name
     */
    private function saveCategory(FormInterface $form, Request $request, Category $category): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryFields = $request->request->all('category');
            $parent = $this->categoryRepository->find($categoryFields['parent']);

            $category->setName($categoryFields['name']);
            $category->setParent($parent);

            $this->categoryRepository->add($category, true);

            return true;
        }

        return false;
    }
}
