<?php
use avansdp\models\template;
use avansdp\models\user;


if( !function_exists('avans_template') ){
    function avans_template( int $template_id = null ) : template
    {
        return new template( $template_id );
    }
}




if( !function_exists('avans_user') ){
    function avans_user( int $user_id = null ) : user
    {
        return new user($user_id);
    }
}