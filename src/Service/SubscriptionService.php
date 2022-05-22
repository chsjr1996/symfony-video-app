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
}
