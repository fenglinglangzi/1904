<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Model\User;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_id = Session('user_id');
        $user_name = Session('user_name');
        $session_id = Session::getId();

        $userinfo = User::where('user_name', $user_name)->first();
        if ($session_id != $userinfo->session_id) {
            Session(['user_id' => null]);
            return redirect('/login')->withErrors("你已在别处登陆");
        }
        //判断一定时间内有没有操作
        if (time() > $userinfo->log_time) {
            Session(['user_id' => null]);
            return redirect('/login')->withErrors("自动退出");
        }
        $userinfo = User::where('user_name', $user_name)->update(['log_time' => time() + 30]);
        // $user = Auth::check();
        if (!$userinfo) {
            return redirect('/login');
        }
        return $next($request);
    }
}
