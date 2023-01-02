<?php
class validation
{
	public $input;
	public $query_string;
	public $field;
	public $url;
	
	public function input_validate($test_input)
	{
		global $connect;
		$this->input = trim(strip_tags(htmlspecialchars(mysqli_real_escape_string($connect, $test_input))));
		return $this->input;
	}

	public function urlstring_validate($query_string_value)
	{
		global $connect;
		$this->query_string=strip_tags(htmlspecialchars(mysqli_real_escape_string($connect, $query_string_value)));
		//$this->query_string=preg_replace('/[^A-Za-z0-9 @%._-]/', '', $this->query_string);
		$this->query_string=str_replace("'"," ",$this->query_string);
		return $this->query_string;
	}

	public function db_field_validate($field_name)
	{
		$this->field = htmlspecialchars_decode($field_name);
		return $this->field;
	}
	
	public function getplaintext($html, $numchars)
	{
		$html = strip_tags($html);
		$html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
		$full_html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
		$html = mb_substr($html, 0, $numchars, 'UTF-8');
		if(strlen($full_html) > $numchars)
		{
			$html .= "…";
		}
		return $html;
	}
	
	public function friendlyURL($inputString)
	{
		$this->url = strtolower($inputString);
		$this->patterns = $this->replacements = array();
		$this->patterns[0] = '/(&amp;|&)/i';
		$this->replacements[0] = '-and-';
		$this->patterns[1] = '/[^a-zA-Z01-9]/i';
		$this->replacements[1] = '-';
		$this->patterns[2] = '/(-+)/i';
		$this->replacements[2] = '-';
		$this->patterns[3] = '/(-$|^-)/i';
		$this->replacements[3] = '';
		$this->url = preg_replace($this->patterns, $this->replacements, $this->url);
		return $this->url;
	}
	
	public function timecount($datetime, $full = false)
	{
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		//print_r($string);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
	
	public function search_filter_enable()
	{
		$url_search_address = $_SERVER['REQUEST_URI'];
		$url_search_address = explode("?", $url_search_address);
		$_SESSION['search_filter'] =  $url_search_address[1];
	}
	
	public function timezone($selected = '')
	{
		$OptionsArray = timezone_identifiers_list();
			$select = '<select name="timezone" CLASS="form-control" ID="timezone" required >';
			$select .= '<option VALUE="">--select--</option>';
			while (list ($key, $row) = each ($OptionsArray) ){
				$select .= '<option value="'.$row.'"';
				$select .= ($row == $selected ? ' selected' : '');
				$select .= '>'.$row.'</option>';
			}
			$select.='</select>';
		return $select;
	}
	
	public function update_permission()
	{
		if($_SESSION['per_update'] == 0)
		{
			echo "<script>alert('You do not have permission to access this section');</script>";
			echo "<script>location.replace('home.php');</script>";
			die;
		}
	}
	
	public function write_permission()
	{
		if($_SESSION['per_write'] == 0)
		{
			echo "<script>alert('You do not have permission to access this section');</script>";
			echo "<script>location.replace('home.php');</script>";
			die;
		}
	}
	
	public function delete_permission()
	{
		if($_SESSION['per_delete'] == 0)
		{
			echo "<script>alert('You do not have permission to access this section');</script>";
			echo "<script>location.replace('home.php');</script>";
			die;
		}
	}
	
	public function read_permission()
	{
		if($_SESSION['per_read'] == 0)
		{
			echo "<script>alert('You do not have permission to access this section');</script>";
			echo "<script>location.replace('home.php');</script>";
			die;
		}
	}
	
	public function admin_permission()
	{
		if($_SESSION['be_type'] != "admin")
		{
			echo "<script>alert('You do not have permission to access this section');</script>";
			echo "<script>location.replace('home.php');</script>";
			die;
		}
	}
	
	public function date_format_custom($date)
	{
		$db = new db;
		
		$configQueryResult = $db->view('date_format', 'rb_config', 'configid', "", "configid desc");
		$configRow = $configQueryResult['result'][0];
		
		if($date != "")
		{
			return date($configRow['date_format'], strtotime($date));
		}
	}
	
	public function time_format_custom($time)
	{
		$db = new db;
		
		$configQueryResult = $db->view('time_format', 'rb_config', 'configid', "", "configid desc");
		$configRow = $configQueryResult['result'][0];
		
		if($time != "")
		{
			return date($configRow['time_format'], strtotime($time));
		}
	}
	
	public function convertToReadableSize($size)
	{
		$base = log($size) / log(1024);
		$suffix = array("", "KB", "MB", "GB", "TB");
		$f_base = floor($base);
		return round(pow(1024, $base - floor($base)), 1) . ' '.$suffix[$f_base];
	}
	
	public function getuseripaddr()
	{
		if(!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		return $ip;
	}
	
	public function calculate_discount($total, $amount)
	{
		$discount = $total - $amount;
		$discount = ($discount / $total) * 100;
		//$discount = bcdiv($discount, 1, 2).'%';
		$discount = number_format($discount, 2).'%';
		return $discount;
	}
	
	public function calculate_discounted_price($discount, $amount)
	{
		$price = ($discount / 100) * $amount;
		//$price = floor($price);
		return $price;
	}
	
	public function price_format($price)
	{
		$price = number_format($price, 2);
		return $price;
	}
	
	public function AmountInWords(float $amount)
	{
		$amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
		$amt_hundred = null;
		$count_length = strlen($num);
		$x = 0;
		$string = array();
		$change_words = array(0 => '', 1 => 'One', 2 => 'Two',
			3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
			7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
			10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
			13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
			16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
			19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
			40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
			70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
		$here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
		while( $x < $count_length ) {
			$get_divider = ($x == 2) ? 10 : 100;
			$amount = floor($num % $get_divider);
			$num = floor($num / $get_divider);
			$x += $get_divider == 10 ? 1 : 2;
			if ($amount) {
				$add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
				$amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
				$string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
				'.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
				'.$here_digits[$counter].$add_plural.' '.$amt_hundred;
			}
			else $string[] = null;
		}
		$implode_to_Rupees = implode('', array_reverse($string));
		$get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
		" . $change_words[$amount_after_decimal % 10]) . ' Paise' : '';
		return ($implode_to_Rupees ? $implode_to_Rupees . 'Rupees ' : '') . $get_paise;
	}
}

$validation = new validation;
?>