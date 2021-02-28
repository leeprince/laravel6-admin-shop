<?php
/**
 * [公共工具类]
 * @Author leeprince:2021-02-28 19:25
 */

namespace App\Utils;


class CommonUtil
{
    /**
     * [生成唯一的字符串ID]
     * @param string $prefix
     * @return string
     */
    public static function uuid($prefix='')
    {
        $str = md5(uniqid(mt_rand(), true));
        $uuid  = substr($str,0,8) . '-';
        $uuid .= substr($str,8,4) . '-';
        $uuid .= substr($str,12,4) . '-';
        $uuid .= substr($str,16,4) . '-';
        $uuid .= substr($str,20,12);
        return $prefix . $uuid;
    }
    
    /**
     * [获取配置的项目外部访问的url]
     * @param  \Illuminate\Http\Request  $request
     */
    public static function getEnvProjectHostUrl($request)
    {
        $projectUrl = config('app.projectUrl');
        
        $reg = '/(https|http):\/\/([^\/]+)/i';
        $envUrl = preg_replace($reg, $projectUrl, $request->fullUrl());
        return $envUrl;
    }
}