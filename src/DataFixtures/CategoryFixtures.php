<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private const ELECTRONICS_CATEGORY_ID = 1;
    private const TOYS_CATEGORY_ID = 1;
    private const BOOKS_CATEGORY_ID = 1;
    private const MOVIES_CATEGORY_ID = 1;

    private array $mainCategories = [
        ['Electronics', self::ELECTRONICS_CATEGORY_ID],
        ['Toys', self::TOYS_CATEGORY_ID],
        ['Books', self::BOOKS_CATEGORY_ID],
        ['Movies', self::MOVIES_CATEGORY_ID],
    ];

    private array $electronicsSubcategories = [
        ['Cameras', 5],
        ['Computers', 6],
        ['Cell Phones', 7],
    ];

    public function load(ObjectManager $manager): void
    {
        $this->loadMainCategories($manager);

        $subcategoriesToLoad = [
            [$this->electronicsSubcategories, self::ELECTRONICS_CATEGORY_ID],
        ];

        foreach ($subcategoriesToLoad as [$subcategory, $parentId]) {
            $this->loadSubcategories($manager, $parentId, $subcategory);
        }
    }

    private function loadMainCategories(ObjectManager $manager): void
    {
        foreach ($this->mainCategories as [$name]) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
        }

        $manager->flush();
    }

    private function loadSubcategories(ObjectManager $manager, int $parentId, array $categoryData): void
    {
        $parent = $manager->getRepository(Category::class)->find($parentId);

        foreach ($categoryData as [$name]) {
            $category = new Category();
            $category->setName($name);
            $category->setParent($parent);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
