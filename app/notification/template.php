<?php
class template
{
    private $message;
    private $data;

    public function __construct( $message , array $data)
    {
        $this->message = $message;
        $this->data = $data;
    }

    public function render()
    {

        if ($this->message) {
            foreach ($this->data as $index => $var) {
                $this->message = str_replace('{' . $index . '}', $var, $this->message);
            }


        }


    }


}