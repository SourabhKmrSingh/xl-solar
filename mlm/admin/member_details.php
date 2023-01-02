<?php
include_once("inc_config.php");
include_once("login_user_check.php");
include_once("classes/mpdf/mpdf.php");

$regid = $validation->urlstring_validate($_GET['regid']);
$memberResult = $db->view("*", "mlm_registrations", "regid", "and regid='{$regid}'");
if($memberResult['num_rows'] == 0)
{
	$_SESSION['error_msg'] = "Error Occurred. Please try again!!!";
	header("Location: purchase_view.php");
	exit();
}
$memberResultRow = $memberResult['result'][0];

$html = "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>{$memberResultRow['membership_id']} - {$memberResultRow['first_name']} {$memberResultRow['last_name']}</title>
    <style>
        *,
        *::before,
        *::after{
            font-family: Arial, Helvetica, sans-serif;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
       
        .center{
			text-align: center;
			 font-family: Arial, Helvetica, sans-serif;
        }
        table{
			width: 70%;
			 font-family: Arial, Helvetica, sans-serif;

        }
        .container{
            margin: 0 auto;
			max-width: 45%;
			 font-family: Arial, Helvetica, sans-serif;
        }
        img{
            max-width: 250px;
        }
        tbody{
            width: 100%;
        }
        td:first-child{
            width: 100%;
            font-weight: bold;
        }
        td{
			min-width: 345px;
			width: 190px;
            padding: 8px;
            vertical-align: baseline;
        }
        p{
            line-height: .2rem;
        }
        .profile-pic{
            
        }
        .profile-pic img{
            max-width: 150px;
            max-height: 150px;
        }
       
    </style>
</head>
<body>
    <div>
        <h4 style='margin: 4rem 0;' class='center'><u>{$memberResultRow['membership_id']} - {$memberResultRow['first_name']} {$memberResultRow['last_name']}</u></h4>
       
    </div>
    
    <div class='container'>
        <table style='width: 100%;'>
            <tbody>
                
                <tr>
                    <td>Name :</td>
                    <td>{$memberResultRow['first_name']} {$memberResultRow['last_name']}</td>
					<td rowspan='12'>
						 <img src='../content/{$memberResultRow['imgName']}' alt='' style='width: 150px;border: 1px solid black;padding: 5px;'>
					</td>
                </tr>
                <tr>
                    <td>Father Name:</td>
                    <td>{$memberResultRow['father_name']}</td>
                </tr>
                <tr>
                    <td>Mother Name:</td>
                    <td>{$memberResultRow['mother_name']}</td>
                </tr>
                <tr>
                    <td>Date of Birth (DOB):</td>
                    <td>{$memberResultRow['date_of_birth']}</td>
                </tr>
                <tr>
                    <td>Phone No.:</td>
                    <td>{$memberResultRow['mobile']}</td>
                </tr>
                <tr>
                    <td>Email ID:</td>
                    <td>{$memberResultRow['email']}</td>
                </tr>
                 <tr>
                    <td>Address:</td>
                    <td>{$memberResultRow['address']}</td>
                </tr>
                <tr>
                    <td>Bank Details: </td>
                    <td colspan='2'><p>Name: {$memberResultRow['bank_name']}</p> <br>
						<p>Account Name: {$memberResultRow['account_name']}</p><br>
						<p>Bank Swift / IFSC CODE: {$memberResultRow['ifsc_code']}</p><br>
                        <p>Account No.:{$memberResultRow['account_number']}</p><br>
                    </td>
                </tr>
                <tr>
                    <td>KYC Document: </td>
                    <td colspan='2'>{$memberResultRow['document']} : {$memberResultRow['document_number']}</td>
                </tr>
                
            </tbody>
		 </table>
    </div>
    
</body>
</html>
";
//  <tr>
//                     <td style='vertical-align: middle;'>Signature / Thumb: </td>
//                     <td><img src='../content/{$memberResultRow['signature']}' alt=''></td>
//                 </tr>

$title = $memberResultRow['membership_id'] . $memberResultRow['first_name'] .$memberResultRow['last_name'];
$title_1 = $title . '.pdf';
$mpdf = new mPDF('utf-8');

$mpdf->default_lineheight_correction = 1.2;
// LOAD a stylesheet
// $stylesheet = file_get_contents('classes/mpdf/bootstrap_pdf.css');
// $mpdf->WriteHTML($stylesheet,1);    // The parameter 1 tells that this is css/style only and no body/html/text
// $mpdf->SetColumns(1,'J');
$mpdf->SetTitle($title);
$mpdf->WriteHTML($html);

$mpdf->Output($title_1, 'I');
exit;
?>