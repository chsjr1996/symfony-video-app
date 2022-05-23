<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class UserController extends AbstractController
{
    public function __construct(private UserService $userService)
    {
    }

    #[Route('/su/users', name: 'admin_users_list')]
    public function index(): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $this->userService->all(),
        ]);
    }

    #[Route('/users/{user}', name: 'admin_users_show')]
    public function show(User $user): Response
    {
        return $this->render('admin/user/form.html.twig');
    }

    #[Route('/my_profile', name: 'admin_users_my_profile')]
    public function myProfile(): Response
    {
        return $this->render('admin/user/form.html.twig', [
            'subscription' => $this->getUser()->getSubscription(),
        ]);
    }

    #[Route('/users/delete/{user}', name: 'admin_users_delete', methods: ['DELETE'])]
    public function delete(User $user): Response
    {
        $selfUser = $user->getId() === $this->getUser()->getId();

        if (!$selfUser && !$this->isGranted('ADMIN_ROLE')) {
            // TODO: Launch a warning, maybe logout too?
            return $this->redirectToRoute('admin_users_my_profile');
        }

        if ($this->userService->remove($user) && $selfUser) {
            session_destroy();
            return $this->redirectToRoute('front_main_page');
        }

        return $this->redirectToRoute('admin_users_list');
    }
}
