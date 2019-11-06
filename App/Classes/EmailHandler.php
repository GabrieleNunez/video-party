<?php namespace App\Classes;

// Library requirements
use Library\View;
use Library\PHPMailer;
use Library\Application;
// application requirements
// currently no application requirements

class EmailHandler
{
    // this is currently the email we are using for brad macdowall
    private $email_owner = '';

    //private $email_owner = 'gabrielenunez@thecoconutcoder.com'; // for testing only
    private $email_maintainer = 'gabrielenunez@protonmail.com';
    private $domain = '';
    private $service = null;
    private $copy_maintainer = false;

    // construct the email handler
    public function __construct($copyto_maintainer = true)
    {
        $this->service = new PHPMailer();
        $this->copy_maintainer = $copyto_maintainer;
        $this->email_owner = Application::setting('owner')['email'];
        $this->domain = Application::setting('domain');

        $mailer_settings = Application::setting('mailer');

        // if we need to send via SMTP we should support this
        if ($mailer_settings['smtp']) {
            $this->service->IsSMTP();
            $this->service->SMTPSecure = $mailer_settings['protocol'];
            $this->service->SMTPAuth = true;
            $this->service->Host = $mailer_settings['host'];
            $this->service->Mailer = 'smtp';
            $this->service->Port = $mailer_settings['port'];
            $this->service->Username = $mailer_settings['username'];
            $this->service->Password = $mailer_settings['password'];
        }
    }

    public function get_email_owner()
    {
        return $this->email_owner;
    }

    // validate the email address using a certain pattern
    public static function validate_email($email_address)
    {
        $validation_result = PHPMailer::ValidateAddress($email_address);
        return $validation_result === false || $validation_result === 0 ? false : true;
    }

    // send the email off immediatly
    // TODO add error handling
    public function send($to, $subject, $content, $replyto = false, $break_copy = false)
    {
        // Set the following email properties
        if ($replyto !== false) {
            $this->service->AddReplyTo($replyto);
        }

        $this->service->SetFrom('notifications@' . $this->domain, 'Notification Service');
        $this->service->AddAddress($to);
        $this->service->Subject = $subject;
        $this->service->Body = $content;
        $this->service->AltBody = strip_tags($content);
        $this->service->Send();

        return true;
    }

    // fire the email off to brad macdowall
    public function send_owner($subject, $content, $replyto = false)
    {
        $brad_result = $this->send($this->email_owner, $subject, $content, $replyto);
        return $brad_result;
    }

    // send maintence email
    public function send_maintainer($subject, $content, $replyto = false)
    {
        return $this->send($this->email_maintainer, $subject, $content, $replyto, true);
    }
}
