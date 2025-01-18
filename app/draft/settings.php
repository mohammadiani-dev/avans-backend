<?php namespace  avansdp;

use avansdp\traits\useAjax;

class settings extends singleton
{

    use useAjax;

    public function init()
    {
        $this->add_ajax( 'get_settings'  , [$this , 'get_settings' ]   , true);
        $this->add_ajax( 'save_settings' , [$this , 'save_settings']  , true);
        $this->add_ajax( 'get_constants' , [$this , 'get_constants'] , true);
    }

    public function save_settings()
    {
        $settings = json_decode(stripslashes($_POST['settings']));

        $update = update_option( 'avans_settings' , json_decode(json_encode($settings), true));

        wp_send_json_success($update);
    }

    public function get_wc_order_states()
    {
        $statuses = function_exists('wc_get_order_statuses') ? wc_get_order_statuses() : [];
        $unsetStatus = array('wc-pending', 'wc-on-hold' , 'wc-cancelled' , 'wc-refunded' , 'wc-failed' , 'wc-checkout-draft');
        foreach($unsetStatus as $key) {
            unset($statuses[$key]);
        }
        return $statuses;
    }

    public function get_constants()
    {
        $post_types = $this->get_post_types();

        wp_send_json_success([
            'post_types_with_comment' => $this->get_post_types_with_comments( $post_types ),
            'post_types' => $post_types,
            'user_roles' => $this->get_user_roles(),
            'wc_order_states' => $this->get_wc_order_states(),
            'rtl' => is_rtl(),
            'language' => explode('-' , get_bloginfo('language'))[0],
        ]);

    }

    public function get_user_roles()
    {
        global $wp_roles;
        $roles = [];

        foreach ($wp_roles->roles as $key => $role) {
            $roles[$key] = translate_user_role($role['name']);
        }

        return $roles;
    }


    public function get_post_types_with_comments( $post_types )
    {

        $post_types_with_comments = array();

        foreach ($post_types as $index => $post_type) {
            if ( post_type_supports($index, 'comments') ) {
                $post_types_with_comments[$index] = $post_type;
            }
        }

        return $post_types_with_comments;

    }

    public function get_post_types()
    {
        $post_types = get_post_types(array('public' => true , 'show_ui' => true), 'objects', 'and');

        $post_type_list = [];

        foreach( $post_types as $post_type){
            if($post_type->name !== 'attachment'){
                $post_type_list[$post_type->name] = $post_type->labels->name;
            }
        }

        return $post_type_list;
    }

    public function get_settings()
    {
        $settings = get_option('avans_settings' , []);

        if(!isset($settings['view_post_type'])){
            $settings['view_post_type'] = $this->get_default_view_post_type_settings();
        }

        if(!isset($settings['wc_purchase'])) {
            $settings['wc_purchase'] = $this->get_default_wc_purchase_settings();
        }

        if(!isset($settings['lms'])) {
            $settings['lms'] = $this->get_default_lms_settings();
        }

        if(!isset($settings['register'])) {
            $settings['register'] = $this->get_default_register_settings();
        }

        if(!isset($settings['referer_register'])) {
            $settings['referer_register'] = $this->get_default_referer_register_settings();
        }

        if(!isset($settings['rating'])) {
            $settings['rating'] = $this->get_default_rating_settings();
        }

        if(!isset($settings['sharing'])) {
            $settings['sharing'] = $this->get_default_sharing_settings();
        }

        if(!isset($settings['comment'])) {
            $settings['comment'] = $this->get_default_comment_post_type_settings();
        }

        if(!isset($settings['publish'])) {
            $settings['publish'] = $this->get_default_publish_post_type_settings();
        }

        if(!isset($settings['anniversary'])) {
            $settings['anniversary'] = $this->get_default_anniversary_settings();
        }

        if(!isset($settings['daily_visit'])) {
            $settings['daily_visit'] = $this->get_default_daily_visit_settings();
        }

        $settings = $this->get_complete_settings( $settings );

        wp_send_json_success( $settings );
    }

    public function get_default_daily_visit_settings()
    {
        return [
            'active' => true,
            'score' => null,
            'delay' => null,
            'limit' => [
                'score' => null,
                'per'  => 'day'
            ],
            'expire' => [
                'time' => null,
                'unit' => 'day'
            ],
            'condition' => []
        ];
    }
    public function get_default_anniversary_settings()
    {
        return [
            'register_date' => [
                'active' => false,
                'score' => null,
                'limit' => [
                    'score' => null,
                    'per'  => 'day'
                ],
                'expire' => [
                    'time' => null,
                    'unit' => 'day'
                ],
                'condition' => []
            ],
            'birth_date' => [
                'active' => false,
                'score' => null,
                'limit' => [
                    'score' => null,
                    'per'  => 'day'
                ],
                'expire' => [
                    'time' => null,
                    'unit' => 'day'
                ],
                'condition' => []
            ]
        ];
    }

    public function get_default_sharing_settings()
    {
        return [
            'active' => true,
            'score' => null,
            'delay' => null,
            'limit' => [
                'score' => null,
                'per'  => 'day'
            ],
            'expire' => [
                'time' => null,
                'unit' => 'day'
            ],
            'condition' => []
        ];
    }
    public function get_default_rating_settings()
    {
        return [
            'active' => true,
            'score' => null,
            'delay' => null,
            'limit' => [
                'score' => null,
                'per'  => 'day'
            ],
            'expire' => [
                'time' => null,
                'unit' => 'day'
            ],
            'condition' => []
        ];
    }
    public function get_default_referer_register_settings()
    {
        return [
            'active' => false,
            'score'  => null,
            'limit' => [
                'score' => null,
                'per'  => 'day'
            ],
            'expire' => [
                'time' => null,
                'unit' => 'day'
            ],
            'condition' => []
        ];
    }

