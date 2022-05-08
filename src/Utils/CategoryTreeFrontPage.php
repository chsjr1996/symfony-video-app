<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeFrontPage extends CategoryTreeAbstract
{
    public function getCategoryListAndParent(int $id): string
    {
        $parentData = $this->getMainParent($id);
        $key = array_search($id, array_column($this->categoriesTree, 'id'));

        $this->mainParentId = $parentData['id'];
        $this->mainParentName = $parentData['name'];
        $this->currentCategoryName = $this->categoriesTree[$key]['name'];

        $categories = $this->buildTree($parentData['id']);

        return $this->getCategoryList($categories);
    }

    // TODO: 6.136... Refactor using other way
    public function getCategoryList(array $categories): string
    {
        $this->categoryList .= "<ul>";

        foreach ($categories as $category) {
            $id = $category['id'];
            $name = $this->twigExtesions->slugify($category['name']);

            $url = $this->urlGenerator->generate('video_list', [
                'categoryname' => $name,
                'id' => $id,
            ]);

            $this->categoryList .= "<li><a href=\"{$url}\">{$name}</a>";

            if (!empty($category['children'])) {
                $this->getCategoryList($category['children']);
            }

            $this->categoryList .= "</li>";
        }

        $this->categoryList .= "</ul>";
        return $this->categoryList;
    }

    public function getMainParent(int $id): array
    {
        $key = array_search($id, array_column($this->categoriesTree, 'id'));
        $parentId = $this->categoriesTree[$key]['parent_id'];

        if ($parentId != null) {
            return $this->getMainParent($parentId);
        }

        return [
            'id' => $this->categoriesTree[$key]['id'],
            'name' => $this->categoriesTree[$key]['name'],
        ];
    }
}
