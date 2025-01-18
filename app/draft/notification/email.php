<?php
use \avansdp\notification\template\EmailTemplate;
class email
{
    private $to;
    private $subject;
    private $template;
    public function __construct(string $to, string $subject , EmailTemplate $template){
        $this->to = $to;
        $this->subject = $subject;
        $this->template = $template;
    }

    public function send()
    {
        return wp_mail( $this->to , $this->subject , $this->template->render() , $this->get_headers() );
    }

    public function get_headers() : string
    {
        $email_site = get_bloginfo('admin_email');
        $site_name = get_bloginfo('name');

        $headers  = "From: $site_name < $email_site >\n";
        $headers .= "X-Sender: $site_name < $email_site >\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $headers .= "X-Priority: 1\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\n";

        return $headers;
    }

}
