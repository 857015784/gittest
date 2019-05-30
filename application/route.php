<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

Route::get('/', 'index/Index/index');
Route::get('test', 'index/Index/test');
Route::get('pushmq', 'index/Index/pushmq');//推送mq
Route::get('callback', 'index/Index/callback');//消费mq
Route::get('cache', 'index/Index/cache');//缓存回调
Route::get('sphinx', 'index/Index/sphinx');//缓存回调
Route::get('celue', 'index/Index/celue');//策略模式
Route::get('facade', 'index/Index/facade');//外观模式
Route::get('factory', 'index/Index/factory');//工厂模式
Route::get('guzzle', 'index/Index/guzzle');//GuzzleHttps示例 ---替代curl
Route::get('/msg/test', 'index/Msg/test');
