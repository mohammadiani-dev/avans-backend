<?php namespace avansdp\post_types;

class template extends base
{
    public function __construct(){

        $this->name = AVANS_PREFIX . "template";
        $this->supports = ['title' , 'editor'];

        parent::__construct();
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

