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
use avansdp\models\user;
use avansdp\settings;

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

        new constants();
        new languages();
        new database();

        require_once __DIR__ . '/app/functions.php';
        require_once __DIR__ . '/app/post_types/loader.php';
        require_once __DIR__ . '/app/taxonomies/loader.php';


        new settings();


        if( is_admin() && !defined('DOING_AJAX') ) {
            avans_user(1)->add_transaction([
                'score' => 8,
                'action' => 'admin',
                'meta' => array(
                    'title' => 'salam'
                )
            ]);
        }

    }
}


avans::getInstance();
