<?php

namespace Mongooer\SwooleThriftZookeeper\Library\Rpc\Server;

use Mongooer\SwooleThriftZookeeper\Library\Rpc\Server\Exception\RpcServiceBuildException;

class ServerBuilder
{
    /**
     * @var string
     */
    private $host;
    /**
     * @var string
     */
    private $port;

    /**
     * @var array ServerNode 节点列表
     */
    private $nodeList = [];

    /**
     * @var array 节点使用的Zookeeper通道列表
     */
    private $zookeeperChannelList = [];

    /**
     * @param string $host
     * @param string $port
     * @throws RpcServiceBuildException
     */
    public function __construct(string $host, string $port)
    {
        if (!$host) {
            throw new RpcServiceBuildException("RPC server host cannot empty!");
        }
        if (!$port) {
            throw new RpcServiceBuildException("RPC server port cannot empty!");
        }
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @param string $name
     * @param string $path
     * @param string $processor
     * @param string $service
     * @param string $zookeeperChannel
     * @return void
     * @throws RpcServiceBuildException
     */
    public function registerNode(string $name, string $path, string $processor, string $service, string $zookeeperChannel)
    {
        if (isset($this->nodeList[$name])) {
            throw new RpcServiceBuildException("{$name} has been registered");
        }
        $this->nodeList[$name] = new ServerNode($name, $path, $processor, $service, $zookeeperChannel);
        if (!in_array($zookeeperChannel, $this->zookeeperChannelList)) {
            $this->zookeeperChannelList[] = $zookeeperChannel;
        }
    }

    /**
     * @return array
     */
    public function getNodeList(): array
    {
        return $this->nodeList;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return array
     */
    public function getZookeeperChannelList(): array
    {
        return $this->zookeeperChannelList;
    }
}
