<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @todo refactor, maybe can be more clean?
     */
    #[Route('/users/{id}', name: 'admin_users_show', defaults: ['id' => null], methods: ['GET', 'POST'])]
    #[Route('/my_profile', name: 'admin_users_my_profile')]
    public function show(Request $request, $id = null): Response
    {
        $isInvalid = '';
        $selfUser = $id == $this->getUser()->getId() || is_null($id);
        $userId = $selfUser ? $this->getUser()->getId() : $id;
        $user = $userId ? $this->userService->getById($userId) : new User();

        $form = $this->createForm(UserType::class, $user, [
            'user_roles' => $user->getRoles(),
        ]);

        if ($request->isMethod('POST')) {;
            $success = $this->userService->save($request, $user, $form, $this->container, true);
            $type = $success ? 'success' : 'danger';
            $message = $success ? 'Your changes were saved!' : 'An error occurred on save your data!';
            $isInvalid = $success ? '' : 'is-invalid';
            $this->addFlash($type, $message);
        }

        return $this->render('admin/user/form.html.twig', [
            'form' => $form->createView(),
            'selfUser' => $selfUser,
            'userId' => $id,
            'subscription' => $this->getUser()->getSubscription(),
            'is_invalid' => $isInvalid,
        ]);
    }

    #[Route('/users/{user}', name: 'admin_users_delete', methods: ['DELETE'])]
    public function delete(User $user): Response
    {
        $selfUser = $user->getId() === $this->getUser()->getId();

        if (!$selfUser && !$this->isGranted('ROLE_ADMIN')) {
            // TODO: Launch a warning, maybe logout too?
            return $this->redirectToRoute('admin_users_my_profile');
        }

        if (!$this->userService->remove($user)) {
            $this->addFlash('danger', 'An error occurred on delete user!');
        }

        if ($selfUser) {
            $this->userService->logoutUser($this->container);
            return $this->redirectToRoute('front_main_page');
        }

        $this->addFlash('success', 'The user was deleted!');
        return $this->redirectToRoute('admin_users_list');
    }
}
