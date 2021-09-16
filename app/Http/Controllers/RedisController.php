<?php
/**
 * Programmer:SuperProgrammer_YXQ
 * Date:2020/6/17
 * Time:17:10
 */

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redis;

/**
 * Redis 操作类
 * Class RedisController
 * @package App\Http\Controllers
 */
class RedisController extends Controller{

    /**
     * @var string 队列名称
     */
    private $redisListName = 'SnapUp';

//    public function __construct()
//    {
//        $this->redisListName = 'SnapUp';
//    }

    /**
     * 设置 Redis
     * @return mixed
     */
    public function SetRedis(){
        $keys = md5(uniqid().time());  // redis key
        // redis data
        $data = [
            'name' => 'Superman',
            'sex' => '男',
            'age' => 18
        ];
        // get redis
//        $conten = unserialize(Redis::get('68894a2f0749e9d795b0c27132555014'));
//        dd($conten);
        // set redis
        $setRedis = Redis::setex($keys,7200,serialize($data));   // serialize 序列化方便传输
        if ($setRedis) {
            return $this->success($keys);
        } else {
            return $this->failed('Redis Set Failed.');
        }
    }



    /**
     * redis 的 list 链表
     * redis list 队列
     */
    public function RedisRightPush(){
        // 清空Redis数据库
        // Redis::flushall();
        $user = [
            'name' => 'Superman',
            'sex' => '男',
            'age' => 18
        ];
        $age = 18;

        // 模拟抢购
        for ($i = 1; $i < 101; $i++) {
            // 抢购10组AD钙奶
            if(Redis::llen($this->redisListName) < 10){
                $user['userID'] = $i;
                $user['age'] = $age + $i;
                $user['date'] = date('Y-m-d H:i:s');
                // 入队
                Redis::rpush('SnapUp',serialize($user));
                echo $i."抢购成功。<br>";
            }else{
                echo $i."抢购完了。<br>";
            }
        }
        echo Redis::llen($this->redisListName)."<br>";

        // 出队
        $one = Redis::lpop('SnapUp');
        var_dump(unserialize($one));
        echo Redis::llen($this->redisListName)."<br>";

    }



//    public function phpCode(){
//        // 栈（先进后出）
//        $stack = new \SplStack();
//        // 入栈
//        $stack->push('data1');
//        $stack->push('data2');
//        // 出栈
//
//
//        echo $stack->pop();
//    }
}
