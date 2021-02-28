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

/** 获取用户授权信息-原生实现
 *      微信公众号开发文档：https://developers.weixin.qq.com/doc/offiaccount/Custom_Menus/Creating_Custom-Defined_Menu.html
 *      微信公众号测试账号：https://mp.weixin.qq.com/debug/cgi-bin/sandboxinfo?action=showinfo&t=sandbox/index
 */
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
    Route::any('wechat/login','WechatOfficialAccountController@auth')->name('Login.WeChat');
});

/**
 * 服务器验证 与 消息的接收与回复 都在这一个路由内完成交互
 *      需要在 app/Http/Middleware/VerifyCsrfToken.php 中将该路由设置到$except(支持 * 的正则)中，排除微信相关路由
 */
Route::any('wechat/subscription','WechatOfficialAccountController@subscription')->name('Login.WeChat');

// 发布微信公众号菜单
Route::get('wechat/createMenu','WechatOfficialAccountController@createMenu');

// 商品首页无需登录
Route::get('products','ProductController@index')->name('products.index');

