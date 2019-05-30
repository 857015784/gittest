<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/4/1
 * Time: 17:24
 */

namespace app\extra\celue;

//环境角色2
class otherAgent extends  baseAgent
{
    function PrintPage() {
        return 'not IE';
    }

}