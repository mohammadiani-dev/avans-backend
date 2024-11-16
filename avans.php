<?php
/*
Plugin Name: avans new face
Plugin URI: https://www.zhaket.com/web/wp-score-gamification
Description: Powerful tool for your business
Author: Yousef Mohammadiani
Version: 6.0.0
Text Domain: avans_plugin
Domain Path: /languages
Author URI: http://mohammadiani.com
*/

require_once  __DIR__ . '/vendor/autoload.php';


function allow_cors() {
    // هدرهایی که به سرور اجازه می‌دهند تا درخواست‌ها از دامنه‌های دیگر را بپذیرد
    header('Access-Control-Allow-Origin: *'); // می‌توانید برای امنیت بیشتر این را به دامنه خاص تغییر دهید
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    // اگر درخواست OPTIONS باشد، پاسخی برای پیش‌چک ارسال می‌شود
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}
add_action('init', 'allow_cors');


class avans
{
    public function __construct()
    {
        add_action('init' , [$this , 'init']);
    }

    public function init() : void
    {
        new \avansdp\settings();
    }
}

new avans;