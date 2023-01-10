<?php
$members = array();
function getAllDownlines($parent)
{
    global $db,$members;
    $treeResult = $db->view('membership_id,imgName,username,status', 'mlm_registrations', 'regid', "and sponsor_id='$parent' and status = 'active'", 'regid asc');

	if($treeResult['num_rows'] >= 1)
	{
		foreach($treeResult['result'] as $treeRow)
		{
            array_push($members,$treeRow['membership_id']);
			getAllDownlines($treeRow['membership_id']);
		}
	}
	return;
}



getAllDownlines($_SESSION['mlm_membership_id']);


$members = implode(",",$members);


?>