<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/5/30
 * Time: 14:23
 */

namespace app\index\controller;


use app\base\controller\BaseController;
use components\ServerException;

class Msg extends BaseController
{
    //测试提示信息
    public function test()
    {
       /// echo CONF_PATH . 'OOO' . 'config' . ENV.'.'.CONF_EXT;die;
        try {
            $this->a(false);
        } catch (\Exception $exception) {
            self::response([], $exception->getCode(), $exception->getMessage());
        }
    }
    //制造一个错误
    public function a($a)
    {
        if (!$a) {
            throw new ServerException(ServerException::PARAMS_IS_ERR);
        }
    }
}