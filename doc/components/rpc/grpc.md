# gRPC 服务开发

[toc]

## 介绍

在 imi 框架中集成 gRPC 服务开发、客户端调用及连接池。

通讯协议为二进制的 Protobuf。

Github: <https://github.com/imiphp/imi-grpc>

> 仅支持 Swoole

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-grpc": "~2.1.0"
    }
}
```

然后执行 `composer update` 安装。

---

你也可以直接创建 imi gRPC 骨架项目：`composer create-project imiphp/project-grpc:~2.1.0`

## 使用说明

可以参考 <https://github.com/imiphp/imi-grpc/tree/master/example> 目录示例，包括完整的服务端和客户端调用。

### 服务和消息格式定义

gRPC 和 Protobuf 是黄金搭档，Protobuf 是用来做通讯消息格式的序列化和反序列化的工作。

gRPC 通讯有请求（Request）消息和响应（Response）消息，从请求消息中获取请求参数，通过响应消息返回给客户端。

定义参考：`example/grpc/grpc.proto`

```proto
syntax = "proto3";

package grpc;
option php_generic_services = true;

service AuthService {
    rpc Login (LoginRequest) returns (LoginResponse);
}

message LoginRequest {
    string phone = 1;       // 手机号
    string password = 2;    // 密码
}

message LoginResponse {
    bool success = 1;       // 是否成功
    string error = 2;       // 错误信息
}
```

定义好后，通过命令生成 PHP 文件：`protoc --php_out=./ grpc.proto`

protoc 下载和安装：<https://github.com/protocolbuffers/protobuf/releases>

生成好文件后，要把生成出来文件所在命名空间，配置到 `composer.json`，例：

```js
"autoload": {
    "psr-4": {
        "Grpc\\": "grpc/Grpc",
        "GPBMetadata\\": "grpc/GPBMetadata",
    }
},
```

### 服务端

`config.php` 配置忽略目录

```php
'ignorePaths' => [
    \dirname(__DIR__) . \DIRECTORY_SEPARATOR . 'grpc',
],
```

如果你用主服务器：

```php
[
    // 主服务器配置
    'mainServer'    =>  [
        'namespace' =>    'ImiApp\GrpcServer',
        'type'      =>    'GrpcServer',
        'host'      =>    '127.0.0.1',
        'port'      =>    8080,
    ],
]
```

如果你用子服务器：

```php
[
    // 子服务器（端口监听）配置
    'subServers'    =>  [
        // 子服务器名
        'XXX'   =>  [
            'namespace' =>    'ImiApp\GrpcServer',
            'type'      =>    'GrpcServer',
            'host'      =>    '127.0.0.1',
            'port'      =>    8080,
        ]
    ],
]
```

#### 控制器

写法与 Http Api 几乎一致。

标准 gRPC Url 格式为：`http://host:port/{package}.{service}/{method}`

```php
/**
 * @Controller("/grpc.AuthService/")
 */
class AuthServiceController extends HttpController implements AuthServiceInterface
{
    /**
     * Method <code>login</code>
     *
     * @Action
     * 
     * @param \Grpc\LoginRequest $request
     * @return \Grpc\LoginResponse
     */
    public function login(\Grpc\LoginRequest $request)
    {
        $response = new LoginResponse;
        $success = '12345678901' === $request->getPhone() && '123456' === $request->getPassword();
        $response->setSuccess($success);
        $response->setError($success ? '' : '登录失败');
        return $response;
    }

}
```

### 客户端

#### 连接池配置

```php
[
    // 连接池配置
    'pools'    =>    [
        'grpc'  =>  [
            'pool'    =>    [
                'class'        =>    \Imi\Rpc\Client\Pool\RpcClientCoroutinePool::class,
                'config'    =>    [
                    // 根据实际情况设置
                    'maxResources'  =>    100,
                    'minResources'  =>    1,
                ],
            ],
            'resource'    =>    [
                // 这里需要和你的服务端路由一致
                'url'           =>  'http://127.0.0.1:8080/{package}.{service}/{name}',
                // 'url'           =>  'http://127.0.0.1:8080/{package}.{service}/{name|ucfirst}', // 参数支持设定函数处理，比如这个将方法名首字母大写，兼容其它部分语言
                'clientClass'   =>  \Imi\Grpc\Client\GrpcClient::class,
                'method'        =>  'POST', // 指定请求方式，默认 GET
            ],
        ],
    ],
    'rpc'   =>  [
        'defaultPool'   =>  'grpc',
    ],
]
```

### 客户端调用

代码调用：

```php
// $service = \Imi\Rpc\Client\Pool\RpcClientPool::getInstance('连接池名')->getService('服务名', '生成出来的服务接口类名');
$service = \Imi\Rpc\Client\Pool\RpcClientPool::getInstance()->getService('grpc.AuthService', \Grpc\AuthServiceInterface::class);
$request = new \Grpc\LoginRequest;
$request->setPhone('');
$service->login($request);
```

注解调用：

```php
use Imi\Rpc\Annotation\RpcClient;
use Imi\Grpc\Client\Annotation\GrpcService;

class Test
{
    /**
     * @RpcClient()
     *
     * @var \Imi\Rpc\Client\IRpcClient
     */
    protected $rpcClient;

    /**
     * @GrpcService(serviceName="grpc.AuthService", interface=\Grpc\AuthServiceInterface::class)
     *
     * @var \Grpc\AuthServiceInterface
     */
    protected $authService;

    public function aaa()
    {
        $request = new \Grpc\LoginRequest;
        $request->setPhone('');

        // 方法一
        $this->rpcClient->getService('服务名', '生成出来的服务接口类名')->方法名($request);

        // 方法二
        $this->xxxRpc->方法名($request);
    }
}
```

`@GrpcService` 注解的 `serviceName` 属性格式为 `{package}.{service}`；
`interface` 属性是生成出来的服务接口类名

**↓↓↓注意↓↓↓：**

> 使用 `@GrpcService` 注解注入时，如果调用的 `grpc` 接口方法名是：`getName`、`send`、`recv`、`call`、`getClient`，请使用 `call` 方法来调用，因为这和内置方法名相冲突了。
