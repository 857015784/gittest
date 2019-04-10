<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/3/30
 * Time: 10:18
 */

namespace components;


class CacheAgent
{

    public $cache = '';
    public $class = '';
    public $site = '';
    const CACHE_TYPE_REDIS = 'Redis';

    public function __construct($cache = self::CACHE_TYPE_REDIS, $site = '')
    {
        $this->site  = strtoupper($site);
        $method      = '\\components\\' . $cache . '::getInstance';
        $this->cache = call_user_func_array($method, array());
    }

    public function exists($key){
        $key = $this->getKey($key);
        return call_user_func_array(array($this->cache, 'exists'), array($key));
    }

    public function __call($func, $arguments)
    {
        $arguments[0] = $this->getKey($arguments[0]);
        return call_user_func_array(array($this->cache, $func), $arguments);
    }
    public function setSite($site)
    {
        $this->site = $site;
        return $this;
    }

    public function getKey($key)
    {
        if(empty($this->site))
            return $key;
        else
            return $this->site . ':' . $key;
    }
}