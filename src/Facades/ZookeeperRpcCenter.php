<?php

namespace Mongooer\SwooleThriftZookeeper\Facades;

use Illuminate\Support\Facades\Facade;
use Mongooer\SwooleThriftZookeeper\Library\Zookeeper\ZookeeperInterface;

/**
 * @method static ZookeeperInterface getServer(string $channel = "default")
 *
 */
class ZookeeperRpcCenter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ZookeeperRpcCenter';
    }
}
