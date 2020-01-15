<?php
/*
 * @Author: your name
 * @Date: 2019-12-25 19:38:47
 * @LastEditTime : 2020-01-13 19:20:49
 * @LastEditors  : Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: \htdocs\Laravel\app\Http\Controllers\Yuekao\Yuekao.php
 */

namespace App\Http\Controllers\Yuekao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tools\Curl;

class Yuekao extends Controller
{
    public function yuekao()
    {
        return view('yuekao/yuekao');
    }
    public function yue()
    {
        return view('yuekao/yue');
    }
}
