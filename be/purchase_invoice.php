<?php
include_once("inc_config.php");
include_once("login_user_check.php");
include_once("classes/mpdf/mpdf.php");

$refno_custom = $validation->urlstring_validate($_GET['ref']);
$purchaseResult = $db->view("*", "rb_purchases", "purchaseid", "and refno_custom='{$refno_custom}' and status='active'", "purchaseid desc", "1");
if($purchaseResult['num_rows'] == 0)
{
	$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
	header("Location: purchase_view.php");
	exit();
}
$purchaseRow = $purchaseResult['result'][0];

if($purchaseRow['product_currency_code'] == 'INR')
{
	$product_currency_code = '&#8377;';
}
else
{
	$product_currency_code = $validation->db_field_validate($purchaseRow['product_currency_code']);
}

$table = "";

//<br />Type: ".$validation->db_field_validate($productPurchaseRow['tax_type'])."

$productPurchaseResult = $db->view("*", "rb_purchases", "purchaseid", "and refno_custom='{$refno_custom}' and status='active'", 'purchaseid desc');
if($productPurchaseResult['num_rows'] >= 1)
{
	$total_price = 0;
	$slr = 1;
	foreach($productPurchaseResult['result'] as $productPurchaseRow)
	{
		$table .= "<tr>";
		//$table .= "<td valign='top' class='p-5'><p align='center'>{$slr}</p></td>";
		if($productPurchaseRow['product_variant'] != "")
		{
			$table .= "<td valign='top' class='p-5'>".$validation->db_field_validate($productPurchaseRow['product_title'])." (".$validation->db_field_validate($productPurchaseRow['product_variant']).")</td>";
		}
		else
		{
			$table .= "<td valign='top' class='p-5'>".$validation->db_field_validate($productPurchaseRow['product_title'])."</td>";
		}
		$table .= "<td valign='top' class='p-5' align='center'>".$validation->db_field_validate($productPurchaseRow['quantity'])."</td>";
		$table .= "<td valign='top' class='p-5'>".($productPurchaseRow['tax_information'] == 'included' ? $product_currency_code."".$validation->price_format($productPurchaseRow['price'] - $validation->calculate_discounted_price($productPurchaseRow['tax'], $productPurchaseRow['price'])) : $product_currency_code."".$validation->price_format($productPurchaseRow['price']))."</td>";
		//$table .= "<td valign='top' class='p-5'>".$product_currency_code."".$validation->price_format($productPurchaseRow['coupon_discount'])."</td>";
		//$table .= "<td valign='top' class='p-5'>".$product_currency_code."".$validation->price_format($productPurchaseRow['shipping'])."</td>";
		$table .= "<td valign='top' class='p-5' style='font-size:29px;'>
			Rate: ".$validation->db_field_validate($productPurchaseRow['tax'])."%
			<br />
			Amount: ".($productPurchaseRow['tax_information'] == 'included' ? $product_currency_code."".$validation->price_format($validation->calculate_discounted_price($productPurchaseRow['tax'], $productPurchaseRow['price']+$productPurchaseRow['shipping']-$productPurchaseRow['coupon_discount'])) : $product_currency_code."".$validation->price_format($productPurchaseRow['taxamount']))."
		</td>";
		$table .= "<td valign='top' class='p-5'>".$product_currency_code."".$validation->db_field_validate($validation->price_format($productPurchaseRow['price']+$productPurchaseRow['shipping']+$productPurchaseRow['taxamount']))."</td>";
		$table .= "</tr>";
		
		$total_price += $productPurchaseRow['price'];
		$total_shipping += $productPurchaseRow['shipping'];
		$total_coupon_discount += $productPurchaseRow['coupon_discount'];
		$total_taxamount += $productPurchaseRow['taxamount'];
		
		$slr++;
	}
}

$table2 = "";
if($purchaseRow['coupon_discount_total'] != "" and $purchaseRow['coupon_discount_total'] != "0" and $purchaseRow['coupon_discount_total'] != "0.00")
{
	$table2 .= "<tr>";
	$table2 .= '<td colspan="4" valign="top" class="p-5"><p>Coupon Discount:</p></td>';
	$table2 .= '<td valign="top" class="p-5"><p>-'.$product_currency_code.''.$validation->price_format($purchaseRow['coupon_discount_total']).'</p></td>';
	$table2 .= "</tr>";
}

$table2 .= "<tr>";
$table2 .= '<td colspan="4" valign="top" class="p-5"><p>Wallet Amount:</p></td>';
$table2 .= '<td valign="top" class="p-5"><p>-'.$product_currency_code.''.$validation->price_format($purchaseRow['wallet_money']).'</p></td>';
$table2 .= "</tr>";

if($purchaseRow['shipping_total'] != "" and $purchaseRow['shipping_total'] != "0" and $purchaseRow['shipping_total'] != "0.00")
{
	$table2 .= "<tr>";
	$table2 .= '<td colspan="4" valign="top" class="p-5"><p>Shipping:</p></td>';
	$table2 .= '<td valign="top" class="p-5"><p>+'.$product_currency_code.''.$validation->price_format($purchaseRow['shipping_total']).'</p></td>';
	$table2 .= "</tr>";
}

