<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class SendEmail{
    
    public function methodTest($params = null)
    {
        $mail = new PHPMailer(true);

		try {
			//Server settings
			$mail->isSMTP();
			$mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
			$mail->Username   = '3lton.leao@gmail.com';                     // SMTP username
			$mail->Password   = '4lu43um4ilus40!';                               // SMTP password
			$mail->Port       = 587;                              // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
			$mail->From = '3lton.leao@gmail.com';

			//Recipients
			$mail->addAddress('3lton.leao@gmail.com', 'Elton Leao');     // Add a recipient

			// Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Here is the subject';
			$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
			$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
			echo 'Message has been sent';
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
    }
}
