<?php

namespace Mongooer\SwooleThriftZookeeper\Library\Zookeeper;


interface ZookeeperInterface
{
    /**
     *
     * @param string $zookeeperHost
     * @param callable|null $watcherCallback
     * @param int $timeout
     * @return void
     */
    public function connect(string $zookeeperHost, ?callable $watcherCallback = null, int $timeout = 10000);

    /**
     * @param string $node
     * @param string $contents
     * @param int|null $flags
     * @param array|null $acl
     * @return string|bool
     */
    public function create(string $node, string $contents, ?array $acl = null, ?int $flags = null): string;

    /**
     * @param string $node
     * @param callable|null $watcherCallback
     * @param array|null $stat
     * @param int $maxSize
     * @return string
     */
    public function get(string $node, callable $watcherCallback = null, array &$stat = null, int $maxSize = 0): string;

    /**
     * @param string $node
     * @param string $data
     * @return bool
     */
    public function set(string $node, string $data): bool;

    /**
     * @param string $node
     * @param callable|null $watcherCallback
     * @return bool
     */
    public function exists(string $node, ?callable $watcherCallback = null): bool;

    /**
     * @param string $node
     * @return bool
     */
    public function remove(string $node): bool;

    /**
     * @param string $node
     * @param callable|null $watcherCallback
     * @return string[]
     */
    public function getChildren(string $node, ?callable $watcherCallback = null): array;

    public function ensurePath(string $node): bool;
}
