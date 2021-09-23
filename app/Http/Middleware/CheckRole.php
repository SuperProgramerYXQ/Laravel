<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('token');
        if($token != ''){
            $conten = unserialize(Redis::get($token));
            if($conten){
                // 获取 token 携带的用户信息
                $user = unserialize(Redis::get($conten));
                if($user['is_admin']){
                    // 超管拥有所有权限，直接通过
                    return $next($request);
                }else{
                    // 获取角色的权限合集
                    $role = unserialize(Redis::get('role'));

                    // 当前路由
                    $url = \Request::path();
                    // 判断权限
                    if(in_array($url,$role[$user['role_id']])){
                        return $next($request);
                    }else{
                        return json_encode(['code'=>403,'message'=>'无权访问!']);
                    }
                }
            }else{
                return json_encode(['code'=>403,'message'=>'登陆验证已过期!']);
            }
        }else{
            return json_encode(['code'=>403,'message'=>'请登陆后访问!']);
        }
    }
}
