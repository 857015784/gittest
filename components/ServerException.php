<?php
/**
 * 公共错误信息
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/5/30
 * Time: 14:21
 */

namespace components;

class ServerException extends \Exception
{
    const PARAMS_IS_ERR = 40000;
    private static $msg = [
        self::PARAMS_IS_ERR => ' Parameter is wrong',
    ];

    public function __construct(int $code)
    {
        $message = self::$msg[$code] ?? '';
        parent::__construct($message, $code);
    }
}