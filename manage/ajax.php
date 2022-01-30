<?php
	include('../session.php');
	include('../conn.php');
	
	$strTodo = $_POST['strTodo'];
	switch($strTodo)
	{
		case "displayMovies" : 
			$date_now = date('Y-m-d');
			$sql = "SELECT 
								A.*, 
								FORMAT(START_DATE, 'MM/dd/yyyy') AS START_DATE_FORMAT, 
								FORMAT(MAINTAIN_DATE, 'MM/dd/yyyy hh:mm tt') AS MAINTAIN_DATE_FORMAT,
								(SELECT CONCAT(MRD_ID, '/', FILE_NAME) FROM MR_MOVIE_POSTER WHERE MRD_ID = A.MRD_ID) AS POSTER_LINK,
								(
									CASE
										WHEN START_DATE <= CONVERT(DATE, GETDATE()) THEN 'D'
										ELSE 'A'
									END
								) AS DSBL
							FROM MR_DETAILS A 
							ORDER BY MRD_ID";
			
			$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
			
			echo '{"data" : [';
			if ($qry)    
			{    		
				$ctr = 0;
				$num_rows = sqlsrv_num_rows($qry);
				
				while( $row = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC)) 
				{
					$ctr++;
					echo json_encode($row);
					if($ctr != $num_rows)
					{
						echo ",";
					}
				}
			}      
			else     
			{    
			 echo "Error in statement execution.\n";    
			 die( print_r( sqlsrv_errors(), true));    
			}
			echo "]}";
			
			sqlsrv_close($conn);
			break;
		
		case "saveMovie" : 
			$add_str = ($_POST['action'] == 'UpdateData' ? " AND MRD_ID != '".$_POST['strMrdId']."'" : "");
			$sql = "SELECT 
								TITLE, START_DATE
							FROM MR_DETAILS A 
							WHERE 
								UPPER(TRIM(TITLE)) = '".strtoupper(trim(urldecode($_POST['txtTitle'])))."' AND
								START_DATE >= CONVERT(DATE,'".$_POST['txtStartDate']."')".$add_str;
								 
			$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
			$num_rows = sqlsrv_num_rows($qry);
			
			if($num_rows == 0)
			{
				if($_POST['action'] == 'AddData')
				{
					$desct = str_replace("'","''", urldecode($_POST['txtDesct']));
					$sql = "INSERT INTO MR_DETAILS 
										(
											TITLE, 
											DESCT, 
											START_DATE, 
											SEAT_PRICE, 
											USER_FROM
										) 
									VALUES 
										(
											'".urldecode($_POST['txtTitle'])."',
											'".$desct."',
											CONVERT(DATE,'".$_POST['txtStartDate']."'),
											'".$_POST['txtSeatPrice']."',
											'".$_SESSION['username']."'
										)";	
					
					$qry = sqlsrv_query($conn, $sql);
					if(!$qry)         
					{    
						echo "Error,Error in statement execution.\n";     
					}
					else
					{
						echo "Successful,Record added successfully!";
					}
				}
				else if($_POST['action'] == 'UpdateData')
				{
					$desct = str_replace("'","''", urldecode($_POST['txtDesct']));
					$sql = "UPDATE MR_DETAILS 
									SET
										TITLE = '".urldecode($_POST['txtTitle'])."',
										DESCT = '".$desct."',
										START_DATE = CONVERT(DATE,'".$_POST['txtStartDate']."'), 
										SEAT_PRICE = '".$_POST['txtSeatPrice']."',
										USER_FROM = '".$_SESSION['username']."',
										MAINTAIN_DATE = CURRENT_TIMESTAMP
									WHERE 
										MRD_ID = '".$_POST['strMrdId']."'";	
					
					$qry = sqlsrv_query($conn, $sql);
					if(!$qry)         
					{    
						echo "Error,Error in statement execution.\n";     
					}
					else
					{
						echo "Successful,Record Updated successfully!";
					}
				}
			}
			else
			{
				echo "Error,Duplicate Record!";
			}
			
			sqlsrv_close($conn);
			break;
			
			case "deleteMovie" : 
				$curr_dir = getcwd().'\posters\\';
				$arrMrdId = explode(',',$_POST['lstMrdId']);
				
				for($i=0; $i<=count($arrMrdId)-1; $i++)
				{
					$curr_dir = $curr_dir.$arrMrdId[$i];
					
					$files = glob($curr_dir.'\\*'); 
					foreach($files as $file)
					{ 
						if(is_file($file)) 
						{
							unlink($file);
						}
					}
					rmdir($curr_dir);
				} //file and folder deletion
				
				$sql = "DELETE FROM MR_MOVIE_POSTER WHERE MRD_ID IN (".$_POST['lstMrdId'].")";
				$qry = sqlsrv_query($conn, $sql);
				
				$sql = "DELETE FROM MR_TIME_SLOTS WHERE MRD_ID IN (".$_POST['lstMrdId'].")";
				$qry = sqlsrv_query($conn, $sql);
				
				$sql = "DELETE FROM MR_DETAILS WHERE MRD_ID IN (".$_POST['lstMrdId'].")";
				$qry = sqlsrv_query($conn, $sql);
				
				if(!$qry)         
				{    
					echo "Error,Error in statement execution.\n"; 
					die( print_r( sqlsrv_errors(), true));        
				}
				else
				{
					echo "Successful,Record deleted successfully!";
				}
				sqlsrv_close($conn);
			break;
			
			case "displayTimeSlots" : 
				if(isset($_POST['strMrdId']))
				{
					$strMrdId = $_POST['strMrdId'];
				}
				else
				{
					$strMrdId = 0;
				}
				
				$date_now = date('Y-m-d');
				$sql = "SELECT 
									B.*,
									FORMAT(START_TIME, 'hh\:mm\:ss') AS START_TIME_FORMAT, 
									FORMAT(END_TIME, 'hh\:mm\:ss') AS END_TIME_FORMAT,
									FORMAT(B.MAINTAIN_DATE, 'MM/dd/yyyy hh:mm tt') AS MAINTAIN_DATE_FORMAT,
									(CASE
										WHEN CONVERT(DATETIME,B.START_DATE_TIME) <= CONVERT(DATETIME, GETDATE()) THEN 'D'
										ELSE 'A'
										END
									) AS DSBL_TIME
								FROM 
								(
									SELECT 
										A.*,
										(SELECT CONCAT(B.START_DATE,' ',A.START_TIME) FROM MR_DETAILS B WHERE MRD_ID = A.MRD_ID) AS START_DATE_TIME,
										(SELECT COUNT(SEAT_LIST) FROM 
											(
												SELECT 
													SEAT_LIST,
													STATUS,
													ROW_NUMBER() OVER (PARTITION BY CUSTOMER_NAME,MRTS_ID,SEAT_LIST,STATUS,USER_FROM ORDER BY MAINTAIN_DATE DESC) AS COL_TOP
												FROM MR_TRANSACTION_HISTORY WHERE MRTS_ID = A.MRTS_ID AND MRD_ID = '".$strMrdId."'
											) AS SEAT_TAKEN
											WHERE COL_TOP = 1 AND STATUS = 'R'
									) AS RSRV_CNT 
									FROM MR_TIME_SLOTS A
									WHERE
									MRD_ID ='".$strMrdId."'
								)B
								ORDER BY START_TIME";
				
				$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
				
				echo '{"data" : [';
				if ($qry)    
				{    		
					$ctr = 0;
					$num_rows = sqlsrv_num_rows($qry);
					
					while( $row = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC)) 
					{
						$ctr++;
						echo json_encode($row);
						if($ctr != $num_rows)
						{
							echo ",";
						}
					}
				}      
				else     
				{    
				 echo "Error in statement execution.\n";    
				 die( print_r( sqlsrv_errors(), true));    
				}
				echo "]}";
				
			sqlsrv_close($conn);
			break;
			
			case "saveTimeSlots" :
			$add_str = ($_POST['action'] == 'UpdateData' ? " AND MRTS_ID != '".$_POST['strMrtsId']."'" : ""); 
			$sql = "SELECT START_TIME, END_TIME
								FROM MR_TIME_SLOTS A 
								WHERE 
									MRD_ID = '".$_POST['strMrdId']."' AND
									START_TIME = CONVERT(TIME,'".$_POST['txtStartTime']."') AND
									END_TIME = CONVERT(TIME,'".$_POST['txtEndTime']."')".$add_str;
			
			$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
			$num_rows = sqlsrv_num_rows($qry);
			
			if($num_rows == 0)
			{
				if($_POST['action'] == 'AddData')
				{
					$sql = "INSERT INTO MR_TIME_SLOTS 
										(
											MRD_ID,
											START_TIME,
											END_TIME,
											NUM_SEATS, 
											USER_FROM
										) 
									VALUES 
										(
											'".$_POST['strMrdId']."',
											CONVERT(TIME,'".$_POST['txtStartTime']."'),
											CONVERT(TIME,'".$_POST['txtEndTime']."'),
											'".$_POST['txtNumSeat']."',
											'".$_SESSION['username']."'
										)";	
					
					$qry = sqlsrv_query($conn, $sql);
					if(!$qry)         
					{    
						echo "Error,Error in statement execution.\n"; 
						die( print_r( sqlsrv_errors(), true));        
					}
					else
					{
						echo "Successful,Record added successfully!";
					}
				}
				else if($_POST['action'] == 'UpdateData')
				{
					$sql = "UPDATE MR_TIME_SLOTS 
									SET  
										START_TIME = CONVERT(TIME,'".$_POST['txtStartTime']."'),
										END_TIME = CONVERT(TIME,'".$_POST['txtEndTime']."'),
										NUM_SEATS = '".$_POST['txtNumSeat']."',
										USER_FROM = '".$_SESSION['username']."',
										MAINTAIN_DATE = CURRENT_TIMESTAMP
									WHERE 
										MRTS_ID = '".$_POST['strMrtsId']."'";	
					
					$qry = sqlsrv_query($conn, $sql);
					if(!$qry)         
					{    
						echo "Error,Error in statement execution.\n"; 
						die( print_r( sqlsrv_errors(), true));        
					}
					else
					{
						echo "Successful,Record Updated successfully!";
					}
				}
			}
			else
			{
				echo "Error,Duplicate Record!";
			}
			
			sqlsrv_close($conn);
			break;
						
			case "deleteTimeSlot" : 
				$sql = "DELETE FROM MR_TIME_SLOTS WHERE MRD_ID IN (".$_POST['lstMrtsId'].")";
				$qry = sqlsrv_query($conn, $sql);
				if(!$qry)         
				{    
					echo "Error,Error in statement execution.\n"; 
					die( print_r( sqlsrv_errors(), true));        
				}
				else
				{
					echo "Successful,Record deleted successfully!";
				}
				sqlsrv_close($conn);
			break;
	}
?>