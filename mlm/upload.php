<?php
include_once("inc_config.php");
if(is_array($_FILES))
{
	$handle = new Upload($_FILES['userImage']);
    if($handle->uploaded)
	{
		$handle->file_force_extension = true;
		$handle->file_max_size = 5242880;
		$handle->allowed = array('image/*');
		if($configRow['large_width'] != "0" and $configRow['large_height'] != "0")
		{
			$handle->image_resize = true;
			$handle->image_x = $validation->db_field_validate($configRow['large_width']);
			$handle->image_y = $validation->db_field_validate($configRow['large_height']);
			$handle->image_no_enlarging = ($configRow['large_ratio'] === "false") ? false : true;
			$handle->image_ratio = true;
		}
		
		$handle->process(IMG_LOC);
		if($handle->processed)
		{
			$imgName = $handle->file_dst_name;
			?>
				<a href="<?php echo BASE_URL; ?>uploads/<?php echo $imgName; ?>" target="_blank"><img src="<?php echo BASE_URL; ?>uploads/<?php echo $imgName; ?>" /></a>
			<?php
		}
		else
		{
			?>
			<script>
			$(document).ready(function(){
				$('#drop-area').find(".image_upper_text").hide();
				$('#drop-area').find(".image_upper_text").fadeIn().html("<i class='fa fa-times' aria-hidden='true' style='color:red;'></i> Image Size must be in 10 MB(Megabyte)!!!");
			});
			</script>
			<?php
			//echo "<script>alert('{$handle->error}')</script>";
			exit();
		}
		
		$handle-> clean();
	}
	else
	{
		//echo "<script>alert('{$handle->error}')</script>";
		exit();
    }
}
?>