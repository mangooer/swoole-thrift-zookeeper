<?php

namespace Mongooer\SwooleThriftZookeeper\Facades;

use Illuminate\Support\Facades\Facade;
use Mongooer\SwooleThriftZookeeper\Library\Rpc\Client\RpcClient as SwooleRpcClient;
/**
 * @method static SwooleRpcClient getClient(string $serviceName)
 */

class RpcClient extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'RpcClient';
    }
}
