<?php

declare(strict_types=1);

namespace App\Infraestrutura\Adaptadores\Email;

use Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;

class PHPMailerEmail
{
    public function __construct()
    {
        
    }

    public function send(
        string $titulo,
        string $clienteEmail,
        string $clienteNome,
    )
    {
        $mail = new PHPMailer(true);
        
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp-pulse.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'contato@objetivasoftware.com.br';                     //SMTP username
            $mail->Password   = 'KsBkfM7CEta3ei';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
            //Recipients
            $mail->setFrom('naoresponda@gestorimob.com.br', 'Gestor Imobiliária');
            $mail->addAddress($clienteEmail, $clienteNome);     //Add a recipient
            //$mail->addAddress('ellen@example.com');               //Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');
        
            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
        
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $titulo;

            // gere um html com o conteudo do email teste aqui e passe para a variavel $body abaixo
            $body = '<h1>Teste</h1>
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Reiciendis illum distinctio aut dicta ipsa voluptas tenetur nobis. Labore dolores quo inventore, accusamus sunt laudantium a, quia expedita nobis in laboriosam.</p>';

            $bodyHTMLess = strip_tags($body);

            $mail->Body    = $body;
            $mail->AltBody = $bodyHTMLess;
        
            $mail->send();

            return true;

        } catch (Exception $e) {
            throw new Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}