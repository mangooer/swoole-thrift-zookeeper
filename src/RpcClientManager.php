<?php

namespace Mongooer\SwooleThriftZookeeper;

use Illuminate\Config\Repository;
use Mongooer\SwooleThriftZookeeper\Library\Rpc\Client\RpcClient;

class RpcClientManager
{
    /**
     * @var array|mixed
     */
    private $config;
    private $client;

    public function __construct(Repository $config)
    {
        $this->config = $config->get('swoole_thrift_zookeeper');
    }

    public function getClient(string $serviceName)
    {
        if (!isset($this->config["client"][$serviceName])) {
            throw new \RuntimeException("rpc client " . $serviceName . " is not exists");
        }
        if (!isset($this->client[$serviceName])) {
            $config = $this->config["client"][$serviceName];
            if (!$config["zookeeper_channel"]) {
                throw new \RuntimeException("rpc client zookeeper_channel can not empty");
            }
            if (!$config["path"]) {
                throw new \RuntimeException("rpc client path can not empty");
            }
            $this->client[$serviceName] = new RpcClient($serviceName, $config["zookeeper_channel"], $config["path"]);
        }
        return $this->client[$serviceName];

    }

    /**
     * 动态将方法传递给默认数据
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->$method(...$parameters);
    }
}
