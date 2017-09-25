#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: guoyexuan
 * Date: 2017/9/25
 * Time: 下午9:11
 */

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

function _init()
{
    $display_lenght = 16;

    $host_list = array(
        1=>array(
            'host'=>'test',
            'name'=>'root',
            'pass'=>'111111.',
            'desc'=>'aa'
        ),
        2=>array(
            'host'=>'test',
            'name'=>'root',
            'pass'=>'22222.',
            'desc'=>'aa'
        ),
        3=>array(
            'host'=>'test',
            'name'=>'guoyexuan',
            'pass'=>'33333333.',
            'desc'=>'aa-guoyexuan'
        ),
    );

    safeEcho("\033[1A\n\033[K-----------------------\033[0;34m 请选择登录ID \033[0m-------------------------------\n\033[0m");
    safeEcho("LoginScript version:1.0          PHP version:". PHP_VERSION. "\n");
    safeEcho("--------------------------------------------------------------------\n");
    safeEcho("\033[1;33mID\033[0m". str_pad('',12 + 2 - strlen('ID')).
             "\033[1;33mHost\033[0m". str_pad('', $display_lenght + 2 - strlen('Host')).
             "\033[1;33mName\033[0m". str_pad('',$display_lenght + 2 - strlen('Name')).
             "\033[1;33mdesc\033[0m \033[1;33m\n");

    foreach ($host_list as $key=>$value)
    {
        safeEcho("\033[1;36m{$key}\033[0m". str_pad('',12 + 2 - strlen($key)).
                 "\033[1;31m{$value['host']}\033[0m". str_pad('', $display_lenght + 2 - strlen($value['host'])).
                 "\033[1;31m{$value['name']}\033[0m". str_pad('',$display_lenght + 2 - strlen($value['name'])).
                 "\033[1;31m{$value['desc']}\033[0m \033[1;31m\n");
    }
    safeEcho("\n\033[1;36m请输入要登录的机器ID:\033\n");

    $param = input_param();
    if(array_key_exists($param,$host_list))
    {
        shell_exec(sprintf("/usr/bin/sshpass -p {$host_list[$param]['pass']} ssh %s@%s",$host_list[$param]['name'],$host_list[$param]['host']));
    }
    else
    {
        exit("EnterError!\n");
    }
}
_init();