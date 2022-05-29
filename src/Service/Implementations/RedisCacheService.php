<?php

namespace App\Service\Implementations;

use App\Service\Abstracts\AbstractCache;
use App\Service\Interfaces\CacheInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisCacheService extends AbstractCache implements CacheInterface
{
    public function __construct(string $redisHost, string $redisPort)
    {
        $adapterInterface = new RedisAdapter(
            RedisAdapter::createConnection("redis://{$redisHost}:{$redisPort}")
        );
        parent::__construct($adapterInterface);
    }
}