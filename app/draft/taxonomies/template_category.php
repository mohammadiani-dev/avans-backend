<?php namespace avansdp\taxonomies;

class template_category extends base {

    public function __construct() {
        $this->name = AVANS_PREFIX . 'template_category';
        $this->object_type = AVANS_PREFIX . 'template';

        parent::__construct();
    }

}