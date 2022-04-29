<?php

namespace Mongooer\SwooleThriftZookeeper\Library\Zookeeper;

class Zookeeper implements ZookeeperInterface
{

    /**
     * @var ?\Zookeeper
     */
    private $server;

    public function __construct(string $zookeeperHost, ?callable $watcherCallback = null, int $timeout = 10000)
    {
        $this->connect($zookeeperHost, $watcherCallback, $timeout);
    }

    public function connect(string $zookeeperHost, ?callable $watcherCallback = null, int $timeout = 10000)
    {
        $this->server = new \Zookeeper($zookeeperHost, $watcherCallback, $timeout);
    }

    public function create(string $node, string $contents, ?array $acl = null, ?int $flags = null): string
    {
        return $this->server->create($node, $contents, $acl, $flags);
    }

    public function get(string $node, callable $watcherCallback = null, array &$stat = null, int $maxSize = 0): string
    {
        return $this->server->get($node, $watcherCallback, $stat, $maxSize);
    }

    public function set(string $node, string $data): bool
    {
        return $this->server->set($node, $data);
    }

    public function exists(string $node, ?callable $watcherCallback = null): bool
    {
        return $this->server->exists($node, $watcherCallback);
    }

    public function remove(string $node): bool
    {
        return $this->server->remove($node);
    }

    public function getChildren(string $node, ?callable $watcherCallback = null): array
    {
        return $this->server->getChildren($node, $watcherCallback);
    }

    public function ensurePath(string $node): bool
    {
        return $this->server->ensurePath($node);
    }
}
