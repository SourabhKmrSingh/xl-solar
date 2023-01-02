<?php
class mail
{
	public function sendmail($emails=array(), $subject, $message)
	{
		$db = new db;
		$validation = new validation;
		
		$configQueryResult = $db->view('mail_server,mail_port,mail_encryption,mail_name,mail_email,mail_password', 'rb_config', 'configid', "", "configid desc");
		$configRow = $configQueryResult['result'][0];
		
		require 'phpmailer/PHPMailerAutoload.php';
		
		$email_to = $getRow['email'];
		
		$mail = new PHPMailer();
		$mail->SetLanguage("en", 'includes/phpMailer/language/');
		$mail->IsSMTP();
		
		$mail->From = $validation->db_field_validate($configRow['mail_email']);
		$mail->FromName = $validation->db_field_validate($configRow['mail_name']);
		$mail->SMTPAuth = true;
		$mail->Host = $validation->db_field_validate($configRow['mail_server']);
		$mail->Port = $validation->db_field_validate($configRow['mail_port']);
		$mail->SMTPSecure = $validation->db_field_validate($configRow['mail_encryption']);
		$mail->Username = $validation->db_field_validate($configRow['mail_email']);
		$mail->Password = $validation->db_field_validate($configRow['mail_password']);
		foreach($emails as $email)
		{
			$mail->AddAddress($email);
		}
		$mail->IsHTML(true);
			
		$mail->Subject = $subject;
		$mail->Body    = $message;
		if($mail->Send())
		{
			return true;
		}
		else
		{
			return "Mailer Error: " . $mail->ErrorInfo;
			exit();
		}
	}
}

$mail = new mail;
?>