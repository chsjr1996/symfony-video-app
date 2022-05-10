<?php

namespace App\Tests\Twig;

use App\Twig\AppExtension;
use PHPUnit\Framework\TestCase;

class SluggerTest extends TestCase
{
    /**
     * @dataProvider getSlugs
     */
    public function testSlugify(string $original, string $slug): void
    {
        $slugger = new AppExtension();
        $this->assertSame($slug, $slugger->slugify($original));
    }

    public function getSlugs()
    {
        yield ['Lorem Ipsum', 'lorem-ipsum'];
        yield [' Lorem Ipsum', 'lorem-ipsum'];
        yield [' LoReM IpSuM', 'lorem-ipsum'];
        yield ['!Lorem Ipsum!', 'lorem-ipsum'];
        yield ['Children\'s book', 'childrens-book'];
        yield ['Toma um xarope que passa', 'toma-um-xarope-que-passa'];
    }
}
