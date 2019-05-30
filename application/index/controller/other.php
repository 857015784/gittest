<?php
/**
 * Created by PhpStorm.
 * User: wanghuabin
 * Date: 2019/5/29
 * Time: 15:34
 */

namespace app\index\controller;


class other
{
    public function jyapi(){
        $appid   = 'jyo_CjxUW1RIWQoDCQFbLS0B';
        $secret  = '3u8sd0v1';
        $keyword = !empty($_POST['keyword']) ? $_POST['keyword'] : '抚州市华远建设有限公司';
        $url     = 'https://api.jianyu360.com/open';
        $arr     = [
            "appid"     => $appid,
            "action"    => "getdata",
            "timestamp" => time(),
            "keyword"   => $keyword,
            'pagenum'=>1
        ];
        $signature = $this->getSignature($arr, $secret);
        $arr['signature'] = $signature;
        $headers['content-type'] = 'application/json';
        $headers['content-encoding'] = 'gzip';
        $headers['accept-charset'] = 'utf-8';
        //print_r($arr);die;
        $res = $this->request($url, $arr, $headers);
        echo json_encode($res);die;

    }
    /**
     * hmac_sha1签名算法
     * @param $str
     * @param $key
     * @return string
     * Author: wanghuabin
     * Time: 2019/5/23   9:15
     */
    function getSignature($data, $key) {
        ksort($data);
        //拼接字符串
        $str = "";
        foreach ($data as $k => $v) {
            if ($str != "") {
                $str = $str . "&";
            }
            $str = $str . $k . "=" . $v;
        }
        $str       = urlencode($str);
        $str       = str_replace("%3A", "%253A", str_replace("%7E", "~", str_replace("*", "%2A", str_replace("+", "%20", $str))));
        $signature = base64_encode(hash_hmac('sha1', $str, $key, true));
        return $signature;
    }

    public function request($url = '', $data = array(), $headers = array(), $dataType = 'xml')
    {
        if( empty( $url ) )
            return false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3600); // 5秒超时

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名

        if( $headers )
        {
            $temp = array();
            foreach( $headers as $key => $val )
                $temp[] = "{$key}: {$val}";
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, $temp );
        }


        if( $data )
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            if( $dataType == 'xml' || $dataType == 'json' )
            {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            else
            {
                $postData = $data;
                if( is_array( $postData ) )
                {
                    $postData = array();
                    foreach( $data as $key => $val )
                    {
                        $postData[] = $key . "=" . urlencode( $val );
                    }
                    $postData = implode( "&", $postData );

                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            }
        }


        $output = curl_exec( $ch );
        $error = curl_error( $ch );
        $httpInfo = curl_getinfo( $ch );
        curl_close($ch);

        $requestResult['http_info'] = $httpInfo;
        $requestResult['error'] = $error;
        $requestResult['output'] = $output;

        $output = trim( $output );

        if( $error && empty( $output ) )
        {
            $output = $error;
        }
        else
        {
            $startVar = substr( $output, 0, 1 );
            $endVar = substr( $output, strlen($output) - 1, 1 );
            if( $startVar == '{' && $endVar == '}' || $startVar == '[' && $endVar == ']' )
            {
                $temp = @json_decode( $output, true );
                if( $temp !== false )
                    $output = $temp;
            }
        }

        $requestResult['data'] = $output;
        return $requestResult;
    }
}