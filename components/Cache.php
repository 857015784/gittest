<?php
/**
 * 缓存回调实现,数据压缩
 * User: wanghuabin
 * Date: 2019/3/29
 * Time: 19:15
 */

namespace components;

use think\Config;

/**
 * Class WCache
 * 缓存回调实现,数据压缩
 */

class Cache
{
    const TYPE_STRING = 1;

    const TYPE_SET = 2;

    //大于 COMPRESS_LEN+4 才会自动执行压缩
    static $compressLen = 10000;

    //缓存失效时间3小时
    static $expireTime = 60 * 60 * 2;

    const  TEST = 'test';


    //回调方法配置 string型缓存 type 选填
    static $keyMap = [
        self::TEST              => ['class' => '\\app\\index\\model\\Comment', 'type' => self::TYPE_STRING],

    ];
    public function __construct()
    {

        if( ENV == 'product' ){
            \think\Config::load(APP_PATH.'/config.php');
        }else{
            \think\Config::load(APP_PATH.'/config.dev.php');
        }
    }

    /**
     * 普通value 结构回调处理
     * @param string $key
     * @param array $params
     * @return bool|mixed|string
     * @throws \Exception
     * Author: wanghuabin
     * Time: 2019/3/30   10:39
     */
    public static function get(string $key, array $params = array())
    {
        static $datas = [];

        $cacheKey = self::getKey($key, $params);

        if(isset($datas[$cacheKey])){
            //file_put_contents(ROOT_PATH.'/runtime/logs/static',$cacheKey.':'.date('y-m-d H:i:s').PHP_EOL,FILE_APPEND);
            return $datas[$cacheKey];
        }

        $cache = new CacheAgent();
        $data  = $cache->get($cacheKey );
        //兼容不同版本的redis
        if ($data!==null && $data!==false) {
            //取出操作位
            $operate = substr($data, 0, 3);
            $data    = substr($data, 4);

            list($isCompress, $isArray) = explode(':', $operate);

            $isCompress && $data = gzuncompress($data);
            $isArray && $data = json_decode($data,true);

        } else {
            $data = static::reset($key, $params);
        }
        $datas[$cacheKey] = $data;
        return $data;
    }

    /**
     * 重置缓存
     * @param $key
     * @param array $params
     * @return mixed
     * @throws \Exception
     * Author: wanghuabin
     * Time: 2019/3/30   10:39
     */
    public static function reset($key, $params = array())
    {
        if(!isset(static::$keyMap[$key])){
            throw new \Exception("{$key}缓存不存在");
        }

        $class     = new static::$keyMap[$key]['class'];
        $method    = $key . 'Source';
        $data      = call_user_func_array(array($class, $method), $params);
        self::set($key, $params, $data);
        return $data;

    }

    /**
     * string 型存储格式 (是压缩):(是数组):数据
     * @param $key
     * @param array $params
     * @param $data
     * @return mixed
     * Author: wanghuabin
     * Time: 2019/3/30   10:39
     */
    public static function set($key, $params = array(), $data)
    {

        $cacheMethod = '';
        $cache       = new CacheAgent();
        $cacheKey         = self::getKey($key, $params);

        $type       = isset(static::$keyMap[$key]['type']) ? static::$keyMap[$key]['type'] : self::TYPE_STRING;
        $expireTime = isset(static::$keyMap[$key]['expire']) ? static::$keyMap[$key]['expire'] : self::$expireTime;

        switch ($type) {
            case self::TYPE_STRING:
                $isArray    = 0;
                $isCompress = 0;

                //数据需要转换为json存储
                if (is_array($data)) {
                    $isArray = 1;
                    $data    = json_encode($data);
                }

                //过大的数据需要压缩
                if (strlen($data) > self::$compressLen) {
                    $isCompress = 1;
                    $data       = gzcompress($data);
                }

                $data = sprintf('%d:%d:%s', $isCompress, $isArray, $data);

                $cacheMethod = 'set';
        }
        if ($cacheMethod != '') {
            return call_user_func_array(array($cache, $cacheMethod), [$cacheKey, $data, $expireTime]);
        }

    }

    /**
     * 获取缓存key
     * @param string $key
     * @param array $group
     * @return string
     * Author: wanghuabin
     * Time: 2019/3/30   10:39
     */
    public static  function getKey(string $key, array $group = []): string
    {


        ksort($group);
        foreach($group as $col=>$item){
            $group[$col]= strtolower((string)$item);
        }


        $groupKey = '';

        if(count($group)>0){
            $groupKey = ':'.implode(':', $group);
        }
        return Config::get('CachePrefix').':'.$key . $groupKey;
    }

    public static function del(string $key, array $params = []){
        $key = self::getKey($key, $params);
        $cache = new CacheAgent();
        return $cache->delete($key);
    }

    public static function delByKey(string $key){

        $cache = new CacheAgent();
        return $cache->delete($key);
    }

    public  static function rpush(string $key,string $value,array $group = []){

        $key = self::getKey($key, $group);
        $cache = new CacheAgent();
        return $cache->rpush($key,$value);
    }

    public static function lpop(string $key,array $group = []){

        $key = self::getKey($key, $group);
        $cache = new CacheAgent();
        return $cache->lpop($key);
    }

    public static  function initCache(){
        if(empty(self::$cache)){
            self::$cache = new CacheAgent();
        }
    }

    public static function  newCache(){
        \components\Redis::newRedis();
    }
}
