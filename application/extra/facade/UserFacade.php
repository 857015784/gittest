<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/4/10
 * Time: 15:03
 */

namespace app\extra\facade;


class UserFacade
{
    public static function getUserCall($userInfo) {
        $User = new User();
        $User->setUserName($userInfo['username']);
        $User->setUserAge($userInfo['userAge']);
        return $User->getUser();
    }

}