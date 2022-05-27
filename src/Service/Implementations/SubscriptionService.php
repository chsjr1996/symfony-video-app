<?php

namespace App\Service\Implementations;

use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SubscriptionService
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private UserRepository $userRepository
    ) {
    }

    public function subscribe(User $user, SessionInterface $session, $tmpForcePayment = false): bool
    {
        try {
            $validTo = (new \DateTime())->modify(Subscription::PLAN_DEFAULT_DURATION);
            $plan = $session->get('planName');

            if (is_null($subscription = $user->getSubscription())) {
                $subscription = new Subscription();
            }

            if ($subscription->getFreePlanUsed() && $plan === Subscription::getPlanDataNameByIndex(0)) {
                return false;
            }

            $subscription->setValidTo($validTo);
            $subscription->setPlan($plan);

            if ($plan === Subscription::getPlanDataNameByIndex(0)) {
                $subscription->setFreePlanUsed(true);
                $subscription->setPaymentStatus(Subscription::STATUS_PAID);
            }

            // TODO: tmp code...
            if ($tmpForcePayment) {
                $subscription->setPaymentStatus(Subscription::STATUS_PAID);
            }

            $user->setSubscription($subscription);

            $this->userRepository->add($user, true);
            $this->subscriptionRepository->add($subscription, true);

            return true;
        } catch (\Exception $ex) {
            // TODO: LOG Here...
            return false;
        }
    }

    public function cancelPlan(User $user): bool
    {
        try {
            $subscription = $user->getSubscription();
            $subscription->setValidTo(new \DateTime());
            $subscription->setPaymentStatus(Subscription::STATUS_CANCELED);

            $this->userRepository->add($user, true);
            $this->subscriptionRepository->add($subscription, true);

            return true;
        } catch (\Exception $ex) {
            // TODO: LOG Here...
            return false;
        }
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
