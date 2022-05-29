<?php

namespace App\EventSubscriber;

use App\Service\Interfaces\CacheInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class AdminChangesDataSubscriber implements EventSubscriberInterface
{
    // TODO: Study how to fix the 'like_control.POST' in real apps...
    private const ROUTES_TO_CLEAR = [
        'admin_categories_store.POST',
        'admin_categories_update.PUT',
        'admin_categories_delete.DELETE',
        'delete_locally.DELETE',
        'update_video_category.POST',
        'like_control.POST', // --- In a real app it's not good...
    ];

    public function __construct(private CacheInterface $cache)
    {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $requestRouteAndMethod = $this->getRequestRouteAndMethod($event);

        if (!in_array($requestRouteAndMethod, self::ROUTES_TO_CLEAR)) {
            return;
        }

        $this->cache->clear();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }

    private function getRequestRouteAndMethod(ResponseEvent $event): string
    {
        $route = $event->getRequest()->attributes->get('_route');
        $method = $event->getRequest()->getMethod();

        return "{$route}.{$method}";
    }
}
