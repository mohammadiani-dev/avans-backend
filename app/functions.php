<?php


//require_once __DIR__ . "/models/functions.php";

$services = scandir(AVANS_PATH .'includes/admin/sms/services');
unset($services[array_search('.', $services, true)]);
unset($services[array_search('..', $services, true)]);

foreach ($services as $service) {
    if (file_exists(AVANS_PATH.'includes/admin/sms/services/'.$service.'/settings.php')) {
        include_once(AVANS_PATH.'includes/admin/sms/services/'.$service.'/settings.php');
    }
}