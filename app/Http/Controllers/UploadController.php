<?php
/**
 * Programmer:SuperProgrammer_YXQ
 * Date:2020/6/8
 * Time:15:53
 */
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Class UploadController
 * 文件上传类
 */
class UploadController extends Controller{

    /**
     * @var array
     */
    private $fileType;

    /**
     * 初始化所需值
     * UploadController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        // 限制的文件类型
        // 该值应为多选项，传入字符串以 , 隔开
        $FileType = strtolower($request->input('fileType','jpg,png,pdf')); // 全小写处理
        $this->fileType = explode(',',$FileType);
    }


    /**
     * base64 文件上传
     * @param Request $request
     * @return mixed
     */
    public function base64UploadFile(Request $request){
        // 上传的文件
        $base64FileStr = $request->input('file','');

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64FileStr, $base64File)){

            $type = $base64File[2];

            $FileUrl = $this->Base64Upload($type,$this->fileType,'Images',$base64FileStr);

            if ($FileUrl['code'] == 200) {
                return $this->success($FileUrl['url']);
            } else {
                return $this->failed($FileUrl['msg']);
            }
        }else{
            return $this->failed('文件格式错误，非base64。');
        }
    }

    /**
     * base64 文件上传处理类
     * @param string $type 文件类型
     * @param array $fileType 限制文件类型
     * @param string $path 文件夹名称
     * @param string $base64String base64数据
     * @param string $disk 驱动器
     * @return array
     */
    private function Base64Upload($type,$fileType,$path='UploadFile',$base64String,$disk='public'){

        // 参数判断
        if (!is_array($fileType)) {
            return [
                'code' => 400,
                'msg' => '参数错误。'
            ];
        }
        $compare = strtolower($type); // 小写做比较处理，呼应初始化的小写，保证大小写后缀都能存入
        // 文件类型合法检查
        if (!in_array($compare,$fileType)) {
            return [
                'code' => 400,
                'msg' => '您规定只支持 '.implode(',',$fileType).' 文件类型。'
            ];
        }

        // 取出base64头
        $replace = substr($base64String, 0, strpos($base64String, ',')+1);
        // 取出base64编码
        $base64Str = str_replace($replace, '', $base64String);
        $base64Str = str_replace(' ', '+', $base64Str);

        // 文件路径
        $FilePath = iconv("UTF-8", "GBK", $path.'/'.date('Y_m_d'));

        // 文件重命名
        $FileNewName = $FilePath.'/'.md5(time()).mt_rand(0,9999).'.'.$type;

        if (Storage::disk($disk)->put($FileNewName, base64_decode($base64Str))) {
            $results = [
                'code' => 200,
                'url' => Storage::url($FileNewName)
            ];
        } else {
            $results = [
                'code' => 400,
                'msg' => '服务器异常，上传失败。'
            ];
        }
        return $results;
    }

    /**
     * File 文件上传
     * @param Request $request
     * @return mixed
     */
    public function UploadFile(Request $request)
    {
        $file = $request->file('file');

        if ($file) {

            $FileUrl = $this->Upload($file,$this->fileType,'PDF',2048000);

            if ($FileUrl['code'] == 200) {
                return $this->success($FileUrl['url']);
            } else {
                return $this->failed($FileUrl['msg']);
            }
        } else {
            return $this->failed('没有文件。');
        }

    }

    /**
     * File 文件上传处理类
     * @param $file 文件
     * @param array $type 文件类型
     * @param string $path 文件夹名称
     * @param int $size 文件大小
     * @param string $disk 驱动器
     * @return bool|mixed
     */
    private function Upload($file,$type,$path='UploadFile',$size=1024000,$disk='public'){

        // 是否上传成功
        if (! $file->isValid()) {
            return [
                'code' => 400,
                'msg' => '上传失败。'
            ];
        }
        // 参数判断
        if (!is_array($type)) {
            return [
                'code' => 400,
                'msg' => '参数错误。'
            ];
        }
        // 文件类型判断
        $fileExtension = $file->getClientOriginalExtension();
        $compare = strtolower($fileExtension); // 小写做比较处理，呼应初始化的小写，保证大小写后缀都能存入
        if(! in_array($compare, $type)) {
            return [
                'code' => 400,
                'msg' => '您规定只支持 '.implode(',',$type).' 文件类型。'
            ];
        }
        // 文件大小
        $tmpFile = $file->getRealPath();

        if (filesize($tmpFile) > (int)$size) {
            return [
                'code' => 400,
                'msg' => '文件过大。'
            ];
        }

        // 文件路径
        $FilePath = iconv("UTF-8", "GBK", $path.'/'.date('Y_m_d'));

        // 文件重命名
        $FileNewName = $FilePath.'/'.md5(time()).mt_rand(0,9999).'.'.$fileExtension;

        if (Storage::disk($disk)->put($FileNewName, file_get_contents($tmpFile)) ){
            $results = [
                'code' => 200,
                'url' => Storage::url($FileNewName)
            ];
        }else{
            $results = [
                'code' => 400,
                'msg' => '服务器异常，上传失败。'
            ];
        }
        return $results;
    }
}
