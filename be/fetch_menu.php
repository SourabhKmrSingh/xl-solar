<?php
include_once("inc_config.php");

@$main_menu = $_POST['main_menu'];
@$sub_menu = $_POST['sub_menu'];
@$mode = $_POST['mode'];

if(isset($main_menu) and $main_menu != "")
{
	$submenuQueryResult = $db->view('pageid,title', 'rb_pages', 'pageid', "and main_menu='$main_menu' and sub_menu='' and order_custom != '0' and status='active'", 'order_custom asc');
	if($submenuQueryResult['num_rows'] >= 1)
	{
	?>
		<select NAME="sub_menu" CLASS="form-control" ID="sub_menu">
			<option VALUE="">--select--</option>
			<?php
			foreach($submenuQueryResult['result'] as $submenuRow)
			{
			?>
				<option VALUE="<?php echo $validation->db_field_validate($submenuRow['pageid']); ?>" <?php if($mode == 'edit') { if($submenuRow['pageid'] == $sub_menu) echo "selected"; } ?>><?php echo $validation->db_field_validate($submenuRow['title']); ?></option>
			<?php
			}
			?>
		</select>
	<?php
	}
	else
	{
		echo '<p class="text">No Data Available!</p>';
	}
}
else
{
	echo '<p class="text">No Data Available!</p>';
}
?>