<?php namespace avansdp\post_types;

class template extends base
{
    public function __construct(){

        $this->name = AVANS_PREFIX . "template";
        $this->supports = ['title' , 'editor'];

        parent::__construct();
    }
}

