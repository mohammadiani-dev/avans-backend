<?php namespace avansdp\taxonomies;

class loader extends \avansdp\post_types\loader {

}

(new loader(__DIR__  , '\avansdp\taxonomies'))->loadClasses();