<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/4/10
 * Time: 15:17
 */

namespace app\extra\factory;

/**
 * 子类1 具体实现
 * Class Circle
 * @package app\extra\factory
 */
class Circle implements IShape
{
    public function draw()
    {
        return '画一个圆形';
    }


}