<?php
/**
 * [微信公众号]
 * @Author leeprince:2021-02-28 13:21
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;

/**
 * [获取微信公众号授权用户信息的原生方式。推荐使用 overtrue/laravel-wechat 的 laravel composer 包去实现]
 * Class WechatOfficialAccountController
 * @package App\Http\Controllers
 */
class WechatOriginOfficialAccountController extends Controller
{
    /**
     * [获取微信code]
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function wxcode()
    {
        //一定要按照参数的顺序进行排列，否则接口将无法访问
        $params = http_build_query([
            'appid' => config('wechat.official_account.default.app_id'),
            'redirect_uri'=> config('app.projectUrl').'/wxtoken',
            'response_type' => 'code',
            'scope' => 'snsapi_userinfo'
        ]);
        
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?'.$params .'#wechat_redirect';
        return redirect($url);
    }
    
    /**
     * [根据code获取access_token, 并根据access_token获取用户基本信息]
     * @param Request $request
     */
    public function wxtoken(Request $request)
    {
        // 根据code获取access_token
        $code = $request->input('code');
        $params = http_build_query([
            'appid' => config('wechat.official_account.default.app_id'),
            'secret'=> config('wechat.official_account.default.secret'),
            'code' => $code,
            'grant_type' => 'authorization_code'
        ]);
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?'.$params;
        $result = json_decode(file_get_contents($url));
        // TODO: [检查获取 access_token 是否成功] - prince_add_todo 2021-02-28 19:22
        // dd($result);
        
        // 根据access_token获取用户基本信息
        $params = http_build_query([
            'access_token' => $result->access_token,
            'openid' => $result->openid,
            'lang' => 'zh_CN',
        ]);
        $url = 'https://api.weixin.qq.com/sns/userinfo?'.$params;
        $UserInfo = json_decode(file_get_contents($url));
        dd($UserInfo);
    }
}