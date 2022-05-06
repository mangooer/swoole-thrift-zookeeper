# swoole-thrift-zookeeper
## 简介
根据学院君的教程，自己整理了一下，做了一个简陋的微服务整合包
## 使用说明
###1.安装  
 `mongooer/swoole-thrift-zookeeper`  
###2.发布配置文件  
 `php artisan vendor:publish --provider="Mongooer\SwooleThriftZookeeper\Providers\RpcServerProvider"`  
###3.配置文件解读  
1.zookeeper注册中心
```
    /*
    |--------------------------------------------------------------------------
    | zookeeper 注册中心
    |--------------------------------------------------------------------------
    */
    "zookeeper_channel" => [
        "default" => [ //通道名称
            "host" => "", //zookeeper地址 ip:port
            "callback" => null, //回调
            "timeout" => 10000 //超时时间
        ],
    ],
```
2.RPC服务端
```
  /*
    |--------------------------------------------------------------------------
    | 服务端
    |--------------------------------------------------------------------------
    */
    "server" => [
        "host" => "127.0.0.1", //swoole服务端启动的地址，默认本地
        "port" => "9099", //swoole服务端服务启动端口
        "node" => [ //需要向zookeeper注册的节点列表
            [
                "name" => "userService",//节点名称
                "path" => "/user",//注册进zookeeper的路径
                "processor" => \App\Thrift\Test\TestProcessor::class,//thrift 生成的 processor类命名空间
                "service" => \App\Services\DemoService::class,//实现接口的服务类命名空间
                "zookeeper_channel" => "default", //走哪个注册中心通道
            ],
        ]
    ],
```
3.RPC客户端
```
    /*
    |--------------------------------------------------------------------------
    | 客户端
    |--------------------------------------------------------------------------
    */
    "client" => [
        "userService" => [ //需要请求的服务端的节点名称
            "path" => "/user",//需要请求的服务端的路径
            "zookeeper_channel" => "default"//走哪个注册中心通道获取服务端
        ]
    ]
```
###4.使用   
- 1.在你的环境中安装 Thrift ，安装在哪里无所谓，我们只用它生成的文件，注意最好注册进环境变量方便命令行使用。
- 2.随便找个地方新建文件夹，在文件夹下新建Thrift文件，里面定义微服务所需要的接口。例如：test.thrift。
```
    namespace php app.Domain.Demo.Thrift.Test
    
    //namespace 这行首先定义了语言是php 
    //后面的app.Domain.Demo.Thrift.Test 则是这个文件的命名空间，
    //Thrift会根据这个命名空间从同级目录开始找，并在相应目录下创生成代码文件
    //所以最好写成我们项目的命名空间，复制时候省的改了
    //注意命名空间大小写问题
    
    // 定义接口
    service Test {
        string getInfo(1:i32 id)
        string updateInfo(1:i32 id)
    }
```
- 3.在当前目录运行命令生成代码，其中xxxxProcessor是服务端配置里需要的文件。xxxxClient是客户端代码，使用时会用到。
  `thrift -r --gen php:server -out ./ Thrift/test.thrift`  
- 4.将生成的代码复制进你的项目我一般会放在 app/Thrift 文件夹下，如果是是分模块的项目就放在对应模块目录下Thrift文件夹下
- 5.根据生成代码所在路径完善配置文件
- 6.开启服务端
  `RpcServer::run();`
- 7.客户端使用
```
    $rpcClient = RpcClient::getClient("userService");//通过配置文件里的名字获取客户端
    $thriftProtocol = $rpcClient->getTMultiplexedProtocol();//获取thriftProtocol
    $client = new TestClient($thriftProtocol);//生成的客户端代码
    $rpcClient->open();//开启
    $result = $client->getInfo(1010);//请求并获得返回
    $rpcClient->close();//关闭
```
