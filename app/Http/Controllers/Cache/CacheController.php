<?php
/**
 * User:SuperMan
 * Date:2021/9/14
 * Time:16:02
 */
namespace App\Http\Controllers\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

/**
 * 缓存
 * Class CacheController
 * @package App\Http\Controllers\Cache
 */
class CacheController extends Controller
{
    /**
     * 添加缓存的形式
     */
    public function AddCache()
    {
        // put() 无返回值
        // Cache::put('name','Superman',10);

        // add() key 存在时返回false
        // $bool = Cache::add('name','XIXI',60);
        // dd($bool);

        // forever() 永久保存对象到缓存
        Cache::forever('name','Superman');

    }
    /**
     * 获取缓存
     */
    public function GetCache()
    {
        // has() 判断缓存是否存在，返回 bool
        if (Cache::has('name'))
        {
            // get() 读取缓存
            // $name = Cache::get('name');

            // pull() 读取并删除缓存
            // $name = Cache::pull('name');
            // dd($name);

            // forget() 从缓存中删除对象，返回 bool
            $bool = Cache::forget('name');
            dd($bool);

        }else{
            echo 'no cache';
        }

    }
}
