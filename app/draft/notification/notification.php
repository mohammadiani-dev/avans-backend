<?php namespace avansdp\notification;


use avansdp\traits\useDb;

class notification
{
    use useDb;
    private $user_id;

    private $notification_table_name;

    private $message_table_name;
    public function __construct( int $user_id = null )
    {

        $this->user_id = $user_id ?? get_current_user_id();

        $this->notification_table_name = $this->db->prefix . 'notifications';
        $this->message_table_name = $this->db->prefix . 'messages';
    }
    public function save( array $args = []){

        $data = $this->validate_notification_args($args);
        if( is_wp_error($data) ){
            return $data;
        }

        return $this->insert_Notification($data);
    }


    private function insert_Notification(array $data): int|false {

        $insert = $this->db->insert($this->notification_table_name, $data);

        return $insert ? $this->db->insert_id : false;
    }


    public function get_message_id( $data )
    {
        $message_id = 0;

        if(!isset($data['message']) ) {
            return new \WP_Error('not valid'  , 'متن پیام تعیین نشده است!');
        }

        $row = $this->db->get_row(
            $this->db->prepare("SELECT id FROM $this->message_table_name  WHERE message LIKE %s " , $data['message'] )
        );

        if($row){
            $message_id = $row->id;
        }else{
            $insert = $this->db->insert($this->message_table_name, ['message' => $data['message'] ]);
            if($insert){
                $message_id = $this->db->insert_id;
            }
        }

        return $message_id;
    }

    public function validate_notification_args($args){

        $insert_data = [];

        $this->user_id = $args['user_id'] ?? $this->user_id;

        if(!isset($this->user_id) || $this->user_id == 0) {
            return new \WP_Error('not valid'  , 'شناسه کاربری تعیین نشده است!');
        }
        $insert_data['user_id'] = $this->user_id;

        if(!isset($args['message_type']) ) {
            return new \WP_Error('not valid'  , 'نوع پیام تعیین نشده است!');
        }
        $insert_data['message_type'] = $args['message_type'];

        if( isset($args['variables']) ){
            $insert_data['variables'] = maybe_serialize($args['variables']);
        }

        if( isset($args['pattern_id']) ){
            $insert_data['pattern_id'] = $args['pattern_id'];
        }

        if( isset($args['scheduled_at']) ){
            $insert_data['scheduled_at'] = $args['scheduled_at'];
        }

        $message_id = $this->get_message_id( $args );
        if( !$message_id ){
            return new \WP_Error('not valid' , 'شناسه پیام نامعتبر است!');
        }else if( is_wp_error($message_id) ){
            return $message_id;
        }else{
            $insert_data['message_id'] = $message_id;
        }

        return $insert_data;
    }


}


if( !function_exists('avans_notification') ){
    function avans_notification( int $user_id = null ) : Notification
    {
        return new notification( $user_id );
    }
}

if( !function_exists('avans_enqueue_notification') ){
    function avans_enqueue_notification( array $args = [] ) : bool
    {
        return avans_notification()->save($args);
    }
}

if( !function_exists('avans_enqueue_toast') ){
    function avans_enqueue_toast( array $args = [] ) : bool
    {
        $args['message_type'] = 'toast';
        return avans_notification()->save($args);
    }
}

if( !function_exists('avans_enqueue_sms') ){
    function avans_enqueue_sms( array $args = [] ) : bool
    {
        $args['message_type'] = 'sms';
        return avans_notification()->save($args);
    }
}

if( !function_exists('avans_enqueue_email') ){
    function avans_enqueue_email( array $args = [] ) : bool
    {
        $args['message_type'] = 'email';
        return avans_notification()->save($args);
    }
}

