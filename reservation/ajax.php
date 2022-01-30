<?php
	include('../session.php');
	include('../conn.php');
	
	$strTodo = $_POST['strTodo'];
	switch($strTodo)
	{
		case "loadSeat" : 
			$date_now = date('Y-m-d');
			$add_str = ($_SESSION['user_type'] != 'A') ? "AND USER_FROM = '".$_SESSION['username']."'" : '';
			$sql = "SELECT 
									NUM_SEATS,
									(SELECT SUBSTRING((SELECT ','+ A.SEAT_LIST  AS [text()] 
										FROM 
											(SELECT * FROM 
													(
														SELECT 
														SEAT_LIST, 
														MRTS_ID,
														MRD_ID,
														STATUS,
														ROW_NUMBER() OVER (PARTITION BY SEAT_LIST,MRTS_ID ORDER BY MAINTAIN_DATE DESC) AS COL_TOP
														FROM MR_TRANSACTION_HISTORY WHERE MRTS_ID = '".$_POST['selTimeSlot']."' AND MRD_ID = '".$_POST['strMrdId']."'
													) AS SEAT_TAKEN
													WHERE COL_TOP = 1 AND STATUS = 'R'
											)A  
									FOR XML PATH('')), 2 , 9999)) AS TAKEN_SEATS,
									(SELECT SUBSTRING((SELECT ','+ A.SEAT_LIST  AS [text()] 
										FROM 
											(SELECT * FROM 
													(
														SELECT 
														SEAT_LIST, 
														MRTS_ID,
														MRD_ID,
														STATUS,
														ROW_NUMBER() OVER (PARTITION BY SEAT_LIST,MRTS_ID ORDER BY MAINTAIN_DATE DESC) AS COL_TOP
														FROM MR_TRANSACTION_HISTORY WHERE MRTS_ID = '".$_POST['selTimeSlot']."' AND MRD_ID = '".$_POST['strMrdId']."'".$add_str."
													) AS SEAT_TAKEN
													WHERE COL_TOP = 1 AND STATUS = 'R'
											)A  
									FOR XML PATH('')), 2 , 9999)) AS USER_RSRV_SEATS
								FROM MR_TIME_SLOTS A 
								WHERE MRTS_ID = '".$_POST['selTimeSlot']."'";
								
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
			
			case "saveReservation" :
				$arrSeat = array();
				$arrSeat = explode(',', $_POST['lstSelected']);
				
				for($i=0; $i<count($arrSeat); $i++)
				{
					$sql = "INSERT INTO MR_TRANSACTION_HISTORY 
										(
											MRD_ID,
											MRTS_ID,
											CUSTOMER_NAME,
											SEAT_LIST,
											STATUS,
											USER_FROM
										) 
									VALUES 
										(
											'".$_POST['strMrdId']."',
											'".$_POST['selTimeSlot']."',
											'".$_POST['txtCustomerName']."',
											'".$arrSeat[$i]."',
											'".$_POST['strStatus']."',
											'".$_SESSION['username']."'
										)";	
					
					$qry = sqlsrv_query($conn, $sql);
					if(!$qry)         
					{    
						echo "Error,Error in statement execution.\n"; 
						break;    
					}
				}
				
				echo "Successful,Reservation saved successfully!";
			break;
			
			case "saveCancellation" :
				$arrSeat = array();
				$arrSeat = explode(',', $_POST['lstSelected']);
				
				for($i=0; $i<count($arrSeat); $i++)
				{
					$sql = "INSERT INTO MR_TRANSACTION_HISTORY 
										(
											MRD_ID,
											MRTS_ID,
											CUSTOMER_NAME,
											SEAT_LIST,
											STATUS,
											USER_FROM
										) 
									SELECT TOP 1 
										'".$_POST['strMrdId']."' AS MRD_ID,
										'".$_POST['selTimeSlot']."' AS MRTS_ID,
									 	A.CUSTOMER_NAME as CUSTOMER_NAME,
										'".$arrSeat[$i]."' AS SEAT_LIST,
										'".$_POST['strStatus']."' AS STATUS,
										'".$_SESSION['username']."' AS USER_FROM
									FROM MR_TRANSACTION_HISTORY A
									WHERE
										A.MRD_ID = '".$_POST['strMrdId']."' AND
										A.MRTS_ID = '".$_POST['selTimeSlot']."' AND
										A.SEAT_LIST = '".$arrSeat[$i]."'
									ORDER BY MAINTAIN_DATE";
									
					$qry = sqlsrv_query($conn, $sql);
					if(!$qry)         
					{    
						echo "Error,Error in statement execution.\n"; 
						die( print_r( sqlsrv_errors(), true));    
						break;    
					}
				}
				
				echo "Successful,Cancellation saved successfully!";
			break;
	}
?>