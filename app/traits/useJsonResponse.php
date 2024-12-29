<?php  namespace avansdp\traits;

trait useJsonResponse
{
    public function response($response, $status_code = null)
    {
        wp_send_json($response, $status_code );
    }

    public function success($response , $status_code = null)
    {
        wp_send_json_success($response, $status_code );
    }

    public function error($response , $status_code = null)
    {
        wp_send_json_error($response, $status_code );
    }

}