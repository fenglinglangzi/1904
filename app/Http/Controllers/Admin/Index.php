<?php
/*
 * @Author: your name
 * @Date: 2019-12-26 09:10:31
 * @LastEditTime : 2020-01-02 18:21:43
 * @LastEditors  : Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: \htdocs\Laravel\app\Http\Controllers\Admin\Index.php
 */



namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tools\Curl;
use App\Model\User;

class Index extends Controller
{
    public $key = "1904api";
    public $iv = "1904190419041904";
    public function index()
    {
        $arr = [
            'user_name' => 'dong',
            'pwd' => '123456'
        ];

        $encrypt = $this->AesEncrypt($arr); //调用加密方法
        $decrypt = $this->AesDecrypt($encrypt); //调用解密方法
        \var_dump($encrypt);
        \var_dump($decrypt);
        die;
        $data = User::get()->toArray();
        return view('admin/index', ['data' => $data]);
    }
    /**
     * 加密
     */
    private function AesEncrypt($data)
    {
        //判断是否是数组  如果是数组就转成json格式
        if (is_array($data)) {
            $data = json_encode($data);
        }

        //加密
        $encrypt = \openssl_encrypt(
            $data, //需要加密的数据
            'AES-256-CBC', //格式
            $this->key,
            1,
            $this->iv
        );
        return  base64_encode($encrypt);
    }
    /**
     * 解密
     */
    private function AesDecrypt($data)
    {
        $data = base64_decode($data);
        $decrypt = \openssl_decrypt(
            $data,
            'AES-256-CBC',
            $this->key,
            1,
            $this->iv
        );
        return  \json_decode($decrypt);
    }
    public function suo(Request $request)
    {
        $user_id = $request->input('user_id');
        $res = User::where('user_id', $user_id)->update(['is_suo' => 1]);
        if ($res) {
            return json_encode(['code' => 200, 'msg' => "解锁成功"]);
        }
    }
}
