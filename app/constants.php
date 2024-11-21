<?php namespace avansdp;

class constants extends singleton {
    public function init()
    {
        if( !defined('AVANS_DB_VERSION') ){
            define("AVANS_DB_VERSION" , '5.1.0');
        }
        if( !defined('AVANS_PREFIX') ){
            define("AVANS_PREFIX" , 'avans_');
        }
    }

}