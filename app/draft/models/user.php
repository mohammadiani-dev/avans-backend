<?php namespace avansdp\models;

use WP_Error;
use avansdp\traits\useDb;
use avansdp\traits\useTime;
use function avansdp\notification\avans_enqueue_toast;
use function avansdp\notification\avans_notification;

class user {

    use useTime;
    private string $table_name;
    private string $table_meta_name;
    private int $user_id;

    private float $score;
    private float $score_valid;

    /**
     * @param int|null $user_id
     */
    public function __construct(int $user_id = null )
    {
        global $wpdb;

        if($user_id) {
            if(get_user_by('ID', $user_id)){
                $this->user_id = $user_id;
            }
        }else{
            $this->user_id = get_current_user_id();
        }

        $this->table_name = $wpdb->prefix . AVANS_PREFIX . 'point_transactions';
        $this->table_meta_name = $wpdb->prefix . AVANS_PREFIX . 'point_transactions_meta';
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return bool|int
     */
    public function save_meta(string $key , mixed $value) : bool|int
    {
        return update_user_meta( $this->user_id, AVANS_PREFIX . $key, $value );
    }

    /**
     * @param string $key
     * @param bool $single
     * @return mixed
     */
    public function get_meta(string $key , bool $single = true ): mixed
    {
        return get_user_meta( $this->user_id, AVANS_PREFIX . $key , $single );
    }

    /**
     * @param array $args
     * @return bool|WP_Error
     */
    public function add_transaction(array $args ) : bool|WP_Error
    {

        $data = $this->validate_transaction_args($args);
        if ( is_wp_error($data) ) {
            return $data;
        }

        $log_id = $this->insert_transaction($data);
        if( !$log_id ){
            return false;
        }

        if( isset( $data['meta'] ) ) {
            $insert_meta = $this->insert_transaction_meta($log_id, $data['meta']);
            if (is_wp_error($insert_meta)) {
                return $insert_meta;
            }
        }

        $this->update_score($data['score']);

        do_action('avans_after_change_score' , $data );

        (new notification())->setType('toast')->setTemplateId(1)->setData([
            'score' => $data['score'],
        ])->save();

        return true;
    }

    /**
     * @param int $log_id
     * @param array $meta
     * @return WP_Error|bool
     */
    private function insert_transaction_meta(int $log_id , array $meta ) : WP_Error|bool
    {
        global $wpdb;

        foreach ($meta as $key => $value) {
            $insert_meta = $wpdb->insert($this->table_meta_name, [
                'log_id'     => $log_id,
                'meta_key'   => sanitize_text_field($key),
                'meta_value' => maybe_serialize($value),
            ]);

            if (!$insert_meta) {
                $wpdb->delete($this->table_meta_name, ['log_id' => $log_id]);
                $wpdb->delete($this->table_name, ['id' => $log_id]);
                
                return new WP_Error('insert_error' ,__('pointing metadata was not saved!' , AVANS_DOMAIN));
            }
        }

        return true;
    }

    /**
     * @param array $data
     * @return int|false
     */
    private function insert_transaction(array $data): int|false {
        global $wpdb;

        unset($data['meta']);

        $insert = $wpdb->insert($this->table_name, $data);

        return $insert ? $wpdb->insert_id : false;
    }

    /**
     * @param array $args
     * @return array|WP_Error
     */
    private function validate_transaction_args(array $args ) : array|WP_Error
    {
        //initialize default data
        $defaults = [
            'score'  => 1,
            'type' => 'admin',
            'expire' => -1,
            'data'   => [],
        ];

        //merge default data with args;
        $data = wp_parse_args( $args,  $defaults);


        $insert_data = [];

        //check user id value
        if( $this->user_id == 0 ){
            return new WP_Error('not_valid' , __('User ID must be a value greater than zero!' , AVANS_DOMAIN));
        }
        $insert_data['user_id'] = $this->user_id;

        //check score value
        if( !is_numeric($data['score']) || $data['score'] == 0 ){
            return new WP_Error('not_valid' , __('score is not set or is zero!' , AVANS_DOMAIN));
        }
        $insert_data['score'] = (float)$data['score'];

        //check action value
        if( !is_string($data['type']) || empty($data['type']) ){
            return new WP_Error('not_valid' , __('Activity type is not set!' , AVANS_DOMAIN) );
        }
        $insert_data['type'] = sanitize_text_field($data['type']);

        //check expire value
        if( $data['expire'] > 0 ){
            $insert_data['expire_date'] = self::get_next_time( $data['expire'] );
        }

        $insert_data['total'] = $this->get_score(true) + $insert_data['score'];

        if(isset($data['meta']) && is_array($data['meta'])){
            $insert_data['meta'] = array_map('sanitize_text_field', $data['meta']);
        }

        return apply_filters('avans_insert_transaction_data', $insert_data );
    }

    /**
     * @param float $score
     * @return void
     */
    private function update_score(float $score ) : void{

        $before_score = $this->get_score();
        $this->score = $before_score + $score;

        $this->save_meta('user_score', $this->score);

        $before_score_valid = $this->get_score(true);
        $this->score_valid = $before_score_valid + $score;

        $this->save_meta('user_score_valid', $this->score_valid);
    }

    /**
     * @param bool $is_valid
     * @return float
     */
    public function get_score(bool $is_valid = false ) : float
    {
        if($is_valid){

            if( isset($this->score_valid) ){
                return $this->score_valid;
            }

            $this->score_valid = (float)$this->get_meta('user_score_valid');

            return $this->score_valid;
        }


        if( isset($this->score) ){
            return $this->score;
        }

        $this->score = (float)$this->get_meta('user_score');

        return $this->score;
    }


}