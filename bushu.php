<?php
/**
 * Created by PhpStorm.
 * User: guoyexuan
 * Date: 2017/10/16
 * Time: 下午4:07
 */

$cmd_arr = array(
    'start',
    'goback',
);

if(!extension_loaded('pcntl'))
{
    exit("Please install pcntl!\n");
}

if(!extension_loaded('posix'))
{
    exit("Please install posix!\n");
}


if(isset($argv[1]) && in_array(strtolower($argv[1]),$cmd_arr))
{
    switch($argv[1])
    {
        case 'start':
            start_deploy();
            break;
        case 'goback':
            break;
    }
}
else
{
    //safeEcho("Usage: deploy script [OPTIONS] \n\n [-? or help] display help infoclear  \n [start] start delopy project \n [goback] goback git project\n");
    safeEcho("Usage: {start|goback}\n");
}

//从git部署项目
function start_deploy()
{
    $startTime = microtime(true);

    $display_lenght = 16;

    $get_git_log_cmd = '/usr/bin/env git log --pretty=format:"%H|%an|%ae|%s|%cr"';
    exec($get_git_log_cmd,$res,$return_var);

    foreach($res as $k=>$v)
    {
        $return_res[] = explode('|',$v);
    }

    if(isset($return_res[0]) && !empty($return_res[0]))
    {
        safeEcho("\033[1A\n\033[K-------------------------\033[0;34m 当前版本 \033[0m---------------------------------\n\033[0m");
        safeEcho("DelopyScript version:1.0          PHP Version:". PHP_VERSION. "\n");
        safeEcho("--------------------------------------------------------------------\n");
        safeEcho(
            "\033[1;33mID\033[0m". str_pad('',12 + 2 - strlen('ID')).
            "\033[1;33mAuthor\033[0m". str_pad('', $display_lenght + 2 - strlen('Author')).
            "\033[1;33mEmail\033[0m". str_pad('',$display_lenght + 2 - strlen('Email')).
            "\033[1;33mTime\033[0m". str_pad('',$display_lenght + 2 - strlen('Time')).
            "\033[1;33mdesc\033[0m \033[1;33m\n"
        );

        foreach ($return_res as $key=>$value)
        {
            safeEcho("\033[1;36m{$key}\033[0m". str_pad('',12 + 2 - strlen($key)).
                "\033[1;31m{$value[1]}\033[0m". str_pad('', $display_lenght + 2 - strlen($value[1])).
                "\033[1;31m{$value[2]}\033[0m". str_pad('',$display_lenght + 2 - strlen($value[2])).
                "\033[1;31m{$value[4]}\033[0m". str_pad('',$display_lenght + 2 - strlen($value[4])).

                "\033[1;31m{$value[3]}\033[0m \033[1;31m\n");
        }
        safeEcho("Pleae Enter ID : \n");
        $Id = input_param();


        switch ($Id)
        {
            case array_key_exists($Id,$return_res):
                $git_pull_cmd = '/usr/bin/env git pull';

                exec($git_pull_cmd,$pull_res,$return_var);

                if($pull_res[0] != 'Already up-to-date.')
                {
                    safeEcho("Git有冲突,解决冲突之后从新运行此脚本!\n");
                    exit();
                }
                else
                {
                    safeEcho("正在更新...\n");

                    $file_name = date('Y-m-d',time());

                    $output_zip_cmd = "git archive --format zip --output {$file_name}.zip master";

                    exec($output_zip_cmd,$zip_res,$return_var);

                    $stopTime = microtime(true);

                    $total = round(($stopTime-$startTime),0);

                    for ($i = 1; $i <= $total; $i++) {
                        printf("Progress: [%-50s] %d%% Done\r", str_repeat('#',$i/$total*50), $i/$total*100);
                        sleep(1);
                    }
                    echo "\n";
                    echo "Done!\n";
                }
                break;
            default:
                safeEcho("Enter ID Error! Please try angin Run Script!!!\n");
        }

    }

}



function safeEcho($msg)
{
    if (!function_exists('posix_isatty') || posix_isatty(STDOUT)) {
        echo $msg;
    }
}

function input_param()
{
    $fp = fopen('/dev/stdin', 'r');
    $input = fgets($fp, 255);
    fclose($fp);
    $input = chop($input);
    return $input;
}

?>