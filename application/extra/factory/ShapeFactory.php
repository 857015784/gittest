<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/4/10
 * Time: 15:23
 */

namespace app\extra\factory;

/**
 * 简单工厂类
 * Class ShapeFactory
 * @package app\extra\factory
 */
class ShapeFactory
{
    const CIRCLE = 'circle';
    const SQUARE = 'square';
    const RECTANGLE = 'rectangle';

    private $shape;

    public function __construct($shape)
    {
        $this->shape = $shape;
    }

    public function getShape()
    {
        switch ($this->shape){
            case self::CIRCLE:
                return new Circle();
                break;
            case self::SQUARE:
                return new Square();
                break;
            case self::RECTANGLE:
                return new Rectangle();//.....
                break;
            default:
                return null;
        }
    }
}