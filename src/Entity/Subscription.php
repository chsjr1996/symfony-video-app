<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ORM\Table('subscriptions')]
class Subscription
{
    private const PLAN_NAMES = ['free', 'pro', 'enterprise'];
    private const PLAN_PRICES = ['free' => 0, 'pro' => 15, 'enterprise' => 29];
    private const PLAN_DESCRIPTIONS = [
        'free' => [
            'texts.front.pricing.advantages.access_for_one_month',
            'texts.front.pricing.advantages.help_center_access',
        ],
        'pro' => [
            'texts.front.pricing.advantages.unlimited_access',
            'texts.front.pricing.advantages.hd_available',
            'texts.front.pricing.advantages.no_ads_on_videos',
            'texts.front.pricing.advantages.help_center_access',
        ],
        'enterprise' => [
            'texts.front.pricing.advantages.unlimited_access',
            'texts.front.pricing.advantages.ultra_hd_available',
            'texts.front.pricing.advantages.no_ads_on_videos',
            'texts.front.pricing.advantages.help_center_access',
        ],
    ];
    private const PLAN_BUTTONS = [
        'free' => ['text' => 'texts.front.pricing.sign_up_for_free', 'class' => 'btn-outline-primary'],
        'pro' => ['text' => 'texts.front.pricing.get_started', 'class' => 'btn-primary'],
        'enterprise' => ['text' => 'texts.front.pricing.contact_us', 'class' => 'btn-primary'],
    ];
    public const PLAN_DEFAULT_DURATION = '+1 month';

    public const STATUS_CANCELED = 'canceled';
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const INVALIDS_STATUS = [self::STATUS_CANCELED, null];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $plan;

    #[ORM\Column(type: 'datetime')]
    private $valid_to;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $payment_status;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $free_plan_used;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(string $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->valid_to;
    }

    public function setValidTo(\DateTimeInterface $valid_to): self
    {
        $this->valid_to = $valid_to;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->payment_status;
    }

    public function setPaymentStatus(?string $payment_status): self
    {
        $this->payment_status = $payment_status;

        return $this;
    }

    public function getFreePlanUsed(): ?bool
    {
        return $this->free_plan_used;
    }

    public function setFreePlanUsed(?bool $free_plan_used): self
    {
        $this->free_plan_used = $free_plan_used;

        return $this;
    }

    public static function getPlanDataNameByIndex(int $index): ?string
    {
        return self::PLAN_NAMES[$index] ?? null;
    }

    public static function getPlanDataPriceByName(string $name): ?int
    {
        return self::PLAN_PRICES[$name] ?? null;
    }

    public static function getAllPlansNames(): array
    {
        return self::PLAN_NAMES;
    }

    public static function getAllPlansPrices(): array
    {
        return self::PLAN_PRICES;
    }

    public static function getAllPlansDescriptions(): array
    {
        return self::PLAN_DESCRIPTIONS;
    }

    public static function getAllPlansButtons(): array
    {
        return self::PLAN_BUTTONS;
    }
}
