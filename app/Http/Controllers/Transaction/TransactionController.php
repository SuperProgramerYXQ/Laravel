<?php
/**
 * User:SuperMan
 * Date:2021/9/18
 * Time:9:06
 */
namespace App\Http\Controllers\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * MYSQL 事务
 * Class TransactionController
 * @package App\Http\Controllers\Transaction
 */
class TransactionController extends Controller
{
    public function Transaction()
    {
        $param = [
            'name' => 'MM',
            'age' => 18
        ];
        // 自动确认法，成功则提交失败自动回滚
        // 此时如果表内最大ID为1，那么此次操作未完成前，自增ID基数为4，锁止ID为2,3的记录不被操作
        // 自动确认法可携带一个可选参数，定义发生死锁时应重新尝试事务的次数
        DB::transaction(function () use ($param){
            DB::table('user')->insert(
                [
                    ['name'=>$param['name'],'age'=>$param['age']],  // 占用ID为2
                    ['name'=>'Super','age'=>'haha']  // 占用ID为3
                ]
            );
        },5);

        // 手动确认事务
        // 事务开始
        DB::beginTransaction();
        try {
            DB::table('user')->insert([
                'name'=>'Superman',
                'age'=>20
            ]);
            // 确认事务
            DB::commit();
        }catch (\Exception $e) {
            // 异常时回滚
            DB::rollBack();
            throw $e;
        }
    }
}
