<?php

namespace Mongooer\SwooleThriftZookeeper;

use Illuminate\Config\Repository;
use Mongooer\SwooleThriftZookeeper\Facades\ZookeeperRpcCenter;
use Mongooer\SwooleThriftZookeeper\Library\Rpc\Server\ServerBuilder;
use Mongooer\SwooleThriftZookeeper\Library\Swoole\Exception\SwooleRpcServerException;
use Mongooer\SwooleThriftZookeeper\Library\Swoole\Server;
use Mongooer\SwooleThriftZookeeper\Library\Swoole\ServerTransport;
use Mongooer\SwooleThriftZookeeper\Library\Swoole\TFramedTransportFactory;
use Thrift\Exception\TException;
use Thrift\Factory\TBinaryProtocolFactory;
use Thrift\TMultiplexedProcessor;
use Zookeeper;

class RpcServerRunner
{
    /**
     * @var array|mixed
     */
    private $config;

    public function __construct(Repository $config)
    {
        $this->config = $config->get('swoole_thrift_zookeeper');
    }

    /**
     * @return void
     * @throws Library\Rpc\Server\Exception\RpcServiceBuildException|SwooleRpcServerException
     */
    public function run()
    {
        //实例化serverBuilder
        $serverConfig = $this->config["server"];
        $rpcServerBuilder = new ServerBuilder($serverConfig["host"], $serverConfig["port"]);
        //注册节点
        foreach ($serverConfig["node"] as $nodeConfig) {
            $rpcServerBuilder->registerNode(
                $nodeConfig["name"],
                $nodeConfig["path"],
                $nodeConfig["processor"],
                $nodeConfig["service"],
                $nodeConfig["zookeeper_channel"]
            );
        }

        try {
            //将每个节点注册进zookeeper
            foreach ($rpcServerBuilder->getNodeList() as $node) {
                $zkServer = ZookeeperRpcCenter::getServer($node->getZookeeperChannel());
                if (!$zkServer->exists($node->getPath())) {
                    $zkServer->create($node->getPath(), null, [[
                        'perms' => Zookeeper::PERM_ALL,
                        'scheme' => 'world',
                        'id' => 'anyone',
                    ]]);
                }
                $nodes = $zkServer->get($node->getPath());
                if (!$nodes) {
                    $nodes = [$rpcServerBuilder->getHost() . ":" . $rpcServerBuilder->getPort()];
                } else {
                    $nodes = json_decode($nodes, true);
                    $nodes[] = $rpcServerBuilder->getHost() . ":" . $rpcServerBuilder->getPort();
                }
                $zkServer->set($node->getPath(), json_encode(array_unique($nodes)));
            }
            //将节点注册到swoole 服务
            $multiProcessor = new TMultiplexedProcessor();
            $tFactory = new TFramedTransportFactory();
            $pFactory = new TBinaryProtocolFactory();
            foreach ($rpcServerBuilder->getNodeList() as $node) {
                $processorClass = $node->getProcessor();
                $serviceClass = $node->getService();
                $processor = new $processorClass(new $serviceClass);
                $multiProcessor->registerProcessor($node->getName(), $processor);
            }
            $transport = new ServerTransport($rpcServerBuilder->getHost(), $rpcServerBuilder->getPort());
            $server = new Server($multiProcessor, $transport, $tFactory, $tFactory, $pFactory, $pFactory);
            $server->serve();
        } catch (TException $exception) {
            foreach ($rpcServerBuilder->getNodeList() as $node) {
                $zkServer = ZookeeperRpcCenter::getServer($node->getZookeeperChannel());
                if ($zkServer->exists($node->getPath())) {
                    $zkServer->remove($node->getPath());
                }
            }
            throw new SwooleRpcServerException("swoole服务启动失败");

        }

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
