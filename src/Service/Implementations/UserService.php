<?php

namespace App\Service\Implementations;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\Argument\ServiceLocator;
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
        return $this->userRepository->findBy([], ['name' => 'ASC']);
    }

    public function save(
        Request $request,
        User $user,
        FormInterface $form,
        ServiceLocator $container,
        bool $fromAdminPanel = false
    ): bool {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userFieldsData = $request->request->all('user');

            $user->setName($userFieldsData['name']);
            $user->setLastName($userFieldsData['last_name']);
            $user->setEmail($userFieldsData['email']);
            $password = $this->passwordHasher->hashPassword($user, $userFieldsData['password']['first']);
            $user->setPassword($password);

            if ($fromAdminPanel && in_array('ROLE_ADMIN', $user->getRoles())) {
                $user->setVimeoApiKey($userFieldsData['vimeo_api_key']);
            } else {
                $user->setRoles(['USER_ROLE']);
            }

            $this->userRepository->add($user, true);

            if (!$fromAdminPanel) {
                $this->loginUserAutomatically($user, $container);
            }

            return true;
        }

        return false;
    }

    public function getById(int $id): User
    {
        return $this->userRepository->find($id);
    }

    public function remove(User $user): bool
    {
        try {
            $this->userRepository->remove($user, true);
            return true;
        } catch (\Exception $ex) {
            // TODO: Log...
            return false;
        }
    }

    public function logoutUser(ServiceLocator $container): bool
    {
        try {
            $container->get('security.token_storage')->setToken(null);
            $this->requestStack->getSession()->invalidate();
            return true;
        } catch (\Exception $ex) {
            return false;
        }
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
