<?php namespace avansdp;

class constants extends singleton {
    public function init()
    {
        if( !defined('AVANS_DB_VERSION') ){
            define("AVANS_DB_VERSION" , '7.5.0');
        }
        if( !defined('AVANS_PREFIX') ){
            define("AVANS_PREFIX" , 'avans_');
        }
        if( !defined('AVANS_DOMAIN') ){
            define("AVANS_DOMAIN" , 'avans');
        }
        if(!defined('AVANS_DIR_PATH')){
            define('AVANS_DIR_PATH' , plugin_dir_path(AVANS_PATH));
        }
        if(!defined('AVANS_DIR_PATH')){
            define('AVANS_MODULES' , plugin_dir_path(AVANS_PATH) . '/app/modules/');
        }
        if(!defined('AVANS_DIR_URL')){
            define('AVANS_DIR_URL' , plugin_dir_url(AVANS_PATH));
        }

    }

}