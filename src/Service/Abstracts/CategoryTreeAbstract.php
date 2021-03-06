<?php

namespace App\Service\Abstracts;

use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @todo Use DTO instead of array association
 */
abstract class CategoryTreeAbstract
{
    protected static $dbConnection;

    public function __construct(
        protected AppExtension $twigExtensions,
        protected EntityManagerInterface $entityManager,
        protected UrlGeneratorInterface $urlGenerator,
        public array $categoriesTree = [],
        public string $categoryListHtml = '',
        public array $categoryListArray = [],
        public string $mainParentId = '',
        public string $mainParentName = '',
        public string $currentCategoryName = '',
        public array $currentCategoryTreeIds = []
    ) {
        $this->categoriesTree = $this->getCategories();
    }

    abstract public function getCategoryList(array $categories): string|array;

    public function buildTree(int $parentId = null): array
    {
        $subcategories = [];
        foreach ($this->categoriesTree as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildTree($category['id']);

                if ($children) {
                    $category['children'] = $children;
                }
                $subcategories[] = $category;
            }
        }

        return $subcategories;
    }

    protected function slugify(string $text): string
    {
        return $this->twigExtensions->slugify($text);
    }

    private function getCategories(): array
    {
        if (self::$dbConnection) {
            return self::$dbConnection;
        }

        // TODO: 6.133... Use Doctrine maybe?
        $conn = $this->entityManager->getConnection();
        $sql = "SELECT * FROM categories";
        $stmt = $conn->prepare($sql);
        $res = $stmt->executeQuery()->fetchAllAssociative();
        return self::$dbConnection = $res;
    }
}
