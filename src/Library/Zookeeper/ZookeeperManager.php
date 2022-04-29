<?php

namespace Mongooer\SwooleThriftZookeeper\Library\Zookeeper;

use Illuminate\Config\Repository;

class ZookeeperManager
{
    /**
     * @var array|mixed
     */
    private $config;

    private $servers = [];

    public function __construct(Repository $config)
    {
        $this->config = $config->get('swoole_thrift_zookeeper');
    }

    public function getServer(string $channel = "default"): ZookeeperInterface
    {
        if (!isset($this->config["zookeeper_channel"][$channel])) {
            throw new \RuntimeException("channel " . $channel . " is not exists");
        }
        if (!isset($this->servers[$channel])) {
            $config = $this->config["zookeeper_channel"][$channel];
            $this->servers[$channel] = new Zookeeper($config["host"], $config["callback"], $config["timeout"]);
        }
        return $this->servers[$channel];

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
