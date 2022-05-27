<?php

namespace App\Controller\Common;

use App\Entity\Subscription;
use App\Entity\User;
use App\Form\UserType;
use App\Service\Implementations\SubscriptionService;
use App\Service\Implementations\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/subscriptions')]
class SubscriptionController extends AbstractController
{
    public function __construct(
        private SubscriptionService $subscriptionService,
        private UserService $userService
    ) {
    }

    #[Route('/pricing', name: 'pricing', methods: ['GET'])]
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig', [
            'plans' => $this->subscriptionService->getAllPlans(),
        ]);
    }

    #[Route('/register/{plan}', name: 'front_register_page', methods: ['GET', 'POST'])]
    public function register(Request $request, SessionInterface $session, $plan): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        if ($request->isMethod('POST')) {
            // TODO: try to catch false response and addFlash to alert user about any errors
            $this->userService->save($request, $user, $form, $this->container);
            $this->subscriptionService->subscribe($user, $session);
            return $this->redirectToRoute('admin_users_my_profile');
        }

        if ($request->isMethod('GET')) {
            $session->set('planName', $plan);
            $session->set('planPrice', Subscription::getPlanDataPriceByName($plan));
        }

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $redirectTo = $plan === Subscription::getPlanDataNameByIndex(0)
                ? 'admin_users_my_profile'
                : 'payment';

            return $this->redirectToRoute($redirectTo);
        }

        return $this->render('front/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/payment/{paypal}', name: 'payment', defaults: ['paypal' => false])]
    public function payment(SessionInterface $session, $paypal): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        if ($paypal) {
            $this->subscriptionService->subscribe($this->getUser(), $session, true);
            return $this->redirectToRoute('admin_users_my_profile');
        }

        return $this->render('front/payment.html.twig');
    }

    #[Route('/cancel', name: 'cancel_plan')]
    public function cancelPlan(): Response
    {
        $this->subscriptionService->cancelPlan($this->getUser());

        return $this->redirectToRoute('admin_users_my_profile');
    }
}
