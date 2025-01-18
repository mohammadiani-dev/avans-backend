<?php namespace avansdp\models;

class template {

    private int $id;
    private string $title = '';
    private string $content = '';

    private string $type = 'sms';
    private array $settings = [];

    private string $status = 'draft';


    public function __construct( $id = null ) {

        if( !empty( $id ) && get_post_type( $id ) == AVANS_PREFIX . 'template' ) {

            $post = get_post( $id );
            $this->id = $post->ID;
            $this->title = $post->post_title;
            $this->content = $post->post_content;
            $this->status = $post->post_status;
            $this->type = get_post_meta( $id , 'template_type' , true );
            $this->settings = get_post_meta( $id , 'template_settings' , true );
        }

    }

    public function setTitle( $title ): static
    {
        $this->title = $title;
        return $this;
    }

    public function setContent( $content ): static
    {
        $this->content = $content;
        return $this;
    }

    public function setSettings( $settings ): static
    {
        $this->settings = $settings;
        return $this;
    }

    public function setStatus( $status ): static
    {
        $this->status = $status;
        return $this;
    }

    public function setType( $type ): static
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSettings(){
        return $this->settings;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function delete() : void
    {
        if( $this->id ){
            wp_delete_post( $this->id );
        }
    }

    public function save(): \WP_Error|int
    {

        $args = [
            'post_type' => AVANS_PREFIX . 'template',
            'post_title' => $this->title,
            'post_content' => $this->content,
            'post_status' => $this->status,
            'meta_input' => array(
                'template_settings' => serialize($this->settings),
                'template_type' => $this->type
            )
        ];

        if($this->id) {
            $args['ID'] = $this->id;

            return wp_update_post($args);

        }

        return wp_insert_post($args);

    }


}



