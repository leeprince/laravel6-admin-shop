<?php
/**
 * [description]
 * @Author leeprince:2021-02-28 21:28
 */

namespace App\Http\Controllers;


use App\Models\User;
use App\Utils\CommonUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}