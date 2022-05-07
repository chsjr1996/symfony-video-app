<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private const ELECTRONICS_CATEGORY_ID = 1;
    private const TOYS_CATEGORY_ID = 2;
    private const BOOKS_CATEGORY_ID = 3;
    private const MOVIES_CATEGORY_ID = 4;
    private const CAMERAS_CATEGORY_ID = 5;
    private const COMPUTERS_CATEGORY_ID = 6;
    private const CELL_PHONES_CATEGORY_ID = 7;
    private const LAPTOPS_CATEGORY_ID = 8;
    private const DESKTOPS_CATEGORY_ID = 9;
    private const APPLE_CATEGORY_ID = 10;
    private const ASUS_CATEGORY_ID = 11;
    private const DELL_CATEGORY_ID = 12;
    private const LENOVO_CATEGORY_ID = 13;
    private const HP_CATEGORY_ID = 14;
    private const CHILDREN_BOOKS_CATEGORY_ID = 15;
    private const KINDLE_BOOKS_CATEGORY_ID = 16;
    private const ROMANCE_CATEGORY_ID = 17;
    private const FAMILY_CATEGORY_ID = 18;
    private const ROMANTIC_COMEDY_CATEGORY_ID = 19;
    private const ROMANTIC_DRAMA_CATEGORY_ID = 20;

    private array $mainCategories = [
        ['Electronics', self::ELECTRONICS_CATEGORY_ID],
        ['Toys', self::TOYS_CATEGORY_ID],
        ['Books', self::BOOKS_CATEGORY_ID],
        ['Movies', self::MOVIES_CATEGORY_ID],
    ];

    private array $electronicsSubcategories = [
        ['Cameras', self::CAMERAS_CATEGORY_ID],
        ['Computers', self::COMPUTERS_CATEGORY_ID],
        ['Cell Phones', self::CELL_PHONES_CATEGORY_ID],
    ];

    private array $computersSubcategories = [
        ['Laptops', self::LAPTOPS_CATEGORY_ID],
        ['Desktops', self::DESKTOPS_CATEGORY_ID],
    ];

    private array $laptopsSubcategories = [
        ['Apple', self::APPLE_CATEGORY_ID],
        ['Asus', self::ASUS_CATEGORY_ID],
        ['Dell', self::DELL_CATEGORY_ID],
        ['Lenovo', self::LENOVO_CATEGORY_ID],
        ['HP', self::HP_CATEGORY_ID],
    ];

    private array $booksSubcategories = [
        ['Children\'s Books', self::CHILDREN_BOOKS_CATEGORY_ID],
        ['Kindle eBooks', self::KINDLE_BOOKS_CATEGORY_ID],
    ];

    private array $moviesSubcategories = [
        ['Romance', self::ROMANCE_CATEGORY_ID],
        ['Family', self::FAMILY_CATEGORY_ID],
    ];

    private array $romanceSubcategory = [
        ['Romantic Comedy', self::ROMANTIC_COMEDY_CATEGORY_ID],
        ['Romantic Drama', self::ROMANTIC_DRAMA_CATEGORY_ID],
    ];

    public function load(ObjectManager $manager): void
    {
        $this->loadMainCategories($manager);

        $subcategoriesToLoad = [
            [$this->electronicsSubcategories, self::ELECTRONICS_CATEGORY_ID],
            [$this->computersSubcategories, self::COMPUTERS_CATEGORY_ID],
            [$this->laptopsSubcategories, self::LAPTOPS_CATEGORY_ID],
            [$this->booksSubcategories, self::BOOKS_CATEGORY_ID],
            [$this->moviesSubcategories, self::MOVIES_CATEGORY_ID],
            [$this->romanceSubcategory, self::ROMANCE_CATEGORY_ID],
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
