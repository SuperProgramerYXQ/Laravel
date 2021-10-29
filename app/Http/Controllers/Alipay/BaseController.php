<?php
/**
 * User:SuperMan
 * Date:2021/10/29
 * Time:9:04
 */

namespace App\Http\Controllers\Alipay;
use Illuminate\Support\Facades\Log;
class BaseController extends RsaController
{

    const RETURN_URL = 'http://www.github-laravel.com/ReturnUrl/';// 同步跳转
    const NOTIFY_URL = 'http://www.github-laravel.com/NotifyUrl/';// 异步通知地址

    const REQUEST_URL = 'https://openapi.alipaydev.com/gateway.do';// 沙箱网关地址
    // const REQUEST_URL = 'https://openapi.alipay.com/gateway.do';// 正式网关地址

    // 验证是否是支付宝发来的通知（验证地址）
    const CHECK_ALI_URL = 'https://mapi.alipay.com/gateway.do?service=notify_verify&partner='.self::ALIPAY_APP_ID.'&notify_id=';


    const ALIPAY_APP_ID = '';// APP ID
    // 应用私钥
    const APP_PRIVATE_KEY = '';
    // const APP_PUBLIC_KEY = '';
    // 支付宝公钥
    const ALI_PUBLIC_KEY = '';

    /**
     * 获取支付页面 URL
     * https://opendocs.alipay.com/open/291/106118
     * @param $data
     */
    public function GetString($data,$type=false)
    {
        // 剔除
        if (isset($data['sign'])){
            unset($data['sign']);
        }

        // 验证RSA2签名时使用
        if ($type){
            unset($data['sign_type']);
        }

        // 排序
        ksort($data);
        // 拼接
        $data = $this->GetUrl($data,false);
        return $data;
    }

    /**
     * 拼接支付页面 URL 字符串
     * @param $data
     * @param bool $encode
     * @return string
     */
    public function GetUrl($data,$encode = true)
    {
        // 携带中文编码后会导致签名验证不通过
        if ($encode){
            // 需要编码
            return http_build_query($data);
        }else{
            // 不需要编码
            return urldecode(http_build_query($data));
        }
    }

    /**
     * 获取签名
     * @param $data
     * @return string
     */
    public function GetSign($data)
    {
        $url = $this->GetString($data);
        return $this->rsaSign($url,self::APP_PRIVATE_KEY);
    }

    /**
     * 将签名插入数组参数
     * @param $data
     * @return mixed
     */
    public function SetSign($data)
    {
        $data['sign'] = $this->GetSign($data);
        return $data;
    }

    /**
     * 验证签名
     * @param $postData
     * @return bool
     */
    public function CheckSign($postData)
    {
        $sign = $this->GetString($postData);
        if ($sign == $postData['sign']){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 判断是否来自支付宝的请求
     * @param $postData
     * @return bool
     */
    public function IsAlipay($postData)
    {
        $str = file_put_contents(self::CHECK_ALI_URL.$postData['notify_id']);
        if ($str == 'true'){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 验证交易状态
     * @param $postData
     * @return bool
     */
    public function CheckOrderStatus($postData)
    {
        if ($postData['trade_status'] == 'TRADE_FINISHED' || $postData['trade_status'] == 'TRADE_SUCCESS'){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 写日志
     * @param $data
     */
    public function logs($data)
    {
        Log::info($data."\r\n");
    }
}
