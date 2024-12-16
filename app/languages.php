<?php namespace avansdp;

class languages extends singleton
{


    public function init() : void
    {
        if ( version_compare( $GLOBALS['wp_version'], '6.7', '<' ) ) {
            load_plugin_textdomain('avans', false, dirname(plugin_basename(AVANS_DOMAIN )).'/languages');
        } else {
            load_textdomain('avans' , plugin_dir_path(AVANS_DOMAIN ) . 'languages/avans-' . determine_locale() . '.mo' );
        }
    }
}