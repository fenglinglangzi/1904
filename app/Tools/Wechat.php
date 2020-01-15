<?php

namespace App\Tools;

use CURLFile;
use App\Token;

class Wechat
{
    const appId = "wxb69b026865f3bc0f";
    const appsecret = "a8e4b22e8cbd8df19fec307eef1e0339";
    //响应微信文本消息
    public static function ReponsrText($msg, $postObj)
    {
        echo "<xml>
                                <ToUserName><![CDATA[" . $postObj->FromUserName . "]]></ToUserName>
                                <FromUserName><![CDATA[" . $postObj->ToUserName . "]]></FromUserName>
                                <CreateTime>" . time() . "</CreateTime>
                                <MsgType><![CDATA[text]]></MsgType>
                                <Content><![CDATA[" . $msg . "]]></Content>
                        </xml>";
        die;
    }
    //响应微信图片消息
    public static function ReponsrImg($media_id, $postObj)
    {
        echo "<xml>
                        <ToUserName><![CDATA[" . $postObj->FromUserName . "]]></ToUserName>
                        <FromUserName><![CDATA[" . $postObj->ToUserName . "]]></FromUserName>
                        <CreateTime>" . time() . "</CreateTime>
                        <MsgType><![CDATA[image]]></MsgType>
                        <Image>
                            <MediaId><![CDATA[" . $media_id . "]]></MediaId>
                        </Image>
                </xml> ";
    }
    //响应微信视频消息
    public static function ReponsrVideo($media_id, $postObj)
    {
        echo "   <xml>
                        <ToUserName><![CDATA[" . $postObj->FromUserName . "]]></ToUserName>
                        <FromUserName><![CDATA[" . $postObj->ToUserName . "]]></FromUserName>
                        <CreateTime>" . time() . "</CreateTime>
                        <MsgType><![CDATA[video]]></MsgType>
                        <Video>
                            <MediaId><![CDATA[" . $media_id . "]]></MediaId>
                            <Title><![CDATA[title]]></Title>
                            <Description><![CDATA[description]]></Description>
                        </Video>
                    </xml>";
    }
    //响应微信语音消息
    public static function ReponsrVoice($media_id, $postObj)
    {
        echo "<xml>
                        <ToUserName><![CDATA[" . $postObj->FromUserName . "]]></ToUserName>
                        <FromUserName><![CDATA[" . $postObj->ToUserName . "]]></FromUserName>
                        <CreateTime>" . time() . "</CreateTime>
                        <MsgType><![CDATA[voice]]></MsgType>
                        <Voice>
                            <MediaId><![CDATA[" . $media_id . "]]></MediaId>
                        </Voice>
                    </xml>";
    }
    //获取access_token
    /*   public static function getToken()
    {
        $access_token = \Cache::get('access_token');
        if (empty($access_token)) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . self::appId . "&secret=" . self::appsecret . "";
            $data = file_get_contents($url);
            $data = json_decode($data, true);
            $access_token = $data['access_token'];
            \Cache::put('access_token', $access_token, 7200);
        }
        return $access_token;
    } */
    public static function getToken()
    {
        $tokeninfo = Token::get()->toArray();

        $access_token = $tokeninfo[0]['access_token'];
        if (empty($access_token) && (time() - $tokeninfo('add_time') < 300)) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . self::appId . "&secret=" . self::appsecret . "";
            $data = file_get_contents($url);
            $data = json_decode($data, true);
            $access_token = $data['access_token'];
            Token::where('access_token', $access_token)->delete();
            Token::create([
                'access_token' => $access_token,
                'add_time' => time()
            ]);
        }
        return $access_token;
    }
    /**
     * //调用天气接口
     */
    public static function getWeather($city)
    {

        $url = "http://api.k780.com:88/?app=weather.future&weaid={$city}&&appkey=46446&&sign=2493b5f70fd4ba22507de66d3b143a54&format=json";

        $data = Curl::get($url);
        $data = json_decode($data, true);
        $msg = "";
        foreach ($data['result'] as $k => $v) {
            $msg .= $v['days'] . " " . $v['citynm'] . " " . $v['week'] . " " . $v['temperature'] . " " . $v['weather'] . "\r\n";
        }
        return $msg;
    }
    /**
     * 根据open_id 获取用户信息
     */
    public static function getUserinfo($open_id)
    {
        $access_token = self::getToken();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $open_id . "&lang=zh_CN";
        $userInfo = file_get_contents($url);
        $userInfo = json_decode($userInfo, true);
        return $userInfo;
    }
    /**
     * 上传素材
     */
    public static function uploadMedia($data)
    {
        $access_token = self::getToken();
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type=" . $data['media_format'] . "";
        $img = "/data/wwwroot/default/blog/public" . $data['media_url'] . "";
        $postData['media'] = new CURLFile($img);
        $res = Curl::post($url, $postData);
        $res = json_decode($res, true);
        return $res;
    }
    /**
     * 获取带参数二维码钥匙 ticket
     */
    public static function getTicket($channel_status)
    {
        $access_token = self::getToken();
        $postData = '{"expire_seconds": 604800, "action_name": "QR_STR_SCENE", "action_info": {"scene": {"scene_str": "' . $channel_status . '"}}}';
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $access_token . "";
        $res = Curl::post($url, $postData);
        $res = json_decode($res, true);
        $ticket = $res['ticket'];
        return $ticket;
    }

    /**
     * 网页授权获取用户openid
     * @return [type] [description]
     */
    public static function getOpenid()
    {
        //先去session里取openid
        $openid = session('openid');
        //var_dump($openid);die;
        if (!empty($openid)) {
            return $openid;
        }
        //微信授权成功后 跳转咱们配置的地址 （回调地址）带一个code参数
        $code = request()->input('code');
        if (empty($code)) {
            //没有授权 跳转到微信服务器进行授权
            $host = $_SERVER['HTTP_HOST'];  //域名
            $uri = $_SERVER['REQUEST_URI']; //路由参数
            $redirect_uri = urlencode("http://" . $host . $uri);  // ?code=xx
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . self::appId . "&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
            header("location:" . $url);
            die;
        } else {
            //通过code换取网页授权access_token
            $url =  "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . self::appId . "&secret=" . self::appsecret . "&code={$code}&grant_type=authorization_code";
            $data = file_get_contents($url);
            $data = json_decode($data, true);
            $openid = $data['openid'];
            //获取到openid之后  存储到session当中
            session(['openid' => $openid]);
            return $openid;
            //如果是非静默授权 再通过openid  access_token获取用户信息

        }
    }

    /**
     * 网页授权获取用户基本信息
     * @return [type] [description]
     */
    public static function getOpenidByUserInfo()
    {
        //先去session里取openid
        $userInfo = session('userInfo');
        //var_dump($openid);die;
        if (!empty($userInfo)) {
            return $userInfo;
        }
        //微信授权成功后 跳转咱们配置的地址 （回调地址）带一个code参数
        $code = request()->input('code');
        if (empty($code)) {
            //没有授权 跳转到微信服务器进行授权
            $host = $_SERVER['HTTP_HOST'];  //域名
            $uri = $_SERVER['REQUEST_URI']; //路由参数
            $redirect_uri = urlencode("http://" . $host . $uri);  // ?code=xx
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . self::appId . "&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
            header("location:" . $url);
            die;
        } else {
            //通过code换取网页授权access_token
            $url =  "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . self::appId . "&secret=" . self::appsecret . "&code={$code}&grant_type=authorization_code";
            $data = file_get_contents($url);
            $data = json_decode($data, true);
            $openid = $data['openid'];
            $access_token = $data['access_token'];
            //获取到openid之后  存储到session当中
            //session(['openid'=>$openid]);
            //return $openid;
            //如果是非静默授权 再通过openid  access_token获取用户信息
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
            $userInfo = file_get_contents($url);
            $userInfo = json_decode($userInfo, true);
            //返回用户信息
            session(['userInfo' => $userInfo]);
            return $userInfo;
        }
    }
}
