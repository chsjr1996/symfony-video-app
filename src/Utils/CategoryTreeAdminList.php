<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminList extends CategoryTreeAbstract
{
    public function getCategoryList(array $categories): string
    {
        $this->categoryList .= '<ul class="fa-ul text-left">';

        foreach ($categories as $category) {
            $id = $category['id'];
            $name = $this->slugify($category['name']);
            $urlEdit = $this->urlGenerator->generate('edit_category', ['id' => $id]);
            $urlDelete = $this->urlGenerator->generate('delete_category', ['id' => $id]);

            $this->categoryList .= '<li><i class="fa-li fa fa-arrow-right"></i>';
            $this->categoryList .= " {$name}";
            $this->categoryList .= "<a href=\"{$urlEdit}\">Edit</a>";
            $this->categoryList .= " <a onclick=\"return confirm('Are you sure?');\" href=\"{$urlDelete}\">Delete</a>";

            if (!empty($category['children'])) {
                $this->getCategoryList($category['children']);
            }

            $this->categoryList .= "</li>";
        }

        $this->categoryList .= "</ul>";
        return $this->categoryList;
    }
}
