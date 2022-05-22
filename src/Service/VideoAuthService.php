<?php

namespace App\Service;

use App\Entity\Subscription;
use App\Entity\User;
use App\Entity\Video;
use Symfony\Component\Security\Core\Security;

class VideoAuthService
{
    private User $user;

    public function __construct(private Security $security) {
        $this->user = $security->getUser();
    }

    public function checkSubscription()
    {
        if (!$this->user || $this->user->getSubscription() == null) {
            return false;
        }

        $paymentStatus = $this->user->getSubscription()->getPaymentStatus();
        $valid = new \DateTime() < $this->user->getSubscription()->getValidTo();

        if (in_array($paymentStatus, Subscription::INVALIDS_STATUS) || !$valid) {
            static $video = Video::VIDEO_FOR_NON_MEMBER;
            return $video;
        }

        return null;
    }
}
