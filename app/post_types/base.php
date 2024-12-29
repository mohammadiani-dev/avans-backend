<?php  namespace avansdp\post_types;

use avansdp\traits\useAjax;
use avansdp\traits\useJsonResponse;

abstract class base
{
    use useAjax;
    use useJsonResponse;
    protected string $name = 'test';
    protected string $label = 'test';

    protected string $description = 'this is a test description';

    protected array $supports = ['title' , 'thumbnail' , 'excerpt' , 'editor' , 'author' , 'revisions'];
    protected array $labels = array();

    protected bool $hierarchical = false;

    protected bool $public = false;
    protected bool $show_ui = false;
    protected bool $show_in_menu = false;
    protected bool $show_in_nav_menus = false;
    protected bool $show_in_admin_bar = false;

    protected bool $can_export = true;

    protected bool $exclude_from_search = true;

    protected bool $publicly_queryable = false;

    protected bool $has_archive = false;

    protected int $menu_position = 5;

    protected bool $show_in_rest = false;

    protected string $capability_type = 'post';

    public function __construct()
    {
        $this->register();
        $this->add_ajax('save_post_' . $this->name , [$this , 'save'] , true );
    }

    private function register()
    {
        register_post_type( $this->name ,  array(
            'label'               => $this->label,
            'description'         => $this->description,
            'labels'              => $this->labels,
            'supports'            => $this->supports,
            'hierarchical'        => $this->hierarchical,
            'public'              => $this->public,
            'show_ui'             => $this->show_ui,
            'show_in_menu'        => $this->show_in_menu,
            'show_in_nav_menus'   => $this->show_in_nav_menus,
            'show_in_admin_bar'   => $this->show_in_admin_bar,
            'menu_position'       => $this->menu_position,
            'can_export'          => $this->can_export,
            'has_archive'         => $this->has_archive,
            'exclude_from_search' => $this->exclude_from_search,
            'publicly_queryable'  => $this->publicly_queryable,
            'capability_type'     => $this->capability_type,
            'show_in_rest'        => $this->show_in_rest,
        ));
    }

    public abstract function save();


}