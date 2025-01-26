<?php

namespace Http;

use Core\LogManager;

class RouterController
{
    private $logger;
    private $routes = [];

    public function __construct()
    {
        $this->logger = new LogManager();
        // 注册 API 路由
        $this->routes['/api/config'] = [
            'GET' => [ApiController::class, 'getConfig'],
            'POST' => [ApiController::class, 'updateConfig']
        ];
        $this->routes['/api/channel-urls'] = [
            'GET' => [ApiController::class, 'getChannelUrls']
        ];

        // 注册 M3U 和 TXT 路由
        $this->routes['#^/m3u(/\d+)?/?$#'] = [
            'GET' => [HttpController::class, 'm3u']
        ];
        $this->routes['#^/txt(/\d+)?/?$#'] = [
            'GET' => [HttpController::class, 'txt']
        ];

        // 注册跳转路由
        $this->routes['/jump.m3u8'] = [
            'GET' => [HttpController::class, 'jump']
        ];

        // 注册调试路由
        $this->routes['/debug'] = [
            'GET' => [HttpController::class, 'debug']
        ];
    }

    public function dispatch($method, $path, $query)
    {
        // 使用 LogManager 记录日志 方法 路径 参数
        $this->logger->info($method . ' ' . $path . ($query ? '?' . $query : ''));
        // 检查精确匹配的路由
        if (isset($this->routes[$path][$method])) {
            [$class, $action] = $this->routes[$path][$method];
            $controller = new $class();
            return $controller->$action();
        }

        // 检查正则匹配的路由
        foreach ($this->routes as $pattern => $handlers) {
            if ($pattern[0] === '#' && preg_match($pattern, $path, $matches)) {
                if (isset($handlers[$method])) {
                    [$class, $action] = $handlers[$method];
                    $controller = new $class();
                    $format = isset($matches[1]) ? trim($matches[1], '/') : '1';
                    return $controller->$action($format, $_GET);
                }
            }
        }

        return false; // 没有匹配的路由
    }
}
