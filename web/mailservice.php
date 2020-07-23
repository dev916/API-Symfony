<?php

	    //$this->connect();
		        require 'phpmailer/PHPMailerAutoload.php';
		
                $email ='info@herecut.net';

				$password = 'o4K#h%aY75$';

	       

				$to_id = $_GET['toid'];

				$message = $_GET['message'];

				$subject = $_GET['subject'];

				$mail = new PHPMailer;
				
				$mail->isSMTP();

				$mail->Host = 'smtp.1and1.com';
	
				$mail->Port = '587';

				$mail->SMTPSecure = 'tls';
				
				$mail->SMTPAuth = true;

				$mail->Username = $email;

				$mail->Password = $password;

				$mail->setFrom('info@herecut.net', 'HereCut');

				$mail->addReplyTo('info@herecut.net', '');

				$mail->addAddress($to_id);

				$mail->Subject = $subject;

				$mail->msgHTML($message);

				if (!$mail->send())

				{

				   $error = "Mailer Error: " . $mail->ErrorInfo;

				    $error;

				}
?>				