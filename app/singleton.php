<?php namespace  avansdp;

abstract class singleton
{
    private static $instance = null;

    public function __CONSTRUCT()
    {
        $this->init();
    }

    public abstract function init();

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function __clone() {}
    public function __wakeup() {}
}