<?php

namespace App\Service\Abstracts;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\CacheItem;

abstract class AbstractCache
{
    protected TagAwareAdapter $cacheAdapter;

    public function __construct(AdapterInterface $adapterInterface)
    {
        $this->cacheAdapter = new TagAwareAdapter($adapterInterface);
    }

    public function getItem(string $key): CacheItem
    {
        return $this->cacheAdapter->getItem($key);
    }

    public function save(CacheItemInterface $cacheItem): bool
    {
        return $this->cacheAdapter->save($cacheItem);
    }

    public function clear(string $prefix = ''): bool
    {
        return $this->cacheAdapter->clear($prefix);
    }
}