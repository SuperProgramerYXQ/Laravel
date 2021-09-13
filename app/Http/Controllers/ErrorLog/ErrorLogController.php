<?php
/**
 * User:SuperMan
 * Date:2021/9/13
 * Time:11:52
 */
namespace App\Http\Controllers\Errorlog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

/**
 * 错误日志的记录
 * Class ErrorLogCortroller
 * @package App\Http\Controllers\Errorlog
 */
class ErrorLogController extends Controller
{
    public function Errors()
    {
        // info 级别的日志
        // Log::info('这是个 info 级别的日志');

        // warning 级别的日志
        // Log::warning('这是个 warning 级别的日志');

        // error 携带参数的日志
        Log::error('这是一个数组',['name'=>'superman','age'=>18]);
    }
}
