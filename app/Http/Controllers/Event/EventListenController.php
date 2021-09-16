<?php
/**
 * User:SuperMan
 * Date:2021/9/16
 * Time:9:35
 */
namespace App\Http\Controllers\Event;
use App\Events\EventName;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ErrorLog\ErrorLogController;

class EventListenController extends Controller
{
    public function RunEvent()
    {
        echo "这是我执行的方法<br>";
        event(new EventName());
//        event(new EventName(new ErrorLogController()));
    }
}
