<?php 
include_once('inc_config.php');

if(isset($_POST['membershipid'])){
    
    $membershipid = $validation->input_validate($_POST['membershipid']);
    $memberDetailsQuery = $db->view('*',"mlm_registrations","regid"," and membership_id='{$membershipid}'");

    $memberDetails = $memberDetailsQuery['result'][0];
    if($memberDetailsQuery['num_rows'] >= 1){
        if($memberDetails['status'] == "active"){
            echo $memberDetails['status'];
        }else{
            echo json_encode(array('username'=>$memberDetails['username'],'mobile'=>$memberDetails['mobile']));
        }
       
    }else{
        echo $memberDetailsQuery['num_rows'];
    }
}

?>