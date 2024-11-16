<?php namespace avansdp\notification;

interface Notification
{
    public function send(): bool;
}