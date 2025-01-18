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


use avansdp\constants;
use avansdp\database;
use avansdp\languages;

require_once  __DIR__ . '/vendor/autoload.php';

if(file_exists(__DIR__ . '/develop.php')){
    require_once __DIR__ . '/develop.php';
}

class avans
{

    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function __construct()
    {
        add_action('init' , [$this , 'init']);
        add_action('init', [$this , 'allow_cors']);
        add_action('admin_footer' , [$this , 'load_local_scripts']);

    }

    public function load_local_scripts(){
        ?>
        <style>
            .toplevel_page_avans_plugin_iframe  img {
                position: relative;
                top: -4px;
            }
            .avans-badge-menu{
                background: #32496a;
                font-size: 80%;
                padding:4px 8px;
                border-radius: 12px;
                color: #ffffff;
                font-family: avans, Tahoma,Arial,sans-serif;
            }
        </style>
        <?php
    }

    public function allow_cors() {

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    public function init() : void
    {
        if(!defined('AVANS_PATH')) define('AVANS_PATH' , __FILE__ );
        new constants();
        new languages();
        new database();

         require_once __DIR__ . '/app/functions.php';

        // require_once __DIR__ . '/app/post_types/loader.php';
        // require_once __DIR__ . '/app/taxonomies/loader.php';


        // new settings();


        // if( is_admin() && !defined('DOING_AJAX') ) {
        //     avans_user(1)->add_transaction([
        //         'score' => 8,
        //         'action' => 'admin',
        //         'meta' => array(
        //             'title' => 'salam'
        //         )
        //     ]);
        // }

    }
}


avans::getInstance();



add_action("admin_menu" , function () {

    add_menu_page(
        __('آوانس', 'avans_plugin_iframe'),
        __('آوانس <span class="avans-badge-menu">جدید</span>', 'avans_plugin_iframe'),
        'manage_avans',
        'avans_plugin_iframe',
        'load_iframe_content',
         plugin_dir_url(__FILE__) . '/assets/img/logo-menu.png',
        45
    );

});

function load_iframe_content(){
    ?>
    <div class="load_settings">
        <iframe id="app-iframe"  src="http://localhost:5173/#/" frameborder="0"></iframe>
    </div>
    <script>

        document.addEventListener("DOMContentLoaded", function () {

            const iframe = document.getElementById("app-iframe");

            // ارسال hash به iframe
            window.addEventListener("hashchange", function () {
                const currentHash = window.location.hash;
                iframe.contentWindow.postMessage({ type: "updateHash", hash: currentHash }, "*");
            });
            // دریافت پیام از iframe
            window.addEventListener("message", function (event) {
                if (event.data.type === "updateHash") {
                    window.location.hash = event.data.hash;
                }
            });
        });
    </script>
    <style>
        .load_settings iframe{
            width: 100%;
            height: calc(100vh - 40px);
        }
        #wpfooter{
            display: none;
        }
        #wpbody-content{
            padding: 0;
        }
        #wpcontent{
            padding: 0;
        }
    </style>
    <?php
}