<?php
    
/*
 * display info to CLI, Command Tools.
 * created by guoyexuan 
 * 2017-01-19 11:48:18
 * LastModify:YexuanGuo at WangJing!
 * LastModifyTime: 2017-07-20 11:00:49
 */
class Thelper{

        private $foreground_colors = array();

        private $background_colors = array();  
   
        private $headers =
                array(
                        "Content-type: application/json;charset='utf-8'",
                        "Accept: application/json",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                );

        public function __construct()
        {
            $this->headers = $this->headers;
            $this->foreground_colors['black'] = '0;30';  
            $this->foreground_colors['dark_gray'] = '1;30';  
            $this->foreground_colors['blue'] = '0;34';  
            $this->foreground_colors['light_blue'] = '1;34';  
            $this->foreground_colors['green'] = '0;32';  
            $this->foreground_colors['light_green'] = '1;32';  
            $this->foreground_colors['cyan'] = '0;36';  
            $this->foreground_colors['light_cyan'] = '1;36';  
            $this->foreground_colors['red'] = '0;31';  
            $this->foreground_colors['light_red'] = '1;31';  
            $this->foreground_colors['purple'] = '0;35';  
            $this->foreground_colors['light_purple'] = '1;35';  
            $this->foreground_colors['brown'] = '0;33';  
            $this->foreground_colors['yellow'] = '1;33';  
            $this->foreground_colors['light_gray'] = '0;37';  
            $this->foreground_colors['white'] = '1;37';  
   
            $this->background_colors['black'] = '40';  
            $this->background_colors['red'] = '41';  
            $this->background_colors['green'] = '42';  
            $this->background_colors['yellow'] = '43';  
            $this->background_colors['blue'] = '44';  
            $this->background_colors['magenta'] = '45';  
            $this->background_colors['cyan'] = '46';  
            $this->background_colors['light_gray'] = '47';  
        }  

        /*
         * CLI显示带颜色的dialog
         */
        public function showColoredString($string, $foreground_color = null, $background_color = null)
        {
            $colored_string = "";  
   
            if (isset($this->foreground_colors[$foreground_color]))
            {
                $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";  
            }    
            if (isset($this->background_colors[$background_color]))
            {
                $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";  
            }  
   
            $colored_string .=  $string . "\033[0m";  
   
            echo $colored_string;  
        }  
   
        public function getForegroundColors()
        {
            return array_keys($this->foreground_colors);  
        }  


        public function getBackgroundColors()
        {
            return array_keys($this->background_colors);  
        }  

        /*
         * curl get 请求
         */
        public function get($url, $cookie = null, $referer = null, $userAgent = null, $time_out = 10)
        {
            $ch = curl_init();
            $headers = array(
                        "Content-type: application/json;charset='utf-8'",
                        "Accept: application/json",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        'X-Request:JSON',
                        'X-Requested-With:XMLHttpRequest'
                );
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, $time_out);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            // curl_setopt($ch, CURLOPT_PROXY, "http://111.222.333.4:110");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1');
            $output = curl_exec($ch);

            curl_close($ch);
            if($output === false){
                 return false;
            }else{
                 return $output;
            }
        }

        /*
         * CURL 请求
         */
        public function CreateRequest($HttpUrl,$HttpMethod,$COMMON_PARAMS,$secretKey, $PRIVATE_PARAMS, $isHttps)
        {
            $FullHttpUrl = $HttpUrl."/v2/index.php";

            $ReqParaArray = array_merge($COMMON_PARAMS, $PRIVATE_PARAMS);
            ksort($ReqParaArray);

            $SigTxt = $HttpMethod.$FullHttpUrl."?";

            $isFirst = true;
            foreach ($ReqParaArray as $key => $value)
            {
                if (!$isFirst)
                {
                    $SigTxt = $SigTxt."&";
                }
                $isFirst= false;

                if(strpos($key, '_'))
                {
                    $key = str_replace('_', '.', $key);
                }

                $SigTxt=$SigTxt.$key."=".$value;
            }

            $Signature = base64_encode(hash_hmac('sha1', $SigTxt, $secretKey, true));


            $Req = "Signature=".urlencode($Signature);
            foreach ($ReqParaArray as $key => $value)
            {
                $Req=$Req."&".$key."=".urlencode($value);
            }

            if($HttpMethod === 'GET')
            {
                if($isHttps === true)
                {
                    $Req="https://".$FullHttpUrl."?".$Req;
                }
                else
                {
                    $Req="http://".$FullHttpUrl."?".$Req;
                }

                /*关闭SSL验证*/
                $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );  

                $Rsp = file_get_contents($Req,false,stream_context_create($arrContextOptions));

            }
            else
            {
                if($isHttps === true)
                {
                    $Rsp= self::SendPost("https://".$FullHttpUrl,$Req,$isHttps);
                }
                else
                {
                    $Rsp= self::SendPost("http://".$FullHttpUrl,$Req,$isHttps);
                }
            }

            return $Rsp;
        }

        /*
         * curl post
         */
        public function SendPost($FullHttpUrl,$Req,$isHttps)
        {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $Req);

            curl_setopt($ch, CURLOPT_URL, $FullHttpUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if ($isHttps === true) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,  false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  false);
            }

            $result = curl_exec($ch);

            return $result;
        }

        /*
         * curl下载文件,成功return 1 失败return 0;
         */
        public function curl_download($url, $dir)
        {
            $ch = curl_init($url);
            $fp = fopen($dir, "wb");
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $res=curl_exec($ch);
            $curl_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($curl_code == 200)
            {
                curl_close($ch);
                fclose($fp);
                return true;
            }
            else
            {
                return false;
            }


        }

        /*
         * 获取毫秒级时间戳
         */
        public function getTime()
        {
            list($t1, $t2) = explode(' ', microtime());
            return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
        }

        public function writeLog($log,$logType,$logName = false)
        {
            file_put_contents('./run_log/'.date('Y-m-d').'_'.$logName.".log",date('H:i:s')." - [$logType] - ".$log.PHP_EOL,FILE_APPEND);
        }

}
?>