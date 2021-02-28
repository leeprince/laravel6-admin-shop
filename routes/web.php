<?php

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

/** 获取用户授权信息-原生实现 */
Route::get('wxcode', 'WechatOriginOfficialAccountController@wxcode');
Route::get('wxtoken', 'WechatOriginOfficialAccountController@wxtoken');

/** 获取用户授权信息-overtrue/laravel-wechat组件包的中间件方式实现
 *      因为基于本地开发，通过内网穿透实现的回调地址，所以单独配置内网穿透访问的域名。
 *          所以在 overtrue/laravel-wechat 组件包获取授权后微信的回调地址的 vendor/overtrue/laravel-wechat/src/Middleware/OAuthAuthenticate.php 的handle 方法中 74 行中
 *          将 getRedirectUrl() 替换为自行添加的getEnvRedirectUrl()
 */
Route::group(['middleware' => 'auth.laravel-wechat'], function () {
    Route::get('wechat/auth', function(){
        $wechat = session('wechat.oauth_user.default'); //拿到授权用户资料
        dd($wechat); //打印出授权用户资料
    });
    
    Route::any('wechat','WechatOfficialAccountController@auth')->name('Login.WeChat');
});
// 商品首页无需登录
Route::get('products','ProductController@index')->name('products.index');

