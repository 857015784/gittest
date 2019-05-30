<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/3/29
 * Time: 18:19
 */

namespace app\base\model;


use think\Db;
use think\Model;
class baseModel extends Model
{
    public function tableName(){
        $table = lcfirst(substr(strrchr(get_called_class(), '\\'), 1));
        return $table;
    }

    public function getAll(){
        $res =  Db::table(self::tableName())->select();
        return $res;
    }
    public function insertdata($data){

        $res =  Db::table(self::tableName())->insert($data);
        return $res;
    }
}