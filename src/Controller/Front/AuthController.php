<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    public function __construct(private UserService $userService)
    {
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('front/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // Symfony's managed route
        throw new \Exception('This should never be reacher!');
    }

    #[Route('/register', name: 'front_register_page', methods: ['GET'])]
    public function registerPage(): Response
    {
        $form = $this->createForm(UserType::class, null, [
            'action' => $this->generateUrl('front_register_store'),
        ]);

        return $this->render('front/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/register', name: 'front_register_store', methods: ['POST'])]
    public function registerStore(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        if ($this->userService->save($request, $user, $form, $this->container)) {
            return $this->redirectToRoute('front_main_page');
        }

        return $this->render('front/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
