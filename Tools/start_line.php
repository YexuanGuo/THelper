<?php
/**
 * Created by PhpStorm.
 * User: guoyexuan
 * Date: 2017/7/20
 * Time: 下午4:05
 */

ini_set('date.timezone','Asia/Shanghai');
ini_set('error_reporting', 0);

include_once("./showTerm.php");

class SpiderLinePic
{


    private $helper;


    private $idCardSaveDir = './idCard';

    private $stuCardSaveDir = './stuCard';

    private $startFlag = 1;

    public function __construct()
    {
        $this->helper = new colorShowCLI();
        $this->helper->showColoredString("--------------------\nspider start...\n--------------------\n", 'green');
        $log_name = 'userId';


        do {
            self::spider_line_filer();
            $this->startFlag++;
            if ($this->startFlag == 50) {
                exit();
            }
            sleep(1);
        } while ($this->startFlag < 50);

    }

    public function spider_line_pic()
    {
        $url = "http://pic.xxxxxx.com/detail?no=$this->startFlag";
        $result = $this->helper->get($url);
        if($result)
        {
            preg_match_all('/<img class="main-image" id="imageid" src="(.*?)">/',$result,$pic_url_arr);

            $pic_url = $pic_url_arr[1][0];

            $card_name = sprintf("./line/%s.jpg",$this->helper->getTime().'_'.$this->startFlag);
            $this->helper->curl_download($pic_url,$card_name);
        }
    }

    public function spider_line_filer()
    {
        $pic_url = "http://pic.xxxxxxxxxx.com/api/v1/list?p=$this->startFlag&from=P/1.0.0";
        $this->helper->writeLog('pageNumber:' . $this->startFlag, 'INFO', 'pageNumber');

        $result  = json_decode($this->helper->get($pic_url),true);
        if($result['resultCode'] == 0 && count($result['list']) > 0)
        {
            foreach ($result['list'] as $k=>$v)
            {
                $extension_name = substr(strrchr($v['fname'], '.'), 1);
                $card_name = sprintf("./line/%s.".$extension_name,$this->helper->getTime().'_'.$this->startFlag);
                $this->helper->curl_download($v['fname'],$card_name);
            }
        }
        else
        {
            exit();
        }
    }
}

$start = new SpiderLinePic();

?>