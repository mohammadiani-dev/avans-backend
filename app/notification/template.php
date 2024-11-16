<?php namespace  avansdp\notification;

class EmailTemplate{

    private $file_path;

    public  function  __construct($file_path)
    {
        $this->file_path = $file_path;
    }

    public function render( $data )
    {

    }

}