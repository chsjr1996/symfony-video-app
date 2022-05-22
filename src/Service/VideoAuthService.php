<?php

namespace App\Service;

use App\Entity\Subscription;
use App\Entity\User;
use App\Entity\Video;
use Symfony\Component\Security\Core\Security;

class VideoAuthService
{
    public function __construct(
        private Security $security,
        private ?User $user = null
    ) {
        $this->user = $security->getUser();
    }

    public function checkSubscription()
    {
        try {
            if (!$this->user || $this->user->getSubscription() == null) {
                throw new \Exception('invalid user!');
            }
            $paymentStatus = $this->user->getSubscription()->getPaymentStatus();
            $valid = new \DateTime() < $this->user->getSubscription()->getValidTo();

            if (in_array($paymentStatus, Subscription::INVALIDS_STATUS) || !$valid) {
                throw new \Exception('invalid user!');
            }

            return null;
        } catch (\Exception $ex) {
            static $video = Video::VIDEO_FOR_NON_MEMBER;
            return $video;
        }
    }
}
