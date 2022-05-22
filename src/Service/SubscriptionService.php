<?php

namespace App\Service;

use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;

class SubscriptionService
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private UserRepository $userRepository
    ) {
    }

    public function cancelPlan(User $user)
    {
        $subscription = $user->getSubscription();
        $subscription->setValidTo(new \DateTime());
        $subscription->setPaymentStatus(Subscription::STATUS_CANCELED);
        $this->userRepository->add($user, true);
        $this->subscriptionRepository->add($subscription, true);
    }

    public function getAllPlans(): array
    {
        $plans = [];
        $plansNames = Subscription::getAllPlansNames();
        $plansPrices = Subscription::getAllPlansPrices();
        $plansDescriptions = Subscription::getAllPlansDescriptions();
        $plansButtons = Subscription::getAllPlansButtons();

        foreach ($plansNames as $planName) {
            $plans[] = [
                'name' => $planName,
                'price' => $plansPrices[$planName],
                'descriptions' => $plansDescriptions[$planName],
                'buttons' => $plansButtons[$planName],
            ];
        }

        return $plans;
    }
}
