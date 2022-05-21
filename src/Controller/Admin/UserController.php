<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class UserController extends AbstractController
{
    #[Route('/su', name: 'admin_users_list')]
    public function index(): Response
    {
        return $this->render('admin/user/index.html.twig');
    }

    #[Route('/{user}', name: 'admin_users_show')]
    public function show(User $user): Response
    {
        return $this->render('admin/user/form.html.twig');
    }
}
