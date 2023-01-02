<?php 
include_once('inc_config.php');

if(isset($_POST['membershipid'])){
    
    $membershipid = $validation->input_validate($_POST['membershipid']);
    $memberDetailsQuery = $db->view('*',"mlm_registrations","regid"," and membership_id='{$membershipid}'");

    $memberDetails = $memberDetailsQuery['result'][0];
    if($memberDetailsQuery['num_rows'] >= 1){
        echo $memberDetails['username'];
    }else{
        echo $memberDetailsQuery['num_rows'];
    }
}

?>