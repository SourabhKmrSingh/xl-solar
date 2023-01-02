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

			'username' => 'rudraahousing',

			'password' => '123456',

			'key' => '4600524668A2B7',

			'campaign' => '174',

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