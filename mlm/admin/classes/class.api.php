<?php

class api

{

	public function sendSMS($recipient_no, $message, $senderID='',$template_id)

	{

		if($senderID != "")

		{

			$senderID = $senderID;

		}

		else

		{

			$senderID = "SUNLIF";

		}

		

		$content = array(

			'username' => 'sunlief',

			'password' => '123456',

			'key' => '2608D2341AE3B6',

			'campaign' => '205',

			'routeid' => '5',

			'type' => 'text',

			'senderid' => $senderID,

			'contacts' => $recipient_no,

			'msg' => $message,

			'time' => '',

			'template_id' => $template_id

		);

		

		$apiUrl = "http://skietsocial.in/app/smsapi/index.php?";

		foreach($content as $key => $val)

		{

			$apiUrl .= $key.'='.rawurlencode($val).'&';

		}

		$apiUrl = rtrim($apiUrl, "&");

		

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $apiUrl);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);

		curl_close($ch);

		

		return $response;																																			

	}

}



$api = new api;

?>