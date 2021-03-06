<?php
namespace Mongooer\SwooleThriftZookeeper\Library\Swoole;

use Thrift\Exception\TTransportException;
use Thrift\Server\TServerTransport;
use Swoole\Server as SwooleServer;
use Thrift\Transport\TTransport;

class ServerTransport extends TServerTransport
{
    /**
     * @var array 服务器选项
     */
    public $options = [
        'worker_num' => 1,
        'dispatch_mode' => 1, //1: 轮循, 3: 争抢
        'open_length_check' => true, //打开包长检测
        'package_max_length' => 8192000, //最大的请求包长度,8M
        'package_length_type' => 'N', //长度的类型，参见PHP的pack函数
        'package_length_offset' => 0,   //第N个字节是包长度的值
        'package_body_offset' => 4,   //从第几个字节计算长度
    ];

    /**
     * @var SwooleServer
     */
    public $server;

    /**
     * SwooleServerTransport constructor.
     * @param $host
     * @param int $port
     * @param int $mode
     * @param int $sockType
     * @param array $options
     */
    public function __construct($host, int $port = 9999, int $mode = SWOOLE_PROCESS, int $sockType = SWOOLE_SOCK_TCP, array $options = [])
    {
        $this->server = new SwooleServer($host, $port, $mode, $sockType);
        $options = array_merge($this->options, $options);
        $this->server->set($options);
    }
    /**
     * List for new clients
     *
     * @return void
     * @throws TTransportException
     */
    public function listen()
    {
        if (!$this->server->start()) {
            throw new TTransportException('Swoole ServerTransport start failed.', TTransportException::UNKNOWN);
        }
    }
    /**
     * Close the server
     *
     * @return void
     */
    public function close()
    {
        $this->server->shutdown();
    }
    /**
     * Swoole服务端通过回调函数获取请求，不可以调用accept方法
     * @return TTransport
     */
    protected function acceptImpl(): ?TTransport
    {
        return null;
    }
}
