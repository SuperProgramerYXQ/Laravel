<?php
/**
 * User:SuperMan
 * Date:2021/9/11
 * Time:9:15
 */
namespace App\Http\Controllers\Queue;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;


/**
 * 队列类
 * 使用 database 进行队列演示
 * Class QueueController
 * @package App\Http\Controllers\Queue
 */
class QueueController extends Controller
{
    /**
     * 将任务放置进队列中
     */
    public function AddTask()
    {
        $message = '这是邮件测试的内容';
        $name = '发送人名称';
        $subject = '邮件的主题';
        $to = '1149614015@qq.com';

        // 根据业务需求是否需要返回值选择函数类型
        // 运行需要依靠 DispatchesJobs 基类
        // dispatch 函数将给定的作业推送到 Laravel作业队列
        dispatch(new SendEmail($message,$name,$subject,$to));

        // dispatch_now 函数立即运行给定的作业并从其handle方法返回值
//        $action = dispatch_now(new SendEmail($message,$name,$subject,$to));
//        if ($action) {
//            return true;
//        }else{
//            return false;
//        }
    }
}
