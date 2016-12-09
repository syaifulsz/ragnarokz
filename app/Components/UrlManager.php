<?php

namespace App\Components;

use Illuminate\Http\Request;

class UrlManager
{
    public static function getRouteParams($requests, $params = [])
    {
        $requests = is_array($requests) ? $requests : $requests->all();

        foreach ($params as $param_key => $param_value) {
            $requests[$param_key] = $param_value;
        }

        return $requests;
    }

    public static function getRouteSort($request, $sort = null)
    {
        return route('manga/chapter', self::getRouteParams($request, ['sort' => $sort]));
    }

    public static function route($route = '/', $request = [], $params = [])
    {
        $request = !$request ? new Request() : $request;
        return route($route, self::getRouteParams($request, $params));
    }
}
