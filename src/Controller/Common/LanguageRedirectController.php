<?php

namespace App\Controller\Common;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see https://needlify.com/post/how-to-prefix-urls-with-the-locale-in-symfony-aea009a2
 */
class LanguageRedirectController extends AbstractController
{
    public function redirectToLocale(): Response
    {
        return $this->redirectToRoute('front_main_page');
    }
}
