<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/4/10
 * Time: 15:01
 */

namespace app\extra\facade;


class User
{
    protected $userName;
    protected $userAge;

    //必要的逻辑1
    public function setUserName($userName) {
        return $this->userName = $userName;
    }
    //必要的逻辑2
    public function setUserAge($userAge) {
        return $this->userAge = $userAge;
    }
    //方法
    public function getUser() {
        return '用户姓名：' . $this->userName . '； 用户年龄：' . $this->userAge;
    }

}