$table2 .= "<tr>";
$table2 .= '<td colspan="4" valign="top" class="p-5"><p style="font-weight:bold;">Amount to be Paid:</p></td>';
$table2 .= '<td valign="top" class="p-5"><p style="font-weight:bold;">'.$product_currency_code.''.$validation->price_format($purchaseRow['final_price']-$purchaseRow['wallet_money']).'</p></td>';
$table2 .= "</tr>";

if($purchaseRow['invoicedate'] != "0000-00-00" and $purchaseRow['invoicedate'] != "")
{
	$invoicedate = "<strong>Invoice Date: </strong> ".$validation->db_field_validate($purchaseRow['invoicedate']).'<br />';
}

$html = '
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Tax Invoice/Bill - '.$validation->db_field_validate($purchaseRow['refno_custom']). '</title>
<style>
body
{
	font-size:14px;
}
.hind_text
{
	font-family: freeserif;
	font-weight:bold;
}
.footer
{
	position: absolute;
	bottom: 29px;
	text-align:center;
	width:100%;
	font-size:14px;
}
table
{
	width:100%;
}
table tr td table tr td
{
	padding:15px !important;
	
}
ol li
{
	font-size:14px;
}
.p-5
{
	padding:5px;
	border-bottom:1px solid #ccc !important;
	font-size:29px;
}
</style>
</head>
<body>

<div align="center">
	<b>XLS Solar</b><br />
	'.$validation->db_field_validate($configRow['address']).'<br />
	<br />
	<strong>Order ID: </strong> '.$validation->db_field_validate($purchaseRow['refno_custom']).'
	<br />
	<strong>Order Date: </strong> '.$validation->db_field_validate($purchaseRow['createdate']).'
	<br />
	'.$invoicedate. '
	
	
	<strong>Payment Mode: </strong> '.strtoupper($validation->db_field_validate($purchaseRow['payment_mode'])).'
	
	<br /><br />
	<b>Billing & Shipping Address</b><br />
	'.$validation->db_field_validate($purchaseRow['shipping_first_name']." ".$purchaseRow['shipping_last_name']).'
	<br />
	'.$validation->db_field_validate($purchaseRow['shipping_mobile']).''.($purchaseRow['shipping_mobile_alter'] != "" ? ", ".$validation->db_field_validate($purchaseRow['shipping_mobile_alter']) : '').'
	<br />
	'.$validation->db_field_validate($purchaseRow['shipping_address']).',
	<br />
	'.($purchaseRow['shipping_landmark'] != "" ? $validation->db_field_validate($purchaseRow['shipping_landmark'])."<br />" : '').'
	'.$validation->db_field_validate($purchaseRow['shipping_city']).', '.$validation->db_field_validate($purchaseRow['shipping_state']).', '.$validation->db_field_validate($purchaseRow['shipping_pincode']).'
	<br />
	'.$validation->db_field_validate($purchaseRow['shipping_country']).'
</div>

<br />

<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="250" valign="top" class="p-5"><p><strong>Name</strong></p></td>
		<td valign="top" class="p-5"><p align="center"><strong>Qty</strong></p></td>
		<td valign="top" class="p-5"><p><strong>Rate</strong></p></td>
		<td width="70" valign="top" class="p-5"><p><strong>Tax</strong></p></td>
		<td valign="top" class="p-5"><p><strong>Amount</strong></p></td>
	</tr>
	'.$table.'
	<tr>
		<td colspan="4" valign="top" class="p-5"><p>Total:</p></td>
		<td valign="top" class="p-5"><p>'.$product_currency_code.''.$validation->price_format($purchaseRow['final_price']+$purchaseRow['coupon_discount_total']-$purchaseRow['shipping_total']).'</p></td>
	</tr>
	'.$table2.'
	<tr>
		<td colspan="5" valign="top" class="p-5">
			<p>Amount in Words:<br>
			'.$validation->AmountInWords($purchaseRow['final_price']-$purchaseRow['wallet_money']).'</p>
		</td>
	</tr>
	<tr>
	<td colspan="5" valign="top" align="right" class="p-5">
		<p align="right">For XL Solar:<br>
		<br>
		Authorized Signatory</p></td>
	</tr>
</table>

<br />
<p style="font-size:12px; text-align:center;">Thank You!</p>

</body>
</html>
';

$mpdf = new mPDF('utf-8', array(80,236), 0, '', 2, 2, 4, 4, 9, 9);

$mpdf->default_lineheight_correction = 1.2;
// LOAD a stylesheet
$stylesheet = file_get_contents('classes/mpdf/bootstrap_pdf.css');
$mpdf->WriteHTML($stylesheet,1);    // The parameter 1 tells that this is css/style only and no body/html/text
$mpdf->SetColumns(1,'J');
$mpdf->SetTitle('Invoice');
$mpdf->WriteHTML($html);

$mpdf->Output('invoice.pdf', 'I');
exit;
?>