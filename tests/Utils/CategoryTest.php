<?php

namespace App\Tests\Utils;

use App\Tests\TestsHelperTrait;
use App\Twig\AppExtension;
use App\Utils\CategoryTreeAdminList;
use App\Utils\CategoryTreeAdminOptionList;
use App\Utils\CategoryTreeFrontPage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
{
    use TestsHelperTrait;

    private CategoryTreeFrontPage $mockedCategoryTreeFrontPage;
    private CategoryTreeAdminList $mockedCategoryTreeAdminList;
    private CategoryTreeAdminOptionList $mockedCategoryTreeOptionList;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $kernel->getContainer()->set('app.twig.app_extension', new AppExtension());

        $appExtension = $kernel->getContainer()->get('app.twig.app_extension');
        $urlGenerator = $kernel->getContainer()->get('router');

        $testedClasses = [
            'mockedCategoryTreeFrontPage' => CategoryTreeFrontPage::class,
            'mockedCategoryTreeAdminList' => CategoryTreeAdminList::class,
            'mockedCategoryTreeOptionList' => CategoryTreeAdminOptionList::class,
        ];

        foreach ($testedClasses as $propertyName => $testedClass) {
            $this->$propertyName = $this->getMockBuilder($testedClass)
                ->disableOriginalConstructor()
                ->setMethods() // TODO: deprecated
                ->getMock();

            $this->$propertyName->categoryListHtml = '';
            $this->$propertyName->categoryListArray = [];
            $this->setProtectedProperty($this->$propertyName, 'twigExtensions', $appExtension);
            $this->setProtectedProperty($this->$propertyName, 'urlGenerator', $urlGenerator);
        }
    }

    /**
     * @dataProvider dataForCategoryTreeFrontPage
     */
    public function testCategoryTreeFronPage(string $html, array $categories, int $id): void
    {
        $this->mockedCategoryTreeFrontPage->categoriesTree = $categories;

        $mainParentId = $this->mockedCategoryTreeFrontPage->getMainParent($id)['id'];
        $buildedCategoriesTree = $this->mockedCategoryTreeFrontPage->buildTree($mainParentId);

        $this->assertSame(
            $html,
            $this->mockedCategoryTreeFrontPage->getCategoryList($buildedCategoriesTree)
        );
    }

    /**
     * @dataProvider dataForCategoryTreeAdminOptionList
     */
    public function testCategoryTreeAdminOptionList(array $optionsArray, array $categories)
    {
        $this->mockedCategoryTreeOptionList->categoriesTree = $categories;
        $buildedCategoriesTree = $this->mockedCategoryTreeOptionList->buildTree();

        $this->assertSame($optionsArray, $this->mockedCategoryTreeOptionList->getCategoryList($buildedCategoriesTree));
    }

    /**
     * @dataProvider dataForCategoryTreeAdminList
     */
    public function testCategoryTreeAdminList(string $html, array $categories)
    {
        $this->mockedCategoryTreeAdminList->categoriesTree = $categories;
        $buildedCategoriesTree = $this->mockedCategoryTreeAdminList->buildTree();

        $this->assertSame($html, $this->mockedCategoryTreeAdminList->getCategoryList($buildedCategoriesTree));
    }

    public function dataForCategoryTreeFrontPage()
    {
        yield [
            '<ul><li><a href="/video-list/category/computers,6">Computers</a><ul><li><a href="/video-list/category/laptops,8">Laptops</a><ul><li><a href="/video-list/category/hp,14">HP</a></li></ul></li></ul></li></ul>',
            [
                ['name' => 'Electronics', 'id' => 1, 'parent_id' => null],
                ['name' => 'Computers', 'id' => 6, 'parent_id' => 1],
                ['name' => 'Laptops', 'id' => 8, 'parent_id' => 6],
                ['name' => 'HP', 'id' => 14, 'parent_id' => 8]
            ],
            1
        ];

        yield [
            '<ul><li><a href="/video-list/category/computers,6">Computers</a><ul><li><a href="/video-list/category/laptops,8">Laptops</a><ul><li><a href="/video-list/category/hp,14">HP</a></li></ul></li></ul></li></ul>',
            [
                ['name' => 'Electronics', 'id' => 1, 'parent_id' => null],
                ['name' => 'Computers', 'id' => 6, 'parent_id' => 1],
                ['name' => 'Laptops', 'id' => 8, 'parent_id' => 6],
                ['name' => 'HP', 'id' => 14, 'parent_id' => 8]
            ],
            6
        ];

        yield [
            '<ul><li><a href="/video-list/category/computers,6">Computers</a><ul><li><a href="/video-list/category/laptops,8">Laptops</a><ul><li><a href="/video-list/category/hp,14">HP</a></li></ul></li></ul></li></ul>',
            [
                ['name' => 'Electronics', 'id' => 1, 'parent_id' => null],
                ['name' => 'Computers', 'id' => 6, 'parent_id' => 1],
                ['name' => 'Laptops', 'id' => 8, 'parent_id' => 6],
                ['name' => 'HP', 'id' => 14, 'parent_id' => 8]
            ],
            8
        ];

        yield [
            '<ul><li><a href="/video-list/category/computers,6">Computers</a><ul><li><a href="/video-list/category/laptops,8">Laptops</a><ul><li><a href="/video-list/category/hp,14">HP</a></li></ul></li></ul></li></ul>',
            [
                ['name' => 'Electronics', 'id' => 1, 'parent_id' => null],
                ['name' => 'Computers', 'id' => 6, 'parent_id' => 1],
                ['name' => 'Laptops', 'id' => 8, 'parent_id' => 6],
                ['name' => 'HP', 'id' => 14, 'parent_id' => 8]
            ],
            14
        ];
    }

    public function dataForCategoryTreeAdminOptionList()
    {
        yield [
            [
                ['name' => 'Electronics', 'id' => 1],
                ['name' => '--Computers', 'id' => 6],
                ['name' => '----Laptops', 'id' => 8],
                ['name' => '------HP', 'id' => 14],
            ],
            [
                ['name' => 'Electronics', 'id' => 1, 'parent_id' => null],
                ['name' => 'Computers', 'id' => 6, 'parent_id' => 1],
                ['name' => 'Laptops', 'id' => 8, 'parent_id' => 6],
                ['name' => 'HP', 'id' => 14, 'parent_id' => 8],
            ],
        ];
    }

    public function dataForCategoryTreeAdminList()
    {
        yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i> Toys <a href="/admin/su/categories/edit/2">Edit</a><form class="delete-form" action="/admin/su/categories/2" method="POST"><input type="hidden" name="_method" value="DELETE" /> <input type="submit" value="Delete" onclick="return confirm(\'Are you sure?\');" /></form></li></ul>',
            [
                ['id' => 2, 'parent_id' => null, 'name' => 'Toys']
            ]
        ];

        yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i> Toys <a href="/admin/su/categories/edit/2">Edit</a><form class="delete-form" action="/admin/su/categories/2" method="POST"><input type="hidden" name="_method" value="DELETE" /> <input type="submit" value="Delete" onclick="return confirm(\'Are you sure?\');" /></form></li><li><i class="fa-li fa fa-arrow-right"></i> Movies <a href="/admin/su/categories/edit/3">Edit</a><form class="delete-form" action="/admin/su/categories/3" method="POST"><input type="hidden" name="_method" value="DELETE" /> <input type="submit" value="Delete" onclick="return confirm(\'Are you sure?\');" /></form></li></ul>',
            [
                ['id' => 2, 'parent_id' => null, 'name' => 'Toys'],
                ['id' => 3, 'parent_id' => null, 'name' => 'Movies']
            ]
        ];

        yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i> Toys <a href="/admin/su/categories/edit/2">Edit</a><form class="delete-form" action="/admin/su/categories/2" method="POST"><input type="hidden" name="_method" value="DELETE" /> <input type="submit" value="Delete" onclick="return confirm(\'Are you sure?\');" /></form></li><li><i class="fa-li fa fa-arrow-right"></i> Movies <a href="/admin/su/categories/edit/3">Edit</a><form class="delete-form" action="/admin/su/categories/3" method="POST"><input type="hidden" name="_method" value="DELETE" /> <input type="submit" value="Delete" onclick="return confirm(\'Are you sure?\');" /></form><ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i> Horrors <a href="/admin/su/categories/edit/4">Edit</a><form class="delete-form" action="/admin/su/categories/4" method="POST"><input type="hidden" name="_method" value="DELETE" /> <input type="submit" value="Delete" onclick="return confirm(\'Are you sure?\');" /></form><ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i> Not so scary <a href="/admin/su/categories/edit/5">Edit</a><form class="delete-form" action="/admin/su/categories/5" method="POST"><input type="hidden" name="_method" value="DELETE" /> <input type="submit" value="Delete" onclick="return confirm(\'Are you sure?\');" /></form></li></ul></li></ul></li></ul>',

            [
                ['id' => 2, 'parent_id' => null, 'name' => 'Toys'],
                ['id' => 3, 'parent_id' => null, 'name' => 'Movies'],
                ['id' => 4, 'parent_id' => 3, 'name' => 'Horrors'],
                ['id' => 5, 'parent_id' => 4, 'name' => 'Not so scary']
            ]
        ];
    }
}
