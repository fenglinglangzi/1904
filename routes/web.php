<?php
/*
 * @Author: your name
 * @Date: 2019-10-28 09:17:20
 * @LastEditTime : 2020-01-15 11:15:53
 * @LastEditors  : Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: \htdocs\Laravel\routes\web.php
 */

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login/login');
});
Route::get('/weixin', 'Wechat\\Weixin@index');
Route::post('/weixin', 'Wechat\\Weixin@reponsrMsg');
Route::domain('index.dongpengyuan.com')->middleware('checklogin')->group(function () {
    Route::prefix('/yuekao')->group(function () {
        Route::any('yuekao', 'Yuekao\Yuekao@yuekao');
        Route::any('yue', 'Yuekao\Yuekao@yue');
    });
});


Route::prefix('/admin')->group(function () {
    Route::any('index', 'Admin\Index@index');
    Route::any('suo', 'Admin\Index@suo');
});
Route::get('/login/login_wechat', function () {
    return view('login/login_wechat');
});


Route::prefix('/login')->group(function () {
    Route::any('login_do', 'Login\Login@login_do');
    Route::any('create_do', 'Login\Login@create_do');
    Route::any('check', 'Login\Login@check');
});

Route::prefix('/api')->group(function () {
    Route::any('login', 'Login\Login@login');
});
