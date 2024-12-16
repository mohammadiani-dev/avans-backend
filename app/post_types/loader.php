<?php namespace avansdp\post_types;

use avansdp\abstracts\class_loader;

class loader extends class_loader {

    public function shouldLoadClass(string $className): bool
    {
        if( $className === 'base' || $className === 'loader'  ) {
            return false;
        }
        return true;
    }


}

(new loader(__DIR__  , '\avansdp\post_types'))->loadClasses();