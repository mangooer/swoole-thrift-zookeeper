<?php

namespace Mongooer\SwooleThriftZookeeper\Facades;

/**
 * @method static run()
 */
class RpcServer
{
    protected static function getFacadeAccessor(): string
    {
        return 'RpcServer';
    }
}
