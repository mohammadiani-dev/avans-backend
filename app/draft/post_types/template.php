<?php namespace avansdp\post_types;

class template extends base
{
    public function __construct(){

        $this->name = AVANS_PREFIX . "template";
        $this->supports = ['title' , 'editor'];

        $this->add_ajax('patterns_list' , [$this , 'list'] , true);

        parent::__construct();
    }

    public function list()
    {
        $page = $_GET['page'] ?? 1;
        $per_page = 10;

        $data = [];

        $templates = new \WP_Query([
            'post_type' => $this->name,
            'posts_per_page' => $per_page,
            'offset' => ($page - 1) * $per_page,
        ]);

        while ($templates->have_posts()) { $templates->the_post();
            $data['posts'][] = [
                'code' => get_the_ID(),
                'title' => get_the_title(),
                'created_at' => get_the_date('Y/m/d H:i:s'),
                'type' => get_post_meta( get_the_ID() , AVANS_PREFIX . 'type', true)
            ];
        }

        $data['pagination']['total'] = $templates->found_posts;
        $data['pagination']['current'] = $page;
        $data['pagination']['per_page'] = $per_page;

        $this->success($data);
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

        $user = get_user_by($post->post_author , 'ID');

        $data = [
            'id' => $id,
            'title' => $post->post_title,
            'status' => $post_statuses[$post->post_status] ?? '',
            'author' => [
                'id' => $post->post_author ?? 1,
                'name' => $user ? $user->display_name : ''
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

