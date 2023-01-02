<?php
include_once("inc_config.php");

@$categoryid = $_POST['categoryid'];
@$subcategoryid = $_POST['subcategoryid'];
@$mode = $_POST['mode'];

if(isset($categoryid) and $categoryid != "")
{
	$subcategoryQueryResult = $db->view('subcategoryid,title', 'rb_subcategories', 'subcategoryid', "and categoryid='$categoryid' and status='active'", 'title asc');
	if($subcategoryQueryResult['num_rows'] >= 1)
	{
	?>
		<select NAME="subcategoryid" CLASS="form-control" ID="subcategoryid">
			<option VALUE="">--select--</option>
			<?php
			foreach($subcategoryQueryResult['result'] as $subcategoryRow)
			{
			?>
				<option VALUE="<?php echo $validation->db_field_validate($subcategoryRow['subcategoryid']); ?>" <?php if($mode == 'edit') { if($subcategoryRow['subcategoryid'] == $subcategoryid) echo "selected"; } ?>><?php echo $validation->db_field_validate($subcategoryRow['title']); ?></option>
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