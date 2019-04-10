<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/3/29
 * Time: 17:41
 */

namespace app\index\model;

use app\base\model\baseModel;
class Comment extends baseModel
{
    public function getList(){
       return $this->getAll();
    }
    public function insertdata($data){
        return $this->insertdata($data);
    }
    public function testSource(){
        return 33333333;
    }
}