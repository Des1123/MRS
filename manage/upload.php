<?php 
		include('../session.php');
    include_once('../conn.php');

		if($_POST['strTodo'] == 'uploadFile')
		{
			$curr_dir = getcwd().'\posters\\';
			
			if (!file_exists($curr_dir))
			{
        mkdir($curr_dir, 0777, true);
      }
			
			$curr_dir = $curr_dir.''.$_POST['hidMrdId'].'\\';
			if (!file_exists($curr_dir))
			{
        mkdir($curr_dir, 0777, true);
      }
			
			$files = glob($curr_dir.'\\*'); 
			foreach($files as $file)
			{ 
				if(is_file($file)) 
				{
					unlink($file);
				}
			}
			
			$file_name = $_FILES['txtFile']['name'];
			$file_size =$_FILES['txtFile']['size'];
			$file_tmp =$_FILES['txtFile']['tmp_name'];
			$file_type=$_FILES['txtFile']['type'];   
			$value = explode(".", $file_name);
			$file_ext = strtolower(array_pop($value));
			$expensions= array("jpeg","jpg","png","gif");   
			
			if(in_array($file_ext,$expensions)== false)
			{
				 $errors="Extension not allowed, please choose a JPG/JPEG, PNG or GIF file.";
			}
			
			if($file_size > 2097152)
			{
				 $errors[]='File size must be 3 MB below';
			}    
			if(empty($errors)==true)
			{
				if(move_uploaded_file($file_tmp,$curr_dir.$file_name) && is_writable($curr_dir))
				{
					$sql = "DELETE FROM MR_MOVIE_POSTER WHERE MRD_ID = '".$_POST['hidMrdId']."'";
					$qry = sqlsrv_query($conn, $sql);
					
					$sql = "INSERT INTO MR_MOVIE_POSTER
								(
									MRD_ID,
									FILE_NAME,
									USER_FROM
								) 
							VALUES 
								(
									'".$_POST['hidMrdId']."',
									'".$file_name."',
									'".$_SESSION['username']."'
								)";	
			
					$qry = sqlsrv_query($conn, $sql);
					if(!$qry)         
					{    
						echo "Error,Error in statement execution.\n"; 
						die( print_r( sqlsrv_errors(), true));        
					}
					sqlsrv_close($conn);
				
				echo "Successful,File uploaded successfully!,manage/posters/".$_POST['hidMrdId']."/".$file_name;
			}
			else
			{
				 echo "Error,Error in File uploading!";     
			}
		}
	}
?>