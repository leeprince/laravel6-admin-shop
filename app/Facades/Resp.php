<?php
/**
 * [响应门面]
 * @Author leeprince:2021-02-28 23:39
 */

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class Resp extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Resp';
    }
}