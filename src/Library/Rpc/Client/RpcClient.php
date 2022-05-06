<?php

namespace Mongooer\SwooleThriftZookeeper\Library\Rpc\Client;

use Mongooer\SwooleThriftZookeeper\Facades\ZookeeperRpcCenter;
use Mongooer\SwooleThriftZookeeper\Library\Swoole\ClientTransport;
use Mongooer\SwooleThriftZookeeper\Library\Zookeeper\ZookeeperInterface;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TMultiplexedProtocol;
use Thrift\Transport\TFramedTransport;

class RpcClient
{
    /**
     * @var string
     */
    private $serviceName;
    /**
     * @var string
     */
    private $zookeeperChannel;
    /**
     * @var string
     */
    private $path;
    /**
     * @var ZookeeperInterface
     */
    private $zookeeperServer;
    /**
     * @var ClientTransport
     */
    private $clientTransport;
    /**
     * @var TFramedTransport
     */
    private $tFramedTransport;
    /**
     * @var TBinaryProtocol
     */
    private $tBinaryProtocol;
    /**
     * @var TMultiplexedProtocol
     */
    private $tMultiplexedProtocol;

    public function __construct(string $serviceName, string $zookeeperChannel, string $path)
    {
        $this->serviceName = $serviceName;
        $this->zookeeperChannel = $zookeeperChannel;
        $this->path = $path;
        $this->init();
    }

    private function init()
    {
        $this->setZookeeperServer();
        $this->setClientTransport();
        $this->setTFramedTransport();
        $this->setTBinaryProtocol();
        $this->setTMultiplexedProtocol();
    }

    public function open()
    {
        $this->tFramedTransport->open();
    }

    public function close()
    {
        $this->tFramedTransport->close();
    }

    public function getTMultiplexedProtocol(): TMultiplexedProtocol
    {
        return $this->tMultiplexedProtocol;
    }

    private function setZookeeperServer()
    {
        $this->zookeeperServer = ZookeeperRpcCenter::getServer($this->zookeeperChannel);
    }

    private function setClientTransport()
    {
        $ipPort = $this->getNode($this->path);
        $this->clientTransport = new ClientTransport($ipPort["ip"], $ipPort["port"]);
    }

    private function setTFramedTransport()
    {
        $this->tFramedTransport = new TFramedTransport($this->clientTransport);
    }

    private function getNode(string $nodePath)
    {
        if (!$this->zookeeperServer->exists($nodePath)) {
            exit('对应的服务节点尚未在 ZK 注册');
        }
        $nodes = $this->zookeeperServer->get($nodePath);
        if (!$nodes) {
            exit('对应的服务节点尚未在 ZK 注册');
        }
        // 从服务节点列表中随机获取一个节点
        $nodes = json_decode($nodes, true);
        $node = $nodes[array_rand($nodes)];
        list($ip, $port) = explode(':', $node);
        return [
            "ip" => $ip,
            "port" => $port,
        ];
    }

    private function setTBinaryProtocol()
    {
        $this->tBinaryProtocol = new TBinaryProtocol($this->tFramedTransport);
    }

    private function setTMultiplexedProtocol()
    {
        $this->tMultiplexedProtocol = new TMultiplexedProtocol($this->tBinaryProtocol, $this->serviceName);
    }


}
