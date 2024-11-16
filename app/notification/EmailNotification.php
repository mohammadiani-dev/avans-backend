<?php

namespace avansdp\notification;

class EmailNotification implements Notification
{
    private $user;

    public  function  __construct( \WP_User $user , EmailTemplate $template , $data )
    {
        $this->user = $user;
    }

    public function send(): bool
    {
        $email = $this->user->user_email;


    }
}