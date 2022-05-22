<?php

namespace App\Controller\Common;

use App\Service\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/subscriptions')]
class SubscriptionController extends AbstractController
{
    public function __construct(private SubscriptionService $subscriptionService)
    {
    }

    #[Route('/pricing', name: 'pricing', methods: ['GET'])]
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig', [
            'plans' => $this->subscriptionService->getAllPlans(),
        ]);
    }

    #[Route('/cancel', name: 'cancel_plan')]
    public function cancelPlan(): Response
    {
        $this->subscriptionService->cancelPlan($this->getUser());

        return $this->redirectToRoute('admin_users_my_profile');
    }
}
