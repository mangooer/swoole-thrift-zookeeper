<?php

namespace Mongooer\SwooleThriftZookeeper\Library\Rpc\Server;

use Mongooer\SwooleThriftZookeeper\Library\Rpc\Server\Exception\RpcServiceBuildException;

class ServerNode
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $processor;
    /**
     * @var string
     */
    private $service;
    /**
     * @var string
     */
    private $zookeeperChannel;
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name, string $path, string $processor, string $service, string $zookeeperChannel)
    {
        $this->path = $path;
        if (!class_exists($processor)) {
            throw new RpcServiceBuildException("class {$processor} not found");
        }
        if (!class_exists($service)) {
            throw new RpcServiceBuildException("class {$service} not found");
        }
        $this->processor = $processor;
        $this->service = $service;
        $this->zookeeperChannel = $zookeeperChannel;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getProcessor(): string
    {
        return $this->processor;
    }

    /**
     * @return string
     */
    public function getZookeeperChannel(): string
    {
        return $this->zookeeperChannel;
    }

    /**
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
