<?php
/**
 * 公共错误信息
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/5/30
 * Time: 14:21
 */

namespace components;
use \ReflectionClass as ReflectionClass;

class ServerException extends \Exception
{
    private $constants = [];
    const PARAMS_IS_ERR = 40000;
    const PARAMS_IS_ERR_MSG = ' Parameter is wrong';

    /**
     * 获取类中常量数组
     * @return array $this->constants
     */
    protected function getConstants()
    {
        $reflection = new ReflectionClass($this);

        if (0 < count($this->constants)) {
            return $this->constants;
        }

        $this->constants = $reflection->getConstants();
        return $this->constants;
    }
    /**
     * @param  integer $code  异常编码
     * @return string  $sMsg  异常编码消息
     */
    public function getMsg($code)
    {
        $msg    = '';
        $msgKey = '';
        $this->getConstants();

        foreach($this->constants as $codeKey => $codeVal) {
            if ($code == $codeVal) {
                $msgKey = "{$codeKey}_MSG";
                continue;
            } else if ($msgKey == $codeKey) {
                $msg = $codeVal;
                break;
            }
        }

        return $msg;
    }

    public function __construct(int $code, string $msg = '')
    {
        '' === $msg && $msg = $this->getMsg($code);
        parent::__construct($msg, $code);
    }
}