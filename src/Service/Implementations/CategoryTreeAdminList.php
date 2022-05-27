<?php

namespace App\Service\Implementations;

use App\Service\Abstracts\CategoryTreeAbstract;

class CategoryTreeAdminList extends CategoryTreeAbstract
{
    public function getCategoryList(array $categories): string
    {
        $this->categoryListHtml.= '<ul class="fa-ul text-left">';

        foreach ($categories as $category) {
            $id = $category['id'];
            $name = $category['name'];
            $urlEdit = $this->urlGenerator->generate('admin_categories_edit', ['id' => $id]);
            $urlDelete = $this->urlGenerator->generate('admin_categories_delete', ['id' => $id]);

            $this->categoryListHtml.= '<li><i class="fa-li fa fa-arrow-right"></i>';
            $this->categoryListHtml.= " {$name}";
            $this->categoryListHtml.= " <a href=\"{$urlEdit}\">Edit</a>";
            $this->categoryListHtml.= $this->generateDeleteForm($urlDelete);

            if (!empty($category['children'])) {
                $this->getCategoryList($category['children']);
            }

            $this->categoryListHtml.= "</li>";
        }

        $this->categoryListHtml.= "</ul>";
        return $this->categoryListHtml;
    }

    private function generateDeleteForm(string $urlDelete): string
    {
        $htmlForm = "<form class=\"delete-form\" action=\"{$urlDelete}\" method=\"POST\">";
        $htmlForm.= "<input type=\"hidden\" name=\"_method\" value=\"DELETE\" />";
        $htmlForm.= " <input type=\"submit\" value=\"Delete\" onclick=\"return confirm('Are you sure?');\" />";
        $htmlForm.= "</form>";
        return $htmlForm;
    }
}
