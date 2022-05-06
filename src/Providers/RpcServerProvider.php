<?php
namespace Mongooer\SwooleThriftZookeeper\Providers;
use Illuminate\Support\ServiceProvider;
use Mongooer\SwooleThriftZookeeper\RpcClientManager;

class RpcServerProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // 发布配置文件
        $this->publishes([
            __DIR__ . '/../../config/swoole_thrift_zookeeper.php' => config_path('swoole_thrift_zookeeper.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('RpcServer', function ($app) {
            return new \Mongooer\SwooleThriftZookeeper\RpcServerRunner($app['config']);
        });
        $this->app->singleton('ZookeeperRpcCenter', function ($app) {
            return new \Mongooer\SwooleThriftZookeeper\Library\Zookeeper\ZookeeperManager($app['config']);
        });
        $this->app->singleton('RpcClient', function ($app) {
            return new RpcClientManager($app['config']);
        });
    }
}
