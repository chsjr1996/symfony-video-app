<?php

namespace App\Service\Implementations;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private CategoryTreeFrontPage $categoryTreeFrontPage
    ) {
    }

    public function save(Request $request, Category $category, FormInterface $form): bool
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

    public function listMainCategories()
    {
        return $this->categoryRepository->findBy(
            ['parent' => null],
            ['name' => 'ASC']
        );
    }

    public function getCategoryTreeIds(int $parentId): CategoryTreeFrontPage
    {
        $this->categoryTreeFrontPage->getCategoryListAndParent($parentId);
        $this->categoryTreeFrontPage->currentCategoryTreeIds = $this->categoryTreeFrontPage->getChildIds($parentId);
        $this->categoryTreeFrontPage->currentCategoryTreeIds[] = (int) $parentId;
        return $this->categoryTreeFrontPage;
    }

    public function getById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    public function remove(Category $category): bool
    {
        $this->categoryRepository->remove($category, true);
        return true;
    }
}
