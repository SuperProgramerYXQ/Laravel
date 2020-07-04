<?php
/**
 * Programmer:SuperProgrammer_YXQ
 * Date:2020/6/23
 * Time:17:49
 */
namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\PhpWord;

/**
 * Office 软件操作类
 * Class OfficeController
 */
class OfficeController extends Controller{

    // 读取Excel数据
    public function readExcel(){
        // Excel 文件地址
        $excelFile = './storage/Excel/2020_06_24/4a00c4cd76dee58c25114144cec8a7f11808.xlsx';
        // 加载文件

        if (file_exists($excelFile)) {
            $data = Excel::load($excelFile)->get();
            return $this->success($data);
//            Excel::load($excelFile,function($reader){
//                $data = $reader->all();
//                return $this->success($data);
//            });
        } else {
            return $this->failed('文件丢失。');
        }


    }

    // 导出数据为Excel
    public function exportExcel()
    {
        // 导出数据
        $Data = [
            ['字段1', '字段2', '字段3', '字段4'], // 表头
            ['内容1', '内容2', '内容3', '内容4'],
            ['内容1', '内容2', '内容3', '内容4'],
            ['内容1', '内容2', '内容3', '内容4'],
            ['内容1', '内容2', '内容3', '内容4'],
            ['内容1', '内容2', '内容3', '内容4'],
            ['内容1', '内容2', '内容3', '内容4']
        ];
        Excel::create('ExcelName', function ($excel) use ($Data) {
            $excel->sheet('sheet', function ($sheet) use ($Data) {
                $sheet->rows($Data);
            });
        })->export('xlsx');
    }

    /**
     * 内容导出为 Word
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function exportWord(){
        $phpWord = new PhpWord();

        //设置默认样式
        $phpWord->setDefaultFontName('仿宋');//字体
        $phpWord->setDefaultFontSize(16);//字号

        //添加页面
        $section = $phpWord->createSection();

        //添加目录
        $styleTOC  = ['tabLeader' => \PhpOffice\PhpWord\Style\TOC::TABLEADER_DOT];
        $styleFont = ['spaceAfter' => 60, 'name' => 'Tahoma', 'size' => 12];
        $section->addTOC($styleFont, $styleTOC);

        //默认样式
        $section->addText('Hello PHP!');
        $section->addTextBreak();//换行符

        //指定的样式
        $section->addText(
            'Hello world!',
            [
                'name' => '宋体',
                'size' => 16,
                'bold' => true,
            ]
        );
        $section->addTextBreak(5);//多个换行符

        //自定义样式
        $myStyle = 'myStyle';
        $phpWord->addFontStyle(
            $myStyle,
            [
                'name' => 'Verdana',
                'size' => 12,
                'color' => '1BFF32',
                'bold' => true,
                'spaceAfter' => 20,
            ]
        );
        $section->addText('Hello laravel!', $myStyle);
        $section->addText('Hello Vue.js!', $myStyle);
        $section->addPageBreak();//分页符

        //添加文本资源
        $textrun = $section->createTextRun();
        $textrun->addText('加粗', ['bold' => true]);
        $section->addTextBreak();//换行符
        $textrun->addText('倾斜', ['italic' => true]);
        $section->addTextBreak();//换行符
        $textrun->addText('字体颜色', ['color' => 'AACC00']);

        //列表
        $listStyle = ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER];
        $section->addListItem('List Item I', 0, null, 'listType');
        $section->addListItem('List Item I.a', 1, null, 'listType');
        $section->addListItem('List Item I.b', 1, null, 'listType');
        $section->addListItem('List Item I.c', 2, null, 'listType');
        $section->addListItem('List Item II', 0, null, 'listType');
        $section->addListItem('List Item II.a', 1, null, 'listType');
        $section->addListItem('List Item II.b', 1, null, 'listType');

        //超链接
        $linkStyle = ['color' => '0000FF', 'underline' => \PhpOffice\PhpWord\Style\Font::UNDERLINE_SINGLE];
        $phpWord->addLinkStyle('myLinkStyle', $linkStyle);
        $section->addLink('http://www.baidu.com', '百度一下', 'myLinkStyle');
        $section->addLink('http://www.baidu.com', null, 'myLinkStyle');

        //添加图片(需判断图片是否存在)
        $imageStyle = ['width' => 480, 'height' => 640, 'align' => 'center'];
        $section->addImage('./storage/Image/bb.jpg', $imageStyle);
        $section->addImage('./storage/Image/cc.jpg',$imageStyle);

        //添加标题
        $phpWord->addTitleStyle(1, ['bold' => true, 'color' => '1BFF32', 'size' => 38, 'name' => 'Verdana']);
        $section->addTitle('标题1', 1);
        $section->addTitle('标题2', 1);
        $section->addTitle('标题3', 1);

        //添加表格
        $styleTable = [
            'borderColor' => '006699',
            'borderSize' => 6,
            'cellMargin' => 50,
        ];
        $styleFirstRow = ['bgColor' => '66BBFF'];//第一行样式
        $phpWord->addTableStyle('myTable', $styleTable, $styleFirstRow);

        $table = $section->addTable('myTable');
        $table->addRow(400);//行高400
        $table->addCell(2000)->addText('学号');
        $table->addCell(2000)->addText('姓名');
        $table->addCell(2000)->addText('专业');
        $table->addRow(400);//行高400
        $table->addCell(2000)->addText('1599040137');
        $table->addCell(2000)->addText('Superman');
        $table->addCell(2000)->addText('计算机网络应用');
        $table->addRow(400);//行高400
        $table->addCell(2000)->addText('16940330097');
        $table->addCell(2000)->addText('Imooc');
        $table->addCell(2000)->addText('教育学技术');

        //页眉与页脚
        $header = $section->createHeader();
        $footer = $section->createFooter();
        $header->addPreserveText('页眉');
        $footer->addPreserveText('页脚 - 页数 {PAGE} - {NUMPAGES}.');


        $fileName = date('YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $xlsTitle = iconv('utf-8', 'gb2312', $fileName);//文件名称
        header('pragma:public');
        header('Content-type:application/vnd.ms-word;charset=utf-8;name="' . $xlsTitle . '.doc"');
        header("Content-Disposition:attachment;filename=$fileName.doc");//attachment新窗口打印inline本窗口打印
        header('Content-Type: application/msword');
        ob_clean();//关键
        flush();//关键

        //生成的文档为Word2007
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        $writer->save('php://output');
    }
}
