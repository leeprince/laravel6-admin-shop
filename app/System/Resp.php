<?php
/**
 * [响应类]
 * @Author leeprince:2021-02-28 23:25
 */

namespace App\System;

use Illuminate\Foundation\Application;

class Resp
{
    private $response = ['data' => null, 'code' => 0, 'message' => ''];
    
    public function __construct(Application $app)
    {
        //
    }
    
    /**
     * [成功响应]
     * @param null $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = null, string $message = 'success')
    {
        $response = $this->response;
        
        $response['data']    = $data;
        $response['message'] = $message;
        
        return response()->json($response);
    }
    
    /**
     * [错误响应]
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function error(string $message = 'error', int $code = 1)
    {
        $response = $this->response;
        
        $response['message'] = $message;
        $response['code']    = $code;
        
        $httpCode= 200;
        if ($code >= 400 && $code < 500) {
            $httpCode = $code;
        }
        return response()->json($response, $httpCode);
    }
}