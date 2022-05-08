<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminList extends CategoryTreeAbstract
{
    public function getCategoryList(array $categories): string
    {
        $this->categoryListHtml.= '<ul class="fa-ul text-left">';

        foreach ($categories as $category) {
            $id = $category['id'];
            $name = $this->slugify($category['name']);
            $urlEdit = $this->urlGenerator->generate('edit_category', ['id' => $id]);
            $urlDelete = $this->urlGenerator->generate('delete_category', ['id' => $id]);

            $this->categoryListHtml.= '<li><i class="fa-li fa fa-arrow-right"></i>';
            $this->categoryListHtml.= " {$name}";
            $this->categoryListHtml.= "<a href=\"{$urlEdit}\">Edit</a>";
            $this->categoryListHtml.= " <a onclick=\"return confirm('Are you sure?');\" href=\"{$urlDelete}\">Delete</a>";

            if (!empty($category['children'])) {
                $this->getCategoryList($category['children']);
            }

            $this->categoryListHtml.= "</li>";
        }

        $this->categoryListHtml.= "</ul>";
        return $this->categoryListHtml;
    }
}
