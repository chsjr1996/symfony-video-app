<?php

namespace App\Service\Interfaces;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\CacheItem;

interface CacheInterface
{
    public function getItem(string $key): CacheItem;
    public function save(CacheItemInterface $cacheItem): bool;
    public function clear(string $prefix = ''): bool;
}