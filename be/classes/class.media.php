<?php
class media
{
	public function image_resize($fix_width, $fix_height, $imgDest, $imgExtension)
	{
		list($width,$height) = getimagesize($imgDest);
		$new_width = $fix_width;
		$new_height = $fix_height;
		$create_image = imagecreatetruecolor($new_width,$new_height);
		if($imgExtension == "png")
		{
			$source = imagecreatefrompng($imgDest);
		}
		elseif($imgExtension == "jpg" || $imgExtension == "jpeg")
		{
			$source = imagecreatefromjpeg($imgDest);
		}
		elseif($imgExtension == "gif")
		{
			$source = imagecreatefromgif($imgDest);
		}
		imagecopyresampled($create_image,$source,0,0,0,0,$new_width,$new_height,$width,$height);
		if($imgExtension == "png")
		{
			imagepng($create_image,$imgDest,100);
		}
		elseif($imgExtension == "jpg" || $imgExtension == "jpeg")
		{
			imagejpeg($create_image,$imgDest,100);
		}
		elseif($imgExtension == "gif")
		{
			imagegif($create_image,$imgDest,100);
		}
	}
	
	public function filedeletion($table, $idname, $id, $fieldname, $loc, $loc2='')
	{
		global $connect;
		$db = new db;
		$validation = new validation;
		
		$delQueryResult = $db->view($fieldname, $table, $idname, "and $idname = '$id'");
		$delresult = $delQueryResult['result'][0];
		$fileName = $delresult[$fieldname];
		$fileloc = $loc."".$fileName;
		$fileloc2 = $loc2."".$fileName;
		if($fileName != "")
		{
			unlink($fileloc);
			unlink($fileloc2);
		}
		
		$updateQuery = $db->update($table, array($fieldname=>''), array($idname=>$id));
		if($updateQuery)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function multiple_filedeletion($table, $idname, $id, $fieldname, $loc, $loc2='', $file='')
	{
		global $connect;
		$db = new db;
		$validation = new validation;
		
		$delQueryResult = $db->view($fieldname, $table, $idname, "and $idname = '$id'");
		$delresult = $delQueryResult['result'][0];
		$fileName = $delresult[$fieldname];
		$fileName_array = explode(" | ", $fileName);
		
		if($file != "")
		{
			if(($file_arr = array_search($file, $fileName_array)) !== false)
			{
				unset($fileName_array[$file_arr]);
			}
			$fileloc = $loc."".$file;
			$fileloc2 = $loc2."".$file;
			if($file != "")
			{
				unlink($fileloc);
				unlink($fileloc2);
			}
			$fileName = implode(" | ", $fileName_array);
		}
		else
		{
			foreach($fileName_array as $fileName)
			{
				$fileloc = $loc."".$fileName;
				$fileloc2 = $loc2."".$fileName;
				if($fileName != "")
				{
					unlink($fileloc);
					unlink($fileloc2);
				}
			}
			$fileName = "";
		}
		
		$updateQuery = $db->update($table, array($fieldname=>$fileName), array($idname=>$id));
		if($updateQuery)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

$media = new media;
?>