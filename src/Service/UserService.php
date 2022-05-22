<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\Argument\ServiceLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserService
{

    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private RequestStack $requestStack
    ) {
    }

    /**
     * @return User[]
     */
    public function all(): array
    {
        return $this->userRepository->findAll();
    }

    public function save(
        Request $request,
        User $user,
        FormInterface $form,
        ServiceLocator $container
    ): bool {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userFieldsData = $request->request->all('user');

            $user->setName($userFieldsData['name']);
            $user->setLastName($userFieldsData['last_name']);
            $user->setEmail($userFieldsData['email']);
            $password = $this->passwordHasher->hashPassword($user, $userFieldsData['password']['first']);
            $user->setPassword($password);
            $user->setRoles(['USER_ROLE']);

            $this->userRepository->add($user, true);
            $this->loginUserAutomatically($user, $container);

            return true;
        }

        return false;
    }

    private function loginUserAutomatically(User $user, ServiceLocator $container): void
    {
        $token = new UsernamePasswordToken(
            $user,
            'main',
            $user->getRoles()
        );

        $container->get('security.token_storage')->setToken($token);
        $this->requestStack->getSession()->set('_security_main', serialize($token));
    }
}
