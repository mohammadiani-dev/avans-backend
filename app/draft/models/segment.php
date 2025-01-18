<?php namespace avansdp\models;

class segment
{

    private string $table_name;

    private int $id;

    private string $name;

    private string $type;

    private string $created_at;

    private string $modified_at;

    public function __construct( int $id = null )
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . AVANS_PREFIX . 'segments';

        if( is_numeric($id) ){

            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d" , $id));
            if($item){
                $this->id = $id;
                $this->name = $item->name;
                $this->type = $item->type;
                $this->created_at = $item->created_at;
                $this->modified_at = $item->modified_at;
            }

        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getModifiedAt(): string
    {
        return $this->modified_at;
    }


    public function save(): bool|\WP_Error
    {
        $args = [];
        global $wpdb;

        if(isset($this->name)){
            $args['name'] = $this->name;
        }

        if(isset($this->type)){
            $args['type'] = $this->type;
        }


        if($this->id) {
            $args['id'] = $this->id;
            $args['modified_at'] = current_time('mysql');

            $update = $wpdb->update($this->table_name, $args, ['id' => $this->id]);

            if($update){
                return true;
            }else{
                return new \WP_Error( 'avans-db-error' , $wpdb->last_error);
            }

        }


        $insert = $wpdb->insert($this->table_name, $args);
        if($insert){
            return true;
        }else{
            return new \WP_Error( 'avans-db-error' , $wpdb->last_error);
        }


    }


}