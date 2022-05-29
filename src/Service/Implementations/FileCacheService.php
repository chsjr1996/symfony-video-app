<?php

namespace App\Service\Implementations;

use App\Service\Abstracts\AbstractCache;
use App\Service\Interfaces\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class FileCacheService extends AbstractCache implements CacheInterface
{
    public function __construct()
    {
        $adapterInterface = new FilesystemAdapter();
        parent::__construct($adapterInterface);
    }
}