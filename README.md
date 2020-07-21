# laravel-json-api

主要是为了统一前后端 restful api 的返回格式标准，按照 https://jsonapi.org 定义返回指定的数据格式。

最好结合前端库 [json-api-fetch](https://git.code.oa.com/slugteam/json-api-fetch) 一起使用。

## 安装

### 从composer安装

```bash
composer require gzhpackages/laravel-json-api
```

### 添加service provider

#### in laravel:

在 `config/app.php` 中的 `providers` 添加
```
'providers' => [
    ...
    GzhPackages\JsonApi\Providers\LaravelServiceProvider::class,
]
```

#### in lumen:

在 `bootstrap/app.php` 中添加

```
$app->register(GzhPackages\JsonApi\Providers\LaravelServiceProvider::class);
```

### 发布配置

php artisan vendor:publish --provider "GzhPackages\JsonApi\Providers\LaravelServiceProvider"

## 使用

### Controller

添加一个 `trait` 给response，`ApiHelper`包含了4个常用的返回值和status code类型。

分别为 `content`, `noContent`, `created`, `accepted`

```php
namespace App\Http\Controllers;

use GzhPackages\JsonApi\Traits\ApiHelper;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ApiHelper;

    public function show()
    {
        // do something...
        return $this->content(['foo' => 'bar']);        
    }
    
    public function create()
    {
        // do something...
        return $this->created();
    }
}
```

### Exception

1、重写 `app/Exceptions/Handler.php` 的`render`方法
```php
function render($request, Exception $exception)
    {
        $rendered = parent::render($request, $exception);

        if (config('app.debug')) {
            return $rendered;
        }

        $status = $rendered->getStatusCode();
        $json = [
            'error' => array_merge(config('errors.server_error'), [
                'title' => 'SERVER_ERROR',
            ]),
        ];
        
        /* 捕获自定义错误 */
        if ($exception instanceOf \GzhPackages\JsonApi\Exceptions\BaseApiException) {
            $json = $exception->toArray();
            $status = $exception->getStatus();
        }
        
        /* 自定义内置的错误，统一返回json */
        if ($exception instanceOf \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $json = [
                'error' => array_merge(config('errors.not_found'), [
                    'title' => 'NOT_FOUND',
                ]),
            ];           
            $status = 404;
        }

        if ($exception instanceOf \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            $json = [
                'error' => array_merge(config('errors.method_not_allowed'), [
                    'title' => 'METHOD_NOT_ALLOWED',
                ]),
            ];    
            $status = 405;
        }

        return response()->json($json, $status);
    }
```

2、命令行创建一个新的 Exception 类

```bash
php artisan make:api-exception NotFoundException
```

3、根据标准修改 http 状态码

```php
class NotFoundException extends BaseApiException
{
    protected $status = 404;
}
```

4、在 `config/errors.php` 中配置错误详情

在字段**detail**中，可以用大括号 `{}` 占位符

```
'not_found_user' => [
    'code'   => 10003,
    'detail' => '未找到该用户 {name}',
],
```


5、抛出自定义异常

第二个参数为 **detail** 占位符替换的对象，如果自定义的**detail**中无占位符，第二个参数可为空
```php
throw new NotFoundException('not_found_user', ['name' => 'xiaoming']);
```

自定义meta信息：

```php
$exception = new NotFoundException('not_found_user');
$exception->withMeta(['language' => 'en']);
throw $exception;
```