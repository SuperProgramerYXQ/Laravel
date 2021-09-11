<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

/**
 * 发送邮件任务类
 * Class SendEmail
 * @package App\Jobs
 */
class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $message;
    protected $name;
    protected $subject;
    protected $send;
    protected $to;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message,$name,$subject,$to)
    {
        //$this->send = env('MAIL_USERNAME','1149614015@qq.com'); // 发送邮件使用的授权账号
        $this->send = '123123@qq.com';
//        $this->message = '这是邮件测试的内容';
//        $this->name = '发送人名称';
//        $this->subject = '邮件的主题';
        $this->message = $message;
        $this->name = $name;
        $this->subject = $subject;
        $this->to = $to;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 发送邮件
        Mail::raw($this->message,function ($email){
            $email->from($this->send,$this->name);
            $email->subject($this->subject);
            $email->to($this->to);
        });
    }
}
