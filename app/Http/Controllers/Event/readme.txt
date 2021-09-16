# 注册事件监听
app\Providers\EventServiceProvider.php
1.在 $listen 下添加数组以键值的形式表示事件以及监听器，例如：
protected $listen = [
    Registered::class => [
        SendEmailVerificationNotification::class,
    ],
    // EventName 事件名称
    'App\Events\EventName' => [
        'App\Listeners\ListenName', // ListenName 监听器名称
    ],
];
2.执行命令 php artisan event:generate 生成事件与监听文件

3.编写事件 \app\Events\EventName.php
    * 根据业务需求注入需要依赖的对象，用于参数获取，例如：
    public $errorLogController;
    public function __construct(ErrorLogController $errorLogController)
    {
        //
        $this->errorLogController = $errorLogController;
    }
4.在监听器中执行监听逻辑
    public function handle(EventName $event)
    {
        // 将获取到的对象参数进行操作

        // 也可操作对象中的方法
        // $event->errorLogController->Errors();
    }
5.在控制器中实现监听事件的绑定
