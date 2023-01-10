<?php include_once('inc_config.php');
$cronTitle = $db->insert('mlm_test', array("test"=> 'Value', 'createdate'=> $createdate, 'createtime'=> $createtime));
echo 'success';
?>