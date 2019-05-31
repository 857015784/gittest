<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/3/30
 * Time: 10:57
 */

namespace components;


use Mq\Rabbitmq\XcmqClient;
use think\Config;

class Helper
{

    public function __construct()
    {
    }

    /**
     * @param string $configKey 调用的队列组
     * @param string $config MQ配置信息
     * @param string $log 日志存储对象
     * @return object
     * @throws \Exception
     * 创建MQ连接
     */
    public static function getMqClient(string $configKey, string $config = '', string $log = ''): XcmqClient
    {

        if ($config == '')
            $config = Config::get('MQinfo');
        if ($log == '')
            $log = Config::get('MQlog');
        vendor('mq.rabbitmq.src.XcmqClient');
        $client = new XcmqClient(['config' => $config, 'log' => $log], $configKey);


        return $client;
    }

}