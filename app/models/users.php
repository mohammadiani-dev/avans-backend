<?php

class users{

    public $user_id;

    public function __construct( int $user_id = null )
    {
        if( isset( $user_id ) ){
            $this->user_id = $user_id;
        }
    }

    public function change_score( float $score , string $type , array $args = null )
    {

    }

}