<?php



namespace App\Http\Controllers\Login;

header('Content-Type: text/html;charset=utf-8');
header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tools\Curl;
use App\Model\User;
use Session;
use Illuminate\Support\Facades\Cache;

class Login extends Controller
{
    const appId = "wxb69b026865f3bc0f";
    const appsecret = "a8e4b22e8cbd8df19fec307eef1e0339";
    public function login(Request $request)
    {
        $siyao = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwA0q6qu6lPOGLZ8FzxSnHXNEi0jaB259zSniIr/E+2yMdUcJf/K84luaHjGBnMBip8kEv5U4EqkVO//Kj8hEet0BNC5yeJmoYgkPpZnuTTXDoKD2N/52RCmyM5xiMFRX4Yi7HsDCbXKLagOaNE2+gdyrEVO1sUEf8Rv9u4tOcLo5BMrwyIRJLZ9xQCPY70VZ9gvffa41dEPe8GRsfBqgcHnL6X84J0QgVjvu8RsgdswRgA89YnslPzR3DGcDA1xxgBJhEPIQxnBCPRGlh0l9QZQf+c5IFrHs+Mfvtwa/YZU/1xz1bf1qiOVoai8HDcjJ2+UJEgMX/J8NeMGgb6OTcwIDAQAB";
        $gongyao = $request->input('gongyao');
        $name = $request->input('name');
        $pwd = $request->input('pwd');
        if ($gongyao != $siyao) {
            return json_encode(['code' => 205, 'msg' => "无权登陆"]);
        }
        $res = User::where('user_name', $name)->first();
        if ($res) {
            if ($pwd != $res['user_pwd']) {
                $cuo = $res->cuo;
                if (empty($cuo)) {
                    $num = 1;
                    User::where('user_name', $name)->update(['cuo' => $num]);
                    return json_encode(['code' => 201, 'msg' => "你已经输错1次，再错误2次后账号将被锁定"]);
                } else if ($cuo == 1) {
                    $num = 2;
                    User::where('user_name', $name)->update(['cuo' => $num]);
                    return json_encode(['code' => 202, 'msg' => "第二次提示你已经输错2次，在错误1次后将被锁定."]);
                } else if ($cuo == 2) {
                    $num = 3;
                    User::where('user_name', $name)->update(['cuo' => $num, 'is_suo' => 2]);
                    return json_encode(['code' => 203, 'msg' => "账号被锁定，请联系管理员解锁"]);
                }
            } else {
                session(['user_id' => $res->user_id]);
                return json_encode(['code' => 200, 'msg' => "登陆成功"]);
            }
        }
    }
    public function login_do(Request $request)
    {
        $name = $request->input('name');
        $pwd = $request->input('pwd');
        $userinfo = User::where('user_name', $name)->first();
        $error_num = $userinfo->error_num;
        if (!time() < $userinfo->error_time) {
            User::where('user_name', $name)->update(['error_num' => 0, 'is_suo' => 1]);
        }
        if ($userinfo->is_suo = 2 && time() < $userinfo->error_time) {
            return redirect('/login')->withErrors("你的账号已锁定,请在" . date("Y-m-d H:i:s", $userinfo->error_time) . "后重试");
        }
        if ($userinfo) {

            if ($pwd != $userinfo->user_pwd) {

                if (empty($error_num)) {
                    $num = 1;
                    User::where('user_name', $name)->update(['error_num' => $num]);
                    return redirect('/login')->withErrors("你已经输错1次，再错误2次后账号将被锁定");
                } else if ($error_num == 1) {
                    $num = 2;
                    User::where('user_name', $name)->update(['error_num' => $num]);
                    return redirect('/login')->withErrors("第二次提示你已经输错2次，在错误1次后将被锁定");
                } else if ($error_num == 2) {
                    $num = 3;
                    User::where('user_name', $name)->update(['error_num' => $num, 'is_suo' => 2, 'error_time' => time() + 60]);
                    return redirect('/login')->withErrors("账号被锁定，请联系管理员解锁");
                }
            } else {
                $session_id = Session::getId();
                User::where('user_name', $name)->update(['error_num' => 0, 'is_suo' => 1, 'session_id' => $session_id, 'log_time' => time() + 30]);
                session(['user_id' => $userinfo->user_id, 'user_name' => $userinfo->user_name]);
                return redirect('/yuekao/yuekao');
            }
        } else {
            return redirect('/login')->withErrors("用户不存在");
        }
    }
    //获取access_token
    public static function getToken()
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
    }
    /**
     * 渠道添加执行
     */
    public function create_do(Request $request)
    {
        $status = md5(uniqid());

        //调用接口
        $ticket = $this->getTicket($status);
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $ticket;
        return view('/login/login_wechat', ['url' => $url, 'status' => $status]);
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
    public function check()
    {
        $status = request()->post('status');
        $openid = Cache::get($status);
        if (!$openid) {
            return \json_encode(['msg' => '未扫码', 'code' => 0]);
        }
        return \json_encode(['msg' => '扫码成功', 'code' => 1]);
    }
}
