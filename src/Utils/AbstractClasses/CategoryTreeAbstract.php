<?php

namespace App\Utils\AbstractClasses;

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
        private AppExtension $twigExtesions,
        protected EntityManagerInterface $entityManager,
        protected UrlGeneratorInterface $urlGenerator,
        public array $categoriesTree = [],
        public string $categoryList = '',
        public string $mainParentId = '',
        public string $mainParentName = '',
        public string $currentCategoryName = ''
    ) {
        $this->categoriesTree = $this->getCategories();
    }

    abstract public function getCategoryList(array $categories): string;

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
        return $this->twigExtesions->slugify($text);
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
