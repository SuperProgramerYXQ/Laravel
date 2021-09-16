<?php

namespace App\Events;

use App\Http\Controllers\ErrorLog\ErrorLogController;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EventName
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $errorLogController;
    // 不需要任何参数的写法
    public function __construct()
    {

    }
    // 需要参数时要引入对应参数所需要的类
//    public function __construct(ErrorLogController $errorLogController)
//    {
//        //
//        $this->errorLogController = $errorLogController;
//    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
