<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
class CorsMiddleware {
    private $headers;
    private $allow_origin;

    public function handle(Request $request, \Closure $next)
    {
        $this->headers = [
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Accept, Authorization, X-Requested-With, Application',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => 1728000,
            'Access-Control-Allow-Origin' => '*',
        ];

        $this->allow_origin = [
            'http://localhost',
            'http://192.168.1.12:8080'
        ];
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

        //如果origin不在允许列表内，直接返回403
        if (!in_array($origin, $this->allow_origin) && !empty($origin))
            // return new Response('Forbidden', 403);


            if ($request->isMethod('options')) {
                return $this->setCorsHeaders(new Response('OK', 200), "*");
            }

        // 如果是简单请求或者非跨域请求，则照常设置header
        $response = $next($request);
        $methodVariable = array($response, 'header');

        //这个判断是因为在开启session全局中间件之后，频繁的报header方法不存在，所以加上这个判断，存在header方法时才进行header的设置
        if (is_callable($methodVariable, false, $callable_name)) {
            return $this->setCorsHeaders($response, $origin);
        }
        return $response;
    }

    /**
     * @param $response
     * @return mixed
     */
    public function setCorsHeaders($response, $origin)
    {
        foreach ($this->headers as $key => $value) {
            $response->header($key, $value);
        }

        if (in_array($origin, $this->allow_origin)) {
            $response->header('Access-Control-Allow-Origin', "*");
        } else {
            $response->header('Access-Control-Allow-Origin', '*');
        }
        return $response;
    }
}
