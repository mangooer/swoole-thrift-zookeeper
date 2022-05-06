<?php

namespace Mongooer\SwooleThriftZookeeper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static run()
 */
class RpcServer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'RpcServer';
    }
}
