<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/4/1
 * Time: 17:25
 */

namespace app\extra\celue;

//具体策略角色
class Browser
{ //具体策略角色
    public function call($object)
    {
        return $object->PrintPage();
    }
}