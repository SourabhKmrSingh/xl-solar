<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "dashboard";
$membership_id = $_SESSION['mlm_membership_id'];




// Level data Inactive


function getMembers($ids){
    global $db;
	$result = array();
    $members = array();
    $activeUser = array();
	if(count($ids) == 1){
		$sponsor_id = $ids[0];
		$values = $db->view('*',"mlm_registrations",'regid'," and sponsor_id = '{$sponsor_id}' and sponsor_id != ''");
	}else{
		$keys =  implode("','", $ids);
		$values = $db->view('*',"mlm_registrations",'regid'," and sponsor_id IN ('$keys') and  sponsor_id != ''");
	}
    
    if($values['num_rows'] >= 1){
        foreach($values['result'] as $value){
            if($value['createdate'] >= '2021-05-11'){
                 array_push($result, $value['membership_id']);
                 if($value['status'] == 'active'){
                    array_push($activeUser, $value['membership_id']);  
                 }
            }
            array_push($members, $value['membership_id']);
        }
    }
    return array('members'=>$members,'results'=>$result,'activeUser' => $activeUser);
}
$membership_id = 'SL001';


$values = $db->view('*',"mlm_registrations",'regid'," and sponsor_id='{$membership_id}'");
$members1= array();
$result1 = array();
$activeUser1 = array();
foreach($values['result'] as $value){
    if($value['createdate'] >= '2021-05-11'){
        array_push($result1, $value['membership_id']);
        if($value['status'] >= 'active'){
            array_push($activeUser1, $value['membership_id']);
        }
    }
    array_push($members1, $value['membership_id']);
}
$Level1 = count($result1);
$Levelactive1 = count($activeUser1); 

	
$data2 = getMembers($members1);
// print_r($data2);

$data3 = getMembers($data2['members']);

print_r($data3);

$data4 = getMembers($data3['members']);

// print_r($data4);
// $Level2 = count($members2);
// $members3 = getMembers($members2);
// $Level3 = count($members3);
// $members4 = getMembers($members3);
// $Level4 = count($members4);
// $members5 = getMembers($members4);
// $Level5 = count($members5);
// $members6 = getMembers($members5);
// $Level6 = count($members6);
// $members7 = getMembers($members7);
// $Level7 = count($members7);







?>