<?php namespace avansdp;

class constants extends singleton {
    public function init()
    {
        if( !defined('AVANS_DB_VERSION') ){
            define("AVANS_DB_VERSION" , '7.4.0');
        }
        if( !defined('AVANS_PREFIX') ){
            define("AVANS_PREFIX" , 'avans_');
        }
        if( !defined('AVANS_DOMAIN') ){
            define("AVANS_DOMAIN" , 'avans');
        }
    }

}