<?php

namespace App\Service\Implementations;

use App\Service\Abstracts\CategoryTreeAbstract;

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
        $this->categoryListHtml.= "<ul>";

        foreach ($categories as $category) {
            $id = $category['id'];
            $name = $category['name'];

            $url = $this->urlGenerator->generate('video_list', [
                'categoryname' => $this->slugify($name),
                'id' => $id,
            ]);

            $this->categoryListHtml.= "<li><a href=\"{$url}\">{$name}</a>";

            if (!empty($category['children'])) {
                $this->getCategoryList($category['children']);
            }

            $this->categoryListHtml.= "</li>";
        }

        $this->categoryListHtml.= "</ul>";
        return $this->categoryListHtml;
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

    public function getChildIds(int $parent): array
    {
        // TODO: study more about static variables, here this way cause strange (and EXpected) behaviors...
        // static $categoryIds = [];
        $categoryIds = [];

        foreach ($this->categoriesTree as $category) {
            if ($category['parent_id'] == $parent) {
                $categoryIds[] = $category['id'];
                $this->getChildIds($category['id']);
            }
        }

        return $categoryIds;
    }
}
