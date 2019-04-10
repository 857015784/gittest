<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/3/30
 * Time: 10:19
 */

namespace components;

use Predis;

/**
 * 测试机连单台redis，线上连哨兵
 * Class Redis
 * @package app\components
 * @author Cesc.Caijinbin <caijinbin@globalegrow.com>  At 2018/9/30 9:37
 */
class Redis
{
    public $con = true;  //redis链接对象
    public $redis; //redis对象
    public $config;
    public $prefix = '';
    private static $instance = null;
    public function __construct()
    {
        if( ENV == 'product' ){
            $this->initRedisSentinels();
            \think\Config::load(APP_PATH.'/config.php');
        }else{
            $this->initRedisTest();
            \think\Config::load(APP_PATH.'/config.dev.php');
        }
    }

    /**
     * 获取redis对象单例
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public static function newRedis(){
        static::$instance = null;
    }

    public function initRedisTest() {
        $this->redis = new \Redis();

        $retry = 0;//重试次数
        $maxRetry = 3;//最多重试次数

        $config = \think\Config::get('RedisTestConfig');

        //print_r($config);exit;

        do{
            $Res = $this->redis->connect($config['server'], $config['port']);

            $authRes = true;
            if( !empty($config['password']) )
            {
                $authRes = $this->redis->auth($config['password']);
                if(!$authRes)
                {
                    $this->con = false;
                }
            }

            $database = 0;
            if(isset($config['database']) && $config['database'])
            {
                $database = $config['database'];
            }

            $this->redis->SELECT($database);

            $retry++;

        }while( !$Res && !$authRes && $retry <= $maxRetry );


        if(!$Res)
        {
            $this->con = false;
            return 'redis 链接失败';
        }
        return $this->redis;
    }

    /**
     * Redis哨兵实例
     */
    public function initRedisSentinels()
    {
        if(is_null($this->redis))
        {
            try
            {
                require_once dirname(__FILE__) . '/predis/autoload.php';

                $PRedisConfig = unserialize(\think\Config::get('RedisTestConfig'));

                $retry = 0;//重试次数
                $maxRetry = 3;//最多重试次数

                if ( !isset($PRedisConfig['sentinels']) || !isset($PRedisConfig['options']) ) {
                    die('CONNECT ERROR');
                }
                do{
                    $this->redis = new Predis\Client( $PRedisConfig['sentinels'], $PRedisConfig['options'] );

                    $authRes = true;


                    /*if( !empty($PRedisConfig['password']) )
                    {
                        $authRes = $this->redis->auth($PRedisConfig['password']);
                    }*/

                    $retry++;

                }while( !$this->redis && $retry < $maxRetry );

            } catch (Exception $ex){
                $error_log = date('Y-m-d H:i:s') . ' : ' . ' redis connect fail' . PHP_EOL;
                trigger_error($error_log);
            }
        }
        return $this->redis;
    }


    /**
     * 设置值
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param int $timeOut 时间
     */
    public function set($key, $value, $timeOut = 0)
    {
        $key = $this->prefix . $key;
        $value = serialize($value);

        if ($timeOut > 0) {
            $retRes = $this->redis->setex($key, $timeOut, $value);
        } else {
            $retRes = $this->redis->set($key, $value);
        }

        if (!$retRes) {
            return $this->error('redis 写入失败 操作set key:' . $key . ' value=' . $value);
        }

        return $retRes;
    }

    /**
     * 通过KEY获取数据
     * @param string $key KEY名称
     */
    public function get($key)
    {
        $key = $this->prefix . $key;
        $result = $this->redis->get($key);
        if (empty($result)) {
            return $result;
        }
        return unserialize($result);
    }

    /**
     * 通过序列化方式获取
     */
    public function serial_get($key)
    {
        if ($this->con) {
            $result = $this->redis->get($key);
            if (empty($result)) {
                return $result;
            }
            return unserialize($result);
        }

        return null;
    }

    /**
     * 通过序列化方式设置
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param int $timeOut 时间
     */
    public function serial_set($key, $value, $timeOut = 0)
    {
        $value = serialize($value);
        if ($this->con) {
            if ($timeOut > 0) {
                $retRes = $this->redis->setex($key, $timeOut, $value);
            } else {
                $retRes = $this->redis->set($key, $value);
            }
            if (!$retRes) {
                return $this->error('redis 写入失败 操作set key:' . $key . ' value=' . $value);
            }
        }
    }

    /**
     * 删除一条数据
     * @param string $key KEY名称
     */
    public function delete($key)
    {
        $key = $this->prefix . $key;
        if ($this->con) {
            return $this->redis->del($key);
        }

        return false;
    }

    /**
     * 数据入队列
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param bool $right 是否从右边开始入
     */
    public function push($key, $value, $right = true)
    {
        $value = json_encode($value);
        if ($right) {
            $Res = $this->redis->rpush($key, $value);
        } else {
            $Res = $this->redis->lpush($key, $value);
        }

        if (!$Res) {
            return $this->error('
}redis 写入失败 操作push key:' . $key . ' value=' . $value);
        }

        return $Res;
    }

    /**
     * list right push
     * @param $key
     * @param $value
     * @return mixed
     */
    public function rpush($key, $value)
    {
        $key = $this->prefix . $key;
        $result = $this->redis->rpush($key, $value);
        return $result;
    }

    /**
     * 查看list的长度
     * @param $key
     * @return bool
     */
    public function rawLlen($key)
    {
        $ret = false;
        if ($this->con) {
            $ret = $this->redis->llen($this->prefix . $key);
        }
        return $ret;
    }

