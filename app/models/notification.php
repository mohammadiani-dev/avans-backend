<?php namespace avansdp\models;

class notification {

    private $id;
    private int $user_id;
    private string $type = 'toast';

    private int $template_id;

    private array $data;

    private $status = 'pending';

    private $response = null;

    private $create_at = null;
    private $scheduled_at = null;
    private $sended_at = null;

    const table_name = AVANS_PREFIX . 'notifications';

    public function __construct( $id = null ) {

        if( !empty( $id ) ) {

            $item = $this->db->get_row( $this->db->prepare('SELECT * FROM notification WHERE id = %d', $id) );

            if($item){
                $this->id = $item->id;
                $this->user_id = $item->user_id;
                $this->type = $item->type;
                $this->data = $item->data ? json_decode($item->data) : [];
                $this->template_id = $item->template_id;
                $this->status = $item->status;
                $this->scheduled_at = $item->scheduled_at;
                $this->sended_at = $item->sended_at;
                $this->create_at = $item->created_at;
            }

        }

    }


    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getType() {
        return $this->type;
    }


    public function getData()
    {
        return $this->data;
    }

    public function getTemplateId() {
        return $this->template_id;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getScheduledAt() {
        return $this->scheduled_at;
    }

    public function getsendedAt()
    {
        return $this->sended_at;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getCreateAt() {
        return $this->create_at;
    }

    public function setUserId( $user_id ) {
        $this->user_id = $user_id;
        return $this;
    }

    public function setType( $type ) {
        $this->type = $type;
        return $this;
    }

    public function setData( $data ) {
        $this->data = $data;
        return $this;
    }

    public function setTemplateId( $template_id ) {
        $this->template_id = $template_id;
        return $this;
    }

    public function setStatus( $status ) {
        $this->status = $status;
        return $this;
    }

    public function setScheduledAt( $scheduled_at ) {
        $this->scheduled_at = $scheduled_at;
        return $this;
    }

    public function setsendedAt( $sended_at ) {
        $this->sended_at = $sended_at;
    }


    public function delete()
    {
        if( $this->id ){
            global $wpdb;

            return $wpdb->delete( self::table_name , [ 'id' => $this->id ] );
        }

        return false;
    }

    public function save(): \WP_Error|int
    {

        if( !isset($this->template_id) ){
            return new \WP_Error(1001 , 'template id is not set');
        }

        $args = [
            'user_id' => $this->user_id ?? get_current_user_id(),
            'template_id' => $this->template_id,
            'status' => $this->status ?? 'pending',
            'type' => $this->type ?? 'toast',
        ];

        if( isset($this->data) ){
            $args['data'] = json_encode( $this->data );
        }

        if( isset($this->scheduled_at) ){
            $args['scheduled_at'] = $this->scheduled_at;
        }

        if( isset($this->response) ){
            $args['response'] = $this->response;
        }

        if(isset($this->sended_at) ){
            $args['sended_at'] = $this->sended_at;
        }

        global $wpdb;

        if($this->id) {

            return $wpdb->update( $wpdb->prefix . self::table_name , $args , [ 'id' => $this->id ] );
        }

        return $wpdb->insert( $wpdb->prefix . self::table_name , $args );

    }


}