    public function get_default_register_settings()
    {
        return [
            'active' => false,
            'score'  => null,
            'old_users' => false,
            'expire' => [
                'time' => null,
                'unit' => 'day'
            ],
        ];
    }

    public function get_default_lms_settings()
    {
        return [
            'enroll' => [
                'active' => false,
                'score' => null,
                'limit' => [
                    'score' => null,
                    'per'  => 'day'
                ],
                'expire' => [
                    'time' => null,
                    'unit' => 'day'
                ],
                'condition' => []
            ],
            'complete_lesson' => [
                'active' => false,
                'score' => null,
                'limit' => [
                    'score' => null,
                    'per'  => 'day'
                ],
                'expire' => [
                    'time' => null,
                    'unit' => 'day'
                ],
                'condition' => []
            ],
            'complete_course' => [
                'active' => false,
                'score' => null,
                'limit' => [
                    'score' => null,
                    'per'  => 'day'
                ],
                'expire' => [
                    'time' => null,
                    'unit' => 'day'
                ],
                'condition' => []
            ],
            'test' => [
                'active' => false,
                'score' => null,
                'limit' => [
                    'score' => null,
                    'per'  => 'day'
                ],
                'expire' => [
                    'time' => null,
                    'unit' => 'day'
                ],
                'condition' => []
            ]
        ];
    }

    public function get_default_wc_purchase_settings()
    {
        return [
            'active' => false,
            'status' => 'wc-processing',
            'type'  => 'fixed_score',
            'score'  => null,
            'min_price' => null,
            'next_time' => [
                'time' => null,
                'per'  => 'day'
            ],
            'limit' => [
                'score' => null,
                'per'  => 'day'
            ],
            'expire' => [
                'time' => null,
                'unit' => 'day'
            ],
            'condition_user' => [],
            'condition_content' => []
        ];
    }
    public function get_default_view_post_type_settings()
    {
        return [
            'post' => [
                'active' => true,
                'score' => null,
                'delay' => null,
                'next_time' => [
                    'time' => null,
                    'per'  => 'day'
                ],
                'limit' => [
                    'score' => null,
                    'per'  => 'day'
                ],
                'expire' => [
                    'time' => null,
                    'unit' => 'day'
                ],
                'condition' => []
            ],
            'page' => [
                'active' => true,
                'score' => null,
                'delay' => null,
                'next_time' => [
                    'time' => null,
                    'per'  => 'day'
                ],
                'limit' => [
                    'score' => null,
                    'per'  => 'day'
                ],
                'expire' => [
                    'time' => null,
                    'unit' => 'day'
                ],
                'condition' => []
            ]
        ];
    }


    public function get_default_comment_post_type_settings()
    {
        return [
            'post' => [
                'active' => false,
                'score' => null,
                'first_comment' => false,
                'limit' => [
                    'score' => null,
                    'per'  => 'day'
                ],
                'expire' => [
                    'time' => null,
                    'unit' => 'day'
                ],
                'condition' => []
            ],
        ];
    }

    public function get_default_publish_post_type_settings()
    {
        return [
            'post' => [
                'active' => false,
                'score' => null,
                'limit' => [
                    'score' => null,
                    'per'  => 'day'
                ],
                'expire' => [
                    'time' => null,
                    'unit' => 'day'
                ],
                'condition' => []
            ],
        ];
    }

    public function get_complete_settings($settings)
    {
        //check view post type settings
        $post_types = $this->get_post_types();
        foreach ($post_types as $index => $post_type) {
            if(!isset($settings['view_post_type'][$index])){
                $settings['view_post_type'][$index] = [
                    'active' => false,
                    'score' => null,
                    'delay' => null,
                    'next_time' => [
                        'time' => null,
                        'per'  => 'day'
                    ],
                    'limit' => [
                        'score' => null,
                        'per'  => 'day'
                    ],
                    'expire' => [
                        'time' => null,
                        'unit' => 'day'
                    ],
                    'condition' => []
                ];
            }
        }

        $keys = array_keys($settings['view_post_type']);
        foreach ($keys as $key) {
            if(!in_array( $key , array_keys($post_types) )){
                unset($settings['view_post_type'][$key]);
            }
        }

        //check publish post type settings
        foreach ($post_types as $index => $post_type) {
            if(!isset($settings['publish'][$index])){
                $settings['publish'][$index] = [
                    'active' => false,
                    'score' => null,
                    'delay' => null,
                    'next_time' => [
                        'time' => null,
                        'per'  => 'day'
                    ],
                    'limit' => [
                        'score' => null,
                        'per'  => 'day'
                    ],
                    'expire' => [
                        'time' => null,
                        'unit' => 'day'
                    ],
                    'condition' => []
                ];
            }
        }

        $keys = array_keys($settings['publish']);
        foreach ($keys as $key) {
            if(!in_array( $key , array_keys($post_types) )){
                unset($settings['publish'][$key]);
            }
        }

        //check comment post type settings
        $post_types_width_comment = $this->get_post_types_with_comments($post_types);
        foreach ($post_types_width_comment as $index => $post_type) {
            if(!isset($settings['comment'][$index])){
                $settings['comment'][$index] = [
                    'active' => false,
                    'score' => null,
                    'first_comment' => false,
                    'limit' => [
                        'score' => null,
                        'per'  => 'day'
                    ],
                    'expire' => [
                        'time' => null,
                        'unit' => 'day'
                    ],
                    'condition' => []
                ];
            }
        }
        $keys = array_keys($settings['comment']);
        foreach ($keys as $key) {
            if(!in_array( $key , array_keys($post_types_width_comment) )){
                unset($settings['comment'][$key]);
            }
        }

        return $settings;
    }
}