<?php

/**
 * @Author: wanghuabin观察者模式
 * @Date:   2018-10-12 09:48:29
 * @Last Modified by:   wanghuabin
 * @Last Modified time: 2018-10-12 09:50:01
 */
 
/**
* 抽象主题角色
*/
interface Subject {
 
    /**
     * 增加一个新的观察者对象
     * @param Observer $observer
     */
    public function attach(Observer $observer);
 
    /**
     * 删除一个已注册过的观察者对象
     * @param Observer $observer
     */
    public function detach(Observer $observer);
 
    /**
     * 通知所有注册过的观察者对象
     */
    public function notifyObservers();
}
 
/**
* 具体主题角色
*/
class ConcreteSubject implements Subject {
 
    private $_observers;
 
    public function __construct() {
        $this->_observers = array();
    }
 
    /**
     * 增加一个新的观察者对象
     * @param Observer $observer
     */
    public function attach(Observer $observer) {
        return array_push($this->_observers, $observer);
    }
 
    /**
     * 删除一个已注册过的观察者对象
     * @param Observer $observer
     */
    public function detach(Observer $observer) {
        $index = array_search($observer, $this->_observers);
        if ($index === FALSE || ! array_key_exists($index, $this->_observers)) {
            return FALSE;
        }
 
        unset($this->_observers[$index]);
        return TRUE;
    }
 
    /**
     * 通知所有注册过的观察者对象
     */
    public function notifyObservers() {
        if (!is_array($this->_observers)) {
            return FALSE;
        }
 
        foreach ($this->_observers as $observer) {
            $observer->update();
        }
 
        return TRUE;
    }
 
}
 
/**
* 抽象观察者角色
*/
interface Observer {
 
    /**
     * 更新方法
     */
    public function update();
}
 
class ConcreteObserver implements Observer {
 
    /**
     * 观察者的名称
     * @var <type>
     */
    private $_name;
 
    public function __construct($name) {
        $this->_name = $name;
    }
 
    /**
     * 更新方法
     */
    public function update() {
        echo 'Observer', $this->_name, ' has notified.<br />';
    }
 
}
//实例化类：
$subject = new ConcreteSubject();
 
/* 添加第一个观察者 */
$observer1 = new ConcreteObserver('Martin');
$subject->attach($observer1);
 
echo '<br /> The First notify:<br />';
$subject->notifyObservers();
 
/* 添加第二个观察者 */
$observer2 = new ConcreteObserver('phppan');
$subject->attach($observer2);
 
echo '<br /> The Second notify:<br />';
$subject->notifyObservers();
 
/* 删除第一个观察者 */
$subject->detach($observer1);
 
echo '<br /> The Third notify:<br />';
$subject->notifyObservers();
 