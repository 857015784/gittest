<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/5/30
 * Time: 15:18
 */
//基类
namespace app\base\controller;


use think\Controller;

class BaseController extends Controller
{
    /**
     * @param $data
     * @param $code
     * @param $message
     * Author: wanghuabin
     * Time: 2019/5/30   15:23
     * 提示信息统一出口
     */
    static function response($data,$code,$message){
        $msg = [
            'data'=>$data,
            'code'=>$code,
            'msg'=>$message,
        ];
        echo  json_encode($msg);
    }
}