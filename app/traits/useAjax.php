<?php  namespace avansdp\traits;

trait useAjax
{
    public function add_ajax( string $name , callable $callback , bool $public = false )
    {
        add_action( "wp_ajax_avans_{$name}" , $callback );
        if($public){
            add_action("wp_ajax_nopriv_avans_{$name}" ,  $callback);
        }
    }
}