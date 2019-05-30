<?php

namespace app\index\controller;


use app\extra\celue\Browser;
use app\extra\celue\otherAgent;
use app\extra\facade\UserFacade;
use app\extra\factory\ShapeFactory;
use app\extra\Guzzle;
use app\index\model\Comment;
use components\Cache;
use components\SphinxClient;

class Index
{
    private $_model = null;

    public function index()
    {
        echo 1222;
    }

    public function test()
    {
        echo 123;
    }

    /**
     * 生产mq信息示例
     * Author: wanghuabin
     * Time: 2019/3/30   13:59
     */
    public function pushmq()
    {
        $mqClient = \components\Helper::getMqClient('testmq');
        $send     = ['act'  => 'comment',
                     'data' => [
                         'name'    => 'mq',
                         'content' => 'aaaa',
                     ]

        ];
        $mqClient->sendMQ($send, 'test_key');
    }

    /**
     * 消费队列示例
     * Author: wanghuabin
     * Time: 2019/3/30   14:08
     */
    public function callback()
    {
        $client = \components\Helper::getMqClient('testmq');
        $client->receiveMQ('mq', array($this, 'handle'));
        $client->close();
        echo 'pop success';
    }

    function handle($data, $obj)
    {
        set_time_limit(60);
        $r = $obj->ack();
        if ($r) {

            try {
                if ($data['act'] == 'comment') {
                    $this->getModel()->insert($data['data']);
                }

            } catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }

        }

    }

    private function getModel(): Comment
    {
        if (!$this->_model instanceof Comment) {
            $this->_model = new Comment();
        }
        return $this->_model;
    }

    /**
     * 斯芬克斯
     * @return \think\response\Json
     * Author: wanghuabin
     * Time: 2019/4/10   13:32
     */
    public function sphinx()
    {
//        require ( "../../../components/sphinxapi.php" );
        $cl = new SphinxClient ();
        $cl->setServer("localhost", 9312);
        // 作为数组返回
        $cl->SetArrayResult(true);
        // 匹配格式  任意匹配
        //$cl->setMatchMode(SPH_MATCH_ANY);
        $cl->setMaxQueryTime(3);
        // input()表示接收用户传过来的数据
        $result = $cl->query(input('test'), '*');

        return json($result);

    }

    /**
     * 缓存示例
     * Author: wanghuabin
     * Time: 2019/3/30   10:44
     */
    public function cache()
    {
        $cache = new Cache();
        //设置缓存
//        $cache::set($cache::TEST, ['a', 'b'], ['12223', '321']);
      //  读取缓存（缓存不存在会自动设置缓存【回调方法】）
        $r = $cache::get($cache::TEST);
        print_r($r);
        die;
    }

    /**
     * GuzzleHttps示例
     * Author: wanghuabin
     * Time: 2019/4/12   13:59
     */
    public function guzzle()
    {
        //POST
        $guzzle    = new Guzzle();
        $base_uri  = 'http://www.elf.com.v2019-04-17.php5.egomsl.com';
        $api       = '/api/freetobuy-api/get-web-params';
        $post_data = ['act_code' => 'hjsdh233','pipeline_code'=>'zf'];
        $res       = $guzzle->guzzle_post($base_uri, $api, $post_data,$headers = [], $type = 'array');
        var_dump($res);
        die;
        //  return $res;
    }

    /*******************************************************设计模式**********************************************/
    //策略模式

    public function celue()
    {
        $bro = new Browser();

        echo $bro->call(new otherAgent ());
    }

    /**
     * 外观模式
     * 通过在必须的逻辑和方法的集合前创建简单的外观接口
     */
    public function facade()
    {
        $userInfo = array('username' => 'test', 'userAge' => 12);
        return UserFacade::getUserCall($userInfo); //只要一个函数就能将调用类简化
    }

    /**
     * 工厂模式
     * Author: wanghuabin
     * Time: 2019/4/10   15:14
     */
    public function factory()
    {
        $shape = (new ShapeFactory(ShapeFactory::SQUARE))->getShape();
        $sting = $shape->draw();
        echo $sting;
    }




}
