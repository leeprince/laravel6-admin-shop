<?php
/**
 * [description]
 * @Author leeprince:2021-02-28 21:28
 */

namespace App\Http\Controllers;


use App\Facades\Resp;
use App\Models\User;
use App\Utils\CommonUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WechatOfficialAccountController
{
    /**
     * [微信授权登录， "auth.laravel-wechat" 中间件获取授权信息]
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function auth(Request $request)
    {
        $WeChatUserInfo = session('wechat.oauth_user.default');
        
        // dd($WeChatUserInfo);
        // exit
        
        $UserInfo = User::where('Openid',$WeChatUserInfo['id'])->first();
        
        if (!$UserInfo) {
            //用户的密码需要他在第一次登录的时候进行设置
            //手机号码需要进行绑定,需要根据用户id来进行异构索引表分表
            $result = User::create([
                'id' => CommonUtil::uuid(),
                'Openid' => $WeChatUserInfo['id'],
                'username' => $WeChatUserInfo['name'],
                'role_id' => 1,//角色默认1位普通用户
                'vender_type' => 2,
                'status' => 0,
                'login_ip' => $request->getClientIp()
            ]);
        }else{
            $result = $UserInfo;
        }
        
        /**
         * 登录验证
         *  Auth 使用用户模型在 config/auth.php 的 providers.users.models 中配置
         */
        Auth::login($result,true);
        
        $redirect_url = $request->redirect_url;
        if ($redirect_url == '') {
            return redirect('/products');
        }else{
            return redirect($redirect_url);
        }
    }
    
    /**
     * [服务器验证 与 消息的接收与回复 都在这一个路由内完成交互]
     */
    public function subscription()
    {
        Log::info('> subscription'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
    
        /**
         * 从app容器中获取组件的微信公众号实例
         *  底层源码在：vendor/overtrue/laravel-wechat/src/ServiceProvider.php 的 register 方法中
         *      核心是：$apps ｜ $this->app->singleton("wechat.{$name}.{$account}" ...
         */
        $app = app('wechat.official_account');
        $app->server->push(function($message){
            return "欢迎关注 PrinceProgramming！";
        });
    
        return $app->server->serve();
    }
    
    /**
     * [发布微信公众号菜单]
     *      注意查看你配置微信公众号信息是真实的公众号信息还是使用测试公众号的信息（https://mp.weixin.qq.com/debug/cgi-bin/sandboxinfo?action=showinfo&t=sandbox/index）
     */
    public function createMenu()
    {
        $buttons = config('wechat.buttons');
    
        $app = app('wechat.official_account');
        
        dump('读取（查询）已设置菜单', $app->menu->list());
    
        dump('添加普通菜单返回结果', $app->menu->create($buttons));
        
        dump('获取当前菜单', $app->menu->current());
        
        return Resp::success();
    }
}