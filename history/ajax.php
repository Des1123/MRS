<?php
	include('../conn.php');
	
	$strTodo = $_POST['strTodo'];
	switch($strTodo)
	{
		case "displayHistory" : 
			$date_now = date('Y-m-d');
			$strMrdId = (!isset($_POST['selMovie'])) ? "0" : $_POST['selMovie'];
			$strMrtsId = (!isset($_POST['selTimeSlot'])) ? "0" : $_POST['selTimeSlot'];
			
			$sql = "SELECT USER_FROM,FORMAT(MAINTAIN_DATE, 'MM/dd/yyyy hh:mm:ss') AS MAINTAIN_DATE, CUSTOMER_NAME, STATUS, SUBSTRING((SELECT ','+ A.SEAT_LIST  AS [text()] 
							FROM (
								SELECT * FROM 
								(
									SELECT 
									SEAT_LIST, STATUS, CUSTOMER_NAME, MRTS_ID, MRD_ID,
									ROW_NUMBER() OVER (PARTITION BY CUSTOMER_NAME,MRTS_ID,SEAT_LIST,STATUS,USER_FROM ORDER BY MAINTAIN_DATE DESC) AS COL_TOP
									FROM MR_TRANSACTION_HISTORY B 
									WHERE B.MRTS_ID = '".$strMrtsId."' AND B.MRD_ID = '".$strMrdId."' AND B.CUSTOMER_NAME = A.CUSTOMER_NAME AND B.STATUS = A.STATUS AND B.MAINTAIN_DATE = A.MAINTAIN_DATE
								) AS SEAT_TAKEN
								WHERE COL_TOP = 1 
							)A FOR XML PATH('')), 2 , 9999) AS SEAT_LIST
							FROM MR_TRANSACTION_HISTORY A WHERE MRTS_ID = '".$strMrtsId."' AND MRD_ID = '".$strMrdId."' 
							GROUP BY CUSTOMER_NAME,STATUS, MAINTAIN_DATE, USER_FROM
							ORDER BY MAINTAIN_DATE DESC
						";
			
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
			
			case "getTimeSlot" :
				$time_now = date('H:i:s');
				$sql = "SELECT 
									B.* 
								FROM 
								(
									SELECT 
										MRTS_ID, 
										FORMAT(START_TIME, 'hh\:mm\:ss') AS START_TIME, 
										FORMAT(END_TIME, 'hh\:mm\:ss') AS END_TIME,
										(SELECT CONCAT(B.START_DATE,' ',A.START_TIME) FROM MR_DETAILS B WHERE MRD_ID = A.MRD_ID) AS START_DATE_TIME
									FROM MR_TIME_SLOTS A
									WHERE
									MRD_ID IN (SELECT MRD_ID FROM MR_DETAILS WHERE START_DATE <= CONVERT(DATE, GETDATE()) AND MRD_ID ='".$_POST['strMrdId']."')
								)B
								WHERE CONVERT(DATETIME,B.START_DATE_TIME) <= CONVERT(DATETIME, GETDATE())
								ORDER BY START_TIME";
				
				$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
				
				echo '[';
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
				echo "]";
				
				sqlsrv_close($conn);
			break;
	}
?>