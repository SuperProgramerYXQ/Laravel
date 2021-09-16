<?php

namespace App\Listeners;

use App\Events\EventName;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ListenName
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EventName  $event
     * @return void
     */
    public function handle(EventName $event)
    {
        // 监听到的事件的动作逻辑
        // $event->errorLogController->Errors();
        echo '监听到你的事件啦';
    }
}
