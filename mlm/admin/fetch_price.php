<?php 
include_once('inc_config.php');

if(isset($_POST['planid'])){
    $planid = $validation->input_validate($_POST['planid']);
    $planDetails = $db->view('amount','mlm_plans','planid'," and planid={$planid} and status='active'");
    echo $planDetails['result'][0]['amount'];
}
?>