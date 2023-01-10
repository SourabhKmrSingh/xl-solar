<?php

class WhatsApp

{

	public static function sendMSG($recipient_no, $message)

	{

        global $configRow;

		$content = array(

			'apikey' => $configRow['api_key'],

			'instance' => $configRow['instance_key'],

			'number' => $recipient_no,

			'msg' => $message,

		);

		$apiUrl = "https://app.whatzapi.com/api/send-text.php?";

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

?>