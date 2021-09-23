<?php
/**
 * User:SuperMan
 * Date:2021/9/23
 * Time:10:03
 */
namespace App\Http\Controllers\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


class LoginController extends Controller
{
    /**
     * 检索是否存在角色权限缓存
     * LoginController constructor.
     */
    public function __construct()
    {
        $role = Redis::get('role');
        if (!$role){
            $this->GetRole();
        }
    }

    /**
     * 用户登陆
     */
    public function Login()
    {
        // 登陆验证逻辑


        // 验证通过逻辑
        $user_info = [
            'user_name' => 'Superman',
            'user_email' => 'Superman@gmail.com',
            'is_admin' => 1,
            'role_id' => 1,
        ];

        try {
            // 为用户添加 Token
            $token_key = md5(uniqid());
            Redis::setex($token_key,7200,serialize($user_info));   // serialize 序列化方便传输
            // 每次登陆都检查一次权限缓存
            $this->GetRole();

            return json_encode(['token'=>$token_key,'code'=>200]);
        }catch (\Exception $e)
        {
            throw $e;
        }


    }

    /**
     * 获取角色的权限
     */
    private function GetRole()
    {
        $role = Redis::get('role');
        if (!$role){
            $role = DB::table('role_url')->get();
            $role_arr = [];
            foreach ($role as $r)
            {
                $role_arr[$r->role_id][] = $r->url;
            }
            Redis::setex('role',7200,serialize($role_arr));
        }
    }


    /**
     * 获取当前项目所有路由列表
     * @return mixed
     */
    public function GetAllRoutes()
    {
        $app = app();
        $path = array();
        $routes = $app->routes->getRoutes();
        foreach ($routes as $k=>$value){
            if(isset($value->action['as'])){ // 设置了name属性的路由为需要认证权限的路由，没有设置的默认开放
                $path[$k]['uri'] = $value->uri;
                $path[$k]['name'] = $value->action['as'];
            }
        }
        return json_encode($path);
    }

}
