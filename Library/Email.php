<?php namespace Library;

use Library\Exceptions\EmailException;

class Email
{
    private $mailer;

    public function __construct()
    {
        $settings = Application::setting('mailer');
        $this->mailer = new \Library\PHPMailer();
        if ($settings['smtp']) {
            $this->mailer->isSMTP();
            $this->mailer->Host = $settings['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $settings['username'];
            $this->mailer->Password = $settings['password'];
            $this->mailer->SMTPSecure = $settings['protocol'];
            $this->mailer->Port = $settings['port'];
        } else {
            $this->mailer->isMail();
        }
        $this->mailer->isHtml = $settings['html'];
        $this->mailer->From = $settings['from'];
        $this->mailer->FromName = $settings['name'];
    }

    public function address($address)
    {
        $this->mailer->addAddress($address);
    }

    public function subject($subject)
    {
        $this->mailer->Subject = $subject;
    }

    public function body($body)
    {
        $this->mailer->Body = $body;
    }

    public function alternateBody($alternate)
    {
        $this->mailer->AltBody = $alternate;
    }

    public function send()
    {
        $success = $this->mailer->send();
        if (!$success) {
            \Library\Printout::write($this->mailer->ErrorInfo);
            throw new EmailException($this->mailer->ErrorInfo);
        }
    }
}

?>
