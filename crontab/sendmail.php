<?php
require __DIR__ . '/../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require __DIR__ . '/../config/config.php';
$mail = new SendMail($config);
$mail->start();

class SendMail {
    private $config;
    public function __construct($config) {
        $this->config = $config;
        $this->connection = mysqli_connect($this->config['DB_HOST'], $this->config['DB_USER'], $this->config['DB_PWD'], $this->config['DB_NAME']);
        // Check connection
        if (mysqli_connect_errno($this->connection))
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $this->connection->query("set names utf8");

    }
    public function start() {
       $query = "SELECT * FROM story WHERE is_read = 0"; 
       $row = $this->fetchRow($this->connection,$query);
       foreach($row as $section) {
            $ret = $this->send($section);
            if($ret) {
                $sql = "UPDATE story SET is_read = 1 WHERE id = {$section['id']}";
                $this->connection->query($sql);
            
            }
       }
    
    }
    public function fetchRow($con, $query) {
        $res = $con->query($query);
        $result = [];
        while($row = $res->fetch_assoc()) {
            $result[] = $row;
        }
        return $result;
    }
    public function send($section) {
    
        $mail = new PHPMailer;
        $mail->Charset='UTF-8';

        //$mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $this->config['MAIL_HOST'];  // Specify main and backup SMTP servers
        //$mail->Host = 'smtp.163.com;smtp2.example.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $this->config['MAIL_USERNAME'];                 // SMTP username
        $mail->Password = $this->config['MAIL_PASSWORD'];                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = $this->config['MAIL_PORT'];                                    // TCP port to connect to

        $mail->setFrom($this->config['MAIL_FROM'], '小说更新助手');
        $mail->addAddress($this->config['MAIL_TO'], 'ljmready');     // Add a recipient
/*
 *        $mail->addAddress('ellen@example.com');               // Name is optional
 *        $mail->addReplyTo('info@example.com', 'Information');
 *        $mail->addCC('cc@example.com');
 *        $mail->addBCC('bcc@example.com');
 *
 *        $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
 *        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
 */
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = "=?utf-8?B?".base64_encode($section['title']) . "?=";
        $mail->Body    = $section['content'];
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        } else {
            return true;
        }
    }
}
