<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\Query\AST\Subselect;
use Doctrine\Persistence\ObjectManager;

class SubscriptionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getSubscriptionData() as [$userId, $plan, $validTo, $paymentStatus, $freePlanUsed]) {
            $subscription = new Subscription();
            $subscription->setPlan($plan);
            $subscription->setValidTo($validTo);
            $subscription->setPaymentStatus($paymentStatus);
            $subscription->setFreePlanUsed($freePlanUsed);

            /** @var User */
            $user = $manager->getRepository(User::class)->find($userId);
            $user->setSubscription($subscription);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getSubscriptionData(): array
    {
        return [
            [
                1,
                Subscription::getPlanDataNameByIndex(1),
                (new \DateTime())->modify('+100 year'),
                Subscription::STATUS_PAID,
                false,
            ],
            [
                2,
                Subscription::getPlanDataNameByIndex(2),
                (new \DateTime())->modify('+100 year'),
                Subscription::STATUS_PAID,
                false,
            ],
            [
                3,
                Subscription::getPlanDataNameByIndex(1),
                (new \DateTime())->modify('+100 year'),
                Subscription::STATUS_PAID,
                false,
            ],
            [
                4,
                Subscription::getPlanDataNameByIndex(0),
                (new \DateTime())->modify('+1 minute'),
                Subscription::STATUS_PAID,
                true,
            ],
        ];
    }
}
