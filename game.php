<?php

/**
 * @Author: wanghuabin
 * @Date:   2019-01-05 10:35:17
 * @Last Modified by:   wanghuabin
 * @Last Modified time: 2019-01-08 10:57:57
 */
use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Lib\Timer;
use PHPSocketIO\SocketIO;
include __DIR__ . '/vendor/autoload.php';

class Games
{
    private static $_instance;

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    function startCard(){
	$card = [
			 'AA','AB','AC','AD','2A','2B',
			 '2C','2D','3A','3B','3C','3D',
			 '4A','4B','4C','4D','5A','5B',
			 '5C','5D','6A','6B','6C','6D',
			 '7A','7B','7C','7D','8A','8B',
			 '8C','8D','9A','9B','9C','9D',
			 '10A','10B','10C','10D','JA','JB',
			 'JC','JD','QA','QB','QC','QD','KA',
			 'KB','KC','KD','ZA','ZB'
			];
	//打乱数组
	shuffle($card);
	//取出三个
	$landlordCard = [$card[0],$card[1],$card[2]];
	//从牌堆删除取出来的3张
	array_splice($card,0,3);
	//再次打乱数组
	shuffle($card);
	//把数组分为四份
	$personCard = [];
	for ( $i = 0; $i < count($card) / 17; $i ++ ) 
		{
			$personCard[$i] = array_slice ( $card, 17 * $i, 17);
		}

	//对已分配的牌面进行排序
	foreach ($personCard as $key => $value) {
		natcasesort($personCard[$key]);
	}
	//发牌结束
	$res = ['personCard'=>$personCard,'landlordCard'=>$landlordCard];
	return ($res);
}
//规则
function rule(){
	$card = $_POST['card'];
	$uid = $_POST['uid'];
	$userCard = $_SESSION['uidCard'][$_GET['uid']];
	//判断牌是否在用户手上

	foreach ($card as $va) {   
	    if (in_array($va, $userCard)) {   
	        continue;   
	    }else {   
	        echo json_encode(['err'=>0,'msg'=>'错误']);exit;//出了不属于自己的牌
	        break;   
	    }
	}

/*	规则判断
	单牌
	对
	三
	三一
	三二
	顺
	四个
	连二
	飞机
	王*/
}
//格式化
function format($card){
	//计算长度
	$cardNum = count($card);
	switch ($cardNum) {
		case '1': //单
			$r = ['card'=>$card[0],'rule'=>1];
			return $r;
			break;
		case '2': //对/王
			if(in_array('ZA', $card) && in_array('ZB', $card)){
				return  'king';
			}else{
				$cardSt = $this->sameCard($card);
				if($cardSt['sameNum'] == 2){
					$r = ['card'=>$card[0],'rule'=>2];
					return $r;
				}
			}
			break;
		case '3'://san
			$cardSt = $this->sameCard($card);
				if($cardSt['sameNum'] == 3){
					$r = ['card'=>$card[0],'rule'=>3];
					return $r;
				}
			break;
		case '4':
			$cardSt = $this->sameCard($card);
				if($cardSt['sameNum'] == 3){
					$r = ['card'=>$card[0],'rule'=>301];	
				}elseif($cardSt['sameNum'] == 4){
					$r = ['card'=>$card[0],'rule'=>4];
				}
				return $r;
			break;
		default:
			# code...
			break;
	}
}
//判断数组中相等元素和不同元素的数量
function sameCard($card){
	$sameNum = 1;
	$differentNum = 1;
	foreach ($card as $k => $v) {
		if($tempc){
			if($tempc	== substr($v,0,1)){
				$sameNum++; 
			}else{
				$differentNum++;
			}
		}else{
			$tempc = substr($v,0,1);
		}
	}
	return ['sameNum'=>$sameNum,'differentNum'=>$differentNum];

}
function login(){
	session_start();
	//cookie 记录登陆人数
	//setcookie("user", "",time()-3600);
	$isOnline = 0;
	if(isset($_COOKIE['user'])){
		$userList = json_decode($_COOKIE['user']);
		//print_r($userList);die;
		$count =count($userList);
		$uid = $_GET['uid'];
		if(in_array($uid, $userList)){
			$isOnline = 1;
			echo json_encode(['err'=>0,'msg'=>'进行中','data'=>$_SESSION['uidCard'][$_GET['uid']]]);exit;
		}
	}
	if($count>3 && !$isOnline){
		echo json_encode(['err'=>1,'msg'=>'人满了']);exit;
	}else{
		 
		
		 if(isset($_COOKIE['user'])){
 			$uidArr = json_decode($_COOKIE['user']);
 			if(!in_array($_GET['uid'], $uidArr)){
 				$uidArr[]=$_GET['uid'];
 				$uidArr = json_encode($uidArr);
 				setcookie('user',$uidArr);
 			}
		 }else{
		 	$uidArr[]=$_GET['uid'];
 			$uidArr = json_encode($uidArr);
 			setcookie('user',$uidArr);
		 }
		 //发牌 牌是否已经存入session
		if($_SESSION['card']){
			$card = $_SESSION['card'];
		}else{
			$card = self::startCard();
			$_SESSION['card'] = $card;
		}
		$userCard = array_values($card['personCard'][0]); //去除第组牌发给当前用户
		unset($card['personCard'][0]);
		$card['personCard'] = array_values($card['personCard']);
		$_SESSION['card'] = $card;//把取出后剩余的牌重新放入session
		$_SESSION['uidCard'][$_GET['uid']] = $userCard;//把当前用户的牌存入session
		echo json_encode(['err'=>0,'msg'=>'加入成功','data'=>$userCard]);exit;

	}
}

}
if ($_REQUEST['act'] == 'startCard') {
    Games::getInstance()->startCard();
}elseif ($_REQUEST['act'] == 'login') {
    Games::getInstance()->login();
} 
