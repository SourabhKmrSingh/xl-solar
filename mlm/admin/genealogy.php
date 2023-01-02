<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "register";

$regid = $validation->urlstring_validate($_GET['regid']);
$registerQueryResult = $db->view('*', 'mlm_registrations', 'regid', "and regid = '$regid'");
$registerRow = $registerQueryResult['result'][0];

$membership_id = $validation->db_field_validate($registerRow['membership_id']);
?>
<!DOCTYPE html>
<html LANG="en">
<head>
<?php include_once("inc_title.php"); ?>
<?php include_once("inc_files.php"); ?>
<script>
function passwordmatch()
{
	if($("#password").val() != $("#confirm_password").val())
	{
		alert("Password and Confirm Password should be Same!");
		$("#password").val("");
		$("#confirm_password").val("");
	}
}
</script>
</head>
<body>
<div ID="wrapper">
<?php include_once("inc_header.php"); ?>
<div ID="page-wrapper">
<div CLASS="container-fluid">
<div CLASS="row">
	<div CLASS="col-lg-12">
		<h1 CLASS="page-header">Genealogy (Team View)</h1>
	</div>
</div>

<?php
$FILE_LOC = FILE_LOC;
function getAllDownlines($parent)
{
    global $db, $FILE_LOC, $validation;
   
    $treeResult = $db->view('membership_id,imgName,username,status', 'mlm_registrations', 'regid', "and sponsor_id='$parent'", 'regid asc');
    // echo "<script>alert('$parent')</script>";
	if($treeResult['num_rows'] >= 1)
	{
		echo "<ul>";
		foreach($treeResult['result'] as $treeRow)
		{
			echo "<li>";
			$img_src= $FILE_LOC.''.$validation->db_field_validate($treeRow['imgName']);
			echo "<a href='javascript:void(0)'><p>"; 
		 	echo $validation->db_field_validate($treeRow['username']);
			echo "</p>";
			if($treeRow['imgName'] != "") { 
				echo "<img src='{$img_src}' class='img-responsive' />"; 
			} else {
				echo "<img src='images/user-icon.png' class='img-responsive' />";
			} 
			echo "<p>" . $validation->db_field_validate($treeRow['membership_id'])."</p>";
			if($treeRow['status'] == "inactive") {
						echo "<p class='pending'>Approval Pending</p>";
			}
			echo "</a>";

			
			getAllDownlines($treeRow['membership_id']);
			
			echo "</li>";
		}
		echo "</ul>";
	}
}
?>

<div class="form-rows-custom mt-3">
	<div class="row mb-3">
		<div class="col-12">
			
			<div class="tree w-mc">
				<ul>
					<li>
						<a href="javascript:void(0)">
							<p><?php echo $validation->db_field_validate($registerRow['username']); ?></p>
							<?php if($registerRow['imgName'] != "") { ?>
								<img src="<?php echo FILE_LOC.''.$validation->db_field_validate($registerRow['imgName']); ?>" class="img-responsive" />
							<?php } else { ?>
								<img src="images/user-icon.png" class="img-responsive" />
							<?php } ?>
							<p><?php echo $validation->db_field_validate($registerRow['membership_id']); ?></p>
						</a>

						<?php
						// echo $registerRow['membership_id'];
						getAllDownlines($registerRow['membership_id']);
						?>
					</li>
				</ul>
			</div>
			
		</div>
	</div>
</div>

</div>
</div>
</div>
</body>
</html>