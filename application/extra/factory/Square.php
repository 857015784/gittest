<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/4/10
 * Time: 15:17
 */

namespace app\extra\factory;

/**
 * 子类2 具体实现 接口试下
 * Class Square
 * @package app\extra\factory
 */
class Square implements IShape
{
    public function draw()
    {
        return '画一个正方形';
    }
}