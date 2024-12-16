<?php namespace avansdp\notification;

use avansdp\singleton;


class hook extends singleton
{
    public function init()
    {
        add_action('avans_after_change_score' , [$this , 'send_notification_after_change_score']);
    }

    public function send_notification_after_change_score( $data ){


    }
}