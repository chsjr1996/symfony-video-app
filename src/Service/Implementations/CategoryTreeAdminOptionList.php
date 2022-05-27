<?php

namespace App\Service\Implementations;

use App\Service\Abstracts\CategoryTreeAbstract;

class CategoryTreeAdminOptionList extends CategoryTreeAbstract
{
    public function getCategoryList(array $categories, int $repeat = 0): array
    {
        foreach ($categories as $category) {
            $id = $category['id'];
            $name = $category['name'];

            $this->categoryListArray[] = [
                'name' => str_repeat('-', $repeat) . $name,
                'id' => $id,
            ];

            if (!empty($category['children'])) {
                $repeat = $repeat + 2;
                $this->getCategoryList($category['children'], $repeat);
                $repeat = $repeat - 2;
            }
        }

        return $this->categoryListArray;
    }
}