    /**
     * redis入队列
     * @param $key
     * @param $value
     * @param $isJsonEncode  bool   是否要对$value进行json_encode
     * @return bool
     */
    public function rawRpush($key, $value, $isJsonEncode = true)
    {
        $ret = false;
        if ($isJsonEncode) {
            $value = json_encode($value);
        }
        if ($this->con) {
            $ret = $this->redis->rpush($this->prefix . $key, $value);
        }
        return $ret;
    }

    /**
     * redis出队列
     * @param $key
     * @param $isJsonDecode bool    是否要对获取的结果json_decode
     * @return bool
     */
    public function rawLpop($key, $isJsonDecode = true)
    {
        $ret = false;
        if ($this->con) {
            $ret = $this->redis->lpop($this->prefix . $key);
        }
        if (!empty($ret) && $isJsonDecode) {
            $ret = json_decode($ret, true);
        }
        return $ret;
    }

    /**
     * 数据出队列
     * @param string $key KEY名称
     * @param bool $left 是否从左边开始出数据
     */
    public function pop($key, $left = true)
    {
        $val = $left ? $this->redis->lpop($key) : $this->redis->rpop($key);

        return json_decode($val);
    }

    /**
     * list lpop
     * @param $key
     * @return mixed
     */
    public function lpop($key)
    {
        $key = $this->prefix . $key;
        $value = $this->redis->lpop($key);
        return $value;
    }

    /**
     * 数据自增
     * @param string $key KEY名称
     */
    public function increment($key)
    {
        $key = $this->prefix . $key;
        return $this->redis->incr($key);
    }

    /**
     * 数据自减
     * @param string $key KEY名称
     */
    public function decrement($key)
    {
        $key = $this->prefix . $key;
        return $this->redis->decr($key);
    }

    /**
     * 数据自增
     * @param string $key KEY名称
     * @param int $value 增加的值
     */
    public function incrby($key, $value)
    {
        if ($this->con) {
            return $this->redis->incrby($key, $value);
        }

        return null;
    }

    /**
     * key是否存在，存在返回ture
     * @param string $key KEY名称
     */
    public function exists($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * 获取多条列表值
     * @param string $key KEY名称
     * @param mixed $start
     * @param mixed $end
     */
    public function lRange($key, $start = 0, $end = -1)
    {
        return $this->redis->lrange($key, $start, $end);
    }

    /**
     * 获取list的长度
     * @param string $key KEY名称
     */
    public function llen($key)
    {
        return $this->redis->llen($key);
    }

    /**
     * 让列表只保留指定区间内的元素
     * @param $key
     * @param int $start
     * @param int $end
     * @return mixed
     */
    public function ltrim($key, $start = 0, $end = -1)
    {
        $key = $this->prefix . $key;
        return $this->redis->ltrim($key, $start, $end);
    }

    /**
     * 设置值
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param int $timeOut 时间
     */
    public function raw_set($key, $value, $timeOut = 0)
    {
        $retRes = false;
        if ($this->con) {
            if ($timeOut > 0) {
                $retRes = $this->redis->setex($key, $timeOut, $value);
            } else {
                $retRes = $this->redis->set($key, $value);
            }
            if (!$retRes) {
                return $this->error('redis 写入失败 操作set key:' . $key . ' value=' . $value);
            }
        }
        return $retRes;
    }

    /**
     * 通过KEY获取数据
     * @param string $key KEY名称
     */
    public function raw_get($key)
    {
        $retRes = false;
        if ($this->con) {
            $retRes = $this->redis->get($key);
        }

        return $retRes;
    }

    private function error($msg = '')
    {
        if ($msg) {
            error_log($msg . ' ' . date('Y-m-d H:i:s') . "\r\n", 3, dirname(dirname(__FILE__)) . '/runtime/logs/rediserror.log');
        }
        $this->con = false;

        return false;
    }

    public function sAdd($key,$value){
        $retRes = false;
        if ($this->con) {
            $retRes = $this->redis->sAdd($this->prefix.$key,$value);
        }
        return $retRes;
    }

    public function sCard($key){
        $retRes = false;
        if ($this->con) {
            $retRes = $this->redis->sCard($this->prefix.$key);
        }
        return $retRes;
    }

    public function sMembers($key)
    {
        $retRes = [];
        if ($this->con) {
            $retRes = $this->redis->sMembers($this->prefix.$key);
        }
        return $retRes;
    }
    /**
     * 获取有序集合的指定范围元素
     * Two options are available: withscores => TRUE, and limit => array($offset, $count)
     */
    public function zRangeByScore($key,$start,$end,$options=[]){
        $retRes = [];
        if ($this->con) {
            $retRes = $this->redis->zRangeByScore($this->prefix.$key,$start,$end,$options);
        }
        return $retRes;
    }
    //添加元素到有序集合
    public function zAdd($key,$score,$value){
        $retRes = false;
        if ($this->con) {
            $retRes = $this->redis->zAdd($this->prefix.$key,$score,$value);
        }
        return $retRes;
    }
    //删除一个有序集合元素
    public function zrem($key,$value){
        $retRes = false;
        if ($this->con) {
            $retRes = $this->redis->zrem($this->prefix.$key,$value);
        }
        return $retRes;
    }
    //设置过期时间
    public function expire($key,$value){
        $retRes = false;
        if ($this->con) {
            $retRes = $this->redis->expire($this->prefix.$key,$value);
        }
        return $retRes;
    }

    public function sIsMember($key,$value)
    {
        $retRes = [];
        if ($this->con) {
            $retRes = $this->redis->sIsMember($this->prefix.$key,$value);
        }
        return $retRes;
    }
    //删除有序集合中的元素
    public function sRem($key,$value)
    {
        $retRes = [];
        if ($this->con) {
            $retRes = $this->redis->sRem($this->prefix.$key,$value);
        }
        return $retRes;
    }
}
