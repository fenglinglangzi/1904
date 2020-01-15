<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tools\Wechat;
use App\Tools\Curl;
use Illuminate\Support\Facades\Cache;

class Weixin extends Controller
{
    public function index(Request $request)
    {
        if ($this->checkSignature($request)) {
            echo $request->echostr;
        } else {
            echo "error";
        }
    }
    private function checkSignature($request)
    {
        $signature = $request->signature;
        $timestamp = $request->timestamp;
        $nonce = $request->nonce;

        $token = env("WXTOKEN");
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /** 响应微信消息 */
    public function reponsrMsg()
    {
        //接收从微信服务器post 过来的xml数据
        $postStr = file_get_contents("php://input");
        //接收的信息写入到一个文件
        file_put_contents("1.txt", $postStr);
        //处理xml格式的数据 将xml格式的数据转换xml格式的对象
        $postObj = simplexml_load_string($postStr, "simpleXMLElement", LIBXML_NOCDATA);

        //判断推送的是事件  不是消息  //判断是关注事件 不是其他事件
        if ($postObj->MsgType == 'event' && $postObj->Event == 'subscribe') {

            //谁给我发的
            $openid = (string) $postObj->FromUserName;
            //获取带参数二维码的标识
            $status = (string) $postObj->EventKey;
            $status =  ltrim($status, 'qrscene_');

            if ($status) {
                Cache::put($status, $openid, 20);
                Wechat::ReponsrText("感谢关注 正在登陆,请稍后", $postObj);
            }
        }
        if ($postObj->MsgType == 'event' && $postObj->Event == 'SCAN') {

            //谁给我发的
            $openid = (string) $postObj->FromUserName;
            //获取带参数二维码的标识
            $status = (string) $postObj->EventKey;
            if ($status) {
                Cache::put($status, $openid, 20);
                Wechat::ReponsrText("正在登陆,请稍后", $postObj);
            }
        }
    }
}
