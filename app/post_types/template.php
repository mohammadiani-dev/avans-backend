<?php namespace avansdp\post_types;

class template extends base
{
    public function __construct(){

        $this->name = AVANS_PREFIX . "template";
        $this->supports = ['title' , 'editor'];

        parent::__construct();
    }


    public function find(){

        $id = $_REQUEST['id'];
        if( empty($id) || get_post_type($id) !== $this->name ) {
            $this->error('شناسه مطلب نا معتبر است!');
        }

        $post = get_post($id);
        if(!$post instanceof \WP_Post){
            $this->error('شناسه مطلب نا معتبر است!');
        }

        $post_statuses = get_post_statuses();

        $data = [
            'id' => $id,
            'title' => $post->post_title,
            'status' => $post_statuses[$post->post_status] ?? '',
            'author' => [
                'id' => get_the_author_ID($id),
                'name' => get_user_by($post->post_author , 'ID')->display_name
            ],
            'type'  => get_post_meta($id , AVANS_PREFIX . 'type' , true),
            'message'  => get_post_meta($id , AVANS_PREFIX . 'message' , true),
            'created_at' => get_the_date('Y/m/d H:i:s' , $post ),
            'modified_at' => get_the_modified_date( 'Y/m/d H:i:s' , $post )
        ];


        $this->success($data);

    }

    public function save()
    {
        $data = json_decode(stripslashes($_POST['data']));

        if(empty($data->title)){
            $this->error('عنوان تعریف نشده است!');
        }

        $args = [
            'post_type' => $this->name,
            'post_title' => $data->title,
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'meta_input' => [
                AVANS_PREFIX . 'type' => $data->type,
                AVANS_PREFIX . 'message' => $data->message
            ]
        ];

        if( !empty($data->id) && get_post_type( (int)$data->id ) == $this->name ){
            $args['ID'] = (int)$data->id;

            $result = wp_update_post($args);
        }else{
            $result = wp_insert_post($args);
        }


        if(is_wp_error($result)){
            $this->error(
                $result->get_error_message() ,
                $result->get_error_code()
            );
        }

        $this->success($result);

    }

}

