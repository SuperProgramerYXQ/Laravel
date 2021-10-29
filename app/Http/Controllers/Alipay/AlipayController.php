<?php
/**
 * User:SuperMan
 * Date:2021/10/29
 * Time:9:03
 */
namespace App\Http\Controllers\Alipay;


use GuzzleHttp\Client;
use Illuminate\Http\Request;

/**
 * 支付页面客户端
 * Class AlipayController
 * @package App\Http\Controllers\Alipay
 */
class AlipayController extends BaseController
{
    /**
     * 支付宝客户端页面
     */
    public function Alipaypage()
    {
        // 公共请求参数
        $public_param = [
            'app_id' => parent::ALIPAY_APP_ID,
            'method' => 'alipay.trade.page.pay',                             // 接口名称
            'format' => 'JSON',
            'return_url' => parent::RETURN_URL,                              // 同步通知地址
            'charset' => 'UTF-8',                                            // 请求使用的编码格式
            'sign_type' => 'RSA2',                                           // 签名类型
            'sign' => '',
            'timestamp' => date('Y-m-d H:i:s'),                      // 发送请求的时间
            'version' => '1.0',
            'notify_url' => parent::NOTIFY_URL,                              // 异步通知地址
            'biz_content' => '',                                             // 请求参数的集合
        ];

        // 请求参数集合
        $api_param = [
            'out_trade_no' => date('YmdHis'),                        // 商户订单号
            'total_amount' => 28888,                                         // 订单总金额
            'subject' => '皇家壹号头牌技师全套服务',                                     // 订单标题
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
            'body' => '170cm,36D,45KG'                                       // 订单附加信息
        ];

        $public_param['biz_content'] = json_encode($api_param,JSON_UNESCAPED_UNICODE);
        $public_param = $this->SetSign($public_param);
        $url = parent::REQUEST_URL.'?'.$this->GetUrl($public_param);

        header("location:".$url);
    }

    /**
     * 同步通知地址
     */
    public function ReturnUrl()
    {
        echo '支付成功';
    }

    /**
     * 异步通知地址
     * 外网能访问到（本地配置虚拟域名可能会无法接收）
     */
    public function NotifyUrl()
    {
        // 获取数据
        $postData = $_POST;
        // 验证签名
        $str = $this->GetString($postData);
        $check = $this->rsaCheck($str,parent::ALI_PUBLIC_KEY,$postData['sign']);
        if ($check){
            $this->logs('签名验证成功！');
        }else{
            $this->logs('签名验证失败！');
            exit();
        }

        // 验证是否来着支付宝的请求
        if ($this->IsAlipay($postData)){
            $this->logs('是支付宝来的请求！');
        }else{
            $this->logs('不是支付宝来的请求！');
            exit();
        }

        // 验证交易状态
        if ($this->CheckOrderStatus($postData)){
            $this->logs('交易成功！');
        }else{
            $this->logs('交易失败！');
            exit();
        }

        // 验证订单号和金额
        // 获取支付宝发过来的订单号 out_trade_no 及金额 total_amount 对比本地数据库的订单号及金额
        // 这里以日志的形式记录对比
        $this->logs('订单号：'.$postData['out_trade_no'].'，订单金额：'.$postData['total_amount']);

        // 更改订单状态
        $this->logs('支付成功！');
        // 回复支付宝通知
        echo 'success';
    }


    /**
     * 支付宝退款
     * https://opendocs.alipay.com/open/028sm9
     * @param Request $request
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function AliRefund(Request $request)
    {
        $trade_no = $request->input('trade_no','');
        $refund_amount = $request->input('refund_amount',0);
        $refund_reason = $request->input('refund_reason','');
        // 实际逻辑为查询数据库中支付宝交易号所对应的订单金额，以及实际付款金额，对比退款金额是否超出实际付款金额
        // 这里为了方便不进行数据库操作

        $public_param = [
            'app_id' => parent::ALIPAY_APP_ID,
            'method' => 'alipay.trade.refund',
            'format' => 'JSON',
            'charset' => 'UTF-8',
            'sign_type' => 'RSA2',
            'sign' => '',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => 1.0,
            'biz_content' => '',
        ];

        $api_param = [
            'out_trade_no' => '',// 商户订单号
            'trade_no' => '2021102822001404660512801084', // 支付宝交易号
            'refund_amount' => 2888888,// 退款金额
            'refund_reason' => '38号技师拒绝客户特殊要求',// 退款原因
//            'trade_no' => $trade_no, // 支付宝交易号
//            'refund_amount' => $refund_amount,// 退款金额
//            'refund_reason' => $refund_reason,// 退款原因
        ];

        $public_param['biz_content'] = json_encode($api_param,JSON_UNESCAPED_UNICODE);
        // 加入签名
        $public_params = $this->SetSign($public_param);

        // 满足 API 的路由形式
        $url = parent::REQUEST_URL.'?'.$this->GetUrl($public_params);

        // 发起退款请求
        $client = new Client();
        $res = $client->request('GET',$url,[
            'herder' => [
                'Content-Type' => 'application/json'
            ],
            'timeout' => 10
        ]);
        if($res->getStatusCode()==200){
            $content = json_decode($res->getBody()->getContents());
            if ($content->alipay_trade_refund_response->code == 10000){
                echo '退款成功！';
            }else{
                echo $content->alipay_trade_refund_response->sub_msg;
            }
        }else{
            echo '服务请求失败，状态码为：'.$res->getStatusCode();
        }
    }
}
