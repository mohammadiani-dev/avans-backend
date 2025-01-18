<?php  namespace avansdp\taxonomies;

abstract class base
{

    protected string $name = 'test';
    protected string | array $object_type = 'post';

    protected bool $hierarchical = false;

    protected bool $show_ui = false;

    protected array $labels = array();

    protected bool $show_admin_column = false;

    protected bool $query_var = false;

    protected bool $meta_box_cb = false;

    protected bool $show_in_rest = false;

    protected bool $rewrite = false;

    public function __construct(){
        $this->register();
    }

    private function register()
    {

        register_taxonomy($this->name , $this->object_type , array(
            'labels'        => $this->labels,
            'hierarchical'  => $this->hierarchical,
            'show_ui'       => $this->show_ui,
            'show_admin_column' => $this->show_admin_column,
            'query_var'     => $this->query_var,
            'meta_box_cb'   => $this->meta_box_cb,
            'show_in_rest'  => $this->show_in_rest,
            'rewrite'       => $this->rewrite
        ));
    }


}