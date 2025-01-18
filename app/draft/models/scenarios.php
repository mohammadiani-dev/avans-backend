<?php namespace avansdp\models;

class scenarios
{
    private string $table_name;
    private int $id;
    private string $type;
    private string $name;

    private string $hook;

    private string $run_time;

    private string $usage_time;

    private string $next_time;

    private string $created_at;
    private string $modified_at;

    public function __construct( int $id = null )
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . AVANS_PREFIX . 'scenarios';

        if( is_numeric($id) ){

            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d" , $id));
            if($item){
                $this->id = $id;
                $this->name = $item->name;
                $this->type = $item->type;
                $this->hook = $item->hook;
                $this->run_time = $item->run_time;
                $this->usage_time = $item->usage_time;
                $this->next_time = $item->next_time;
                $this->created_at = $item->created_at;
                $this->modified_at = $item->modified_at;
            }

        }
    }
    
    public function getId(): int
    {
        return $this->id;
    }


    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getHook(): string
    {
        return $this->hook;
    }

    public function setHook(string $hook): void
    {
        $this->hook = $hook;
    }

    public function getRunTime(): string
    {
        return $this->run_time;
    }

    public function setRunTime(string $run_time): void
    {
        $this->run_time = $run_time;
    }

    public function getUsageTime(): string
    {
        return $this->usage_time;
    }

    public function setUsageTime(string $usage_time): void
    {
        $this->usage_time = $usage_time;
    }

    public function getNextTime(): string
    {
        return $this->next_time;
    }

    public function setNextTime(string $next_time): void
    {
        $this->next_time = $next_time;
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

        if(isset($this->hook)){
            $args['hook'] = $this->hook;
        }

        if(isset($this->next_time)){
            $args['next_time'] = $this->next_time;
        }

        if(isset($this->run_time)){
            $args['run_time'] = $this->run_time;
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

