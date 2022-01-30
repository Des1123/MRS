<?php
	include('../conn.php');
	
	$strTodo = $_POST['strTodo'];
	switch($strTodo)
	{
		case "loadMovie" : 
			$date_now = date('Y-m-d');
			$sql = "SELECT 
								A.*, 
								FORMAT(START_DATE, 'MM/dd/yyyy') AS START_DATE_FORMAT, 
								FORMAT(MAINTAIN_DATE, 'MM/dd/yyyy hh:mm tt') AS MAINTAIN_DATE_FORMAT,
								(SELECT CONCAT(MRD_ID, '/', FILE_NAME) FROM MR_MOVIE_POSTER WHERE MRD_ID = A.MRD_ID) AS POSTER_LINK,
								(CASE 
									WHEN START_DATE = CONVERT(DATE,'".$date_now."') THEN 'N'
									WHEN START_DATE > CONVERT(DATE,'".$date_now."') THEN 'C'
									ELSE 'P'
								 END
								) AS CATEGORY
							FROM MR_DETAILS A 
							WHERE START_DATE >= CONVERT(DATE,'".$date_now."')
							ORDER BY CATEGORY, START_DATE";
			
			$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
			
			echo '[';
			if ($qry)    
			{    		
				$ctr = 0;
				$num_rows = sqlsrv_num_rows($qry);
				
				while($row = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC)) 
				{
					$ctr++;
					echo "{";
						echo '"MRD_ID":"'.$row['MRD_ID'].'",';
						echo '"TITLE":"'.$row['TITLE'].'",';
						echo '"DESCT":"'.rawurlencode($row['DESCT']).'",';
						echo '"START_DATE_FORMAT":"'.$row['START_DATE_FORMAT'].'",';
						echo '"MAINTAIN_DATE_FORMAT":"'.$row['MAINTAIN_DATE_FORMAT'].'",';
						echo '"POSTER_LINK":"'.urlencode($row['POSTER_LINK']).'",';
						echo '"CATEGORY":"'.$row['CATEGORY'].'",';
						echo '"TIME_SLOTS":[';
							$sql_ts = "SELECT 
														A.*, 
														FORMAT(START_TIME, 'hh\:mm\:ss') AS START_TIME_FORMAT, 
														FORMAT(END_TIME, 'hh\:mm\:ss') AS END_TIME_FORMAT,
														FORMAT(MAINTAIN_DATE, 'MM/dd/yyyy hh:mm tt') AS MAINTAIN_DATE_FORMAT,
														(SELECT COUNT(SEAT_LIST) FROM 
																(
																	SELECT 
																	SEAT_LIST, 
																	MRTS_ID,
																	MRD_ID,
																	STATUS,
																	ROW_NUMBER() OVER (PARTITION BY SEAT_LIST,MRTS_ID ORDER BY MAINTAIN_DATE DESC) AS COL_TOP
																	FROM MR_TRANSACTION_HISTORY WHERE MRTS_ID = A.MRTS_ID AND MRD_ID = '".$row['MRD_ID']."'
																) AS SEAT_TAKEN
																WHERE COL_TOP = 1 AND STATUS = 'R'
														) AS RSRV_CNT 
													FROM MR_TIME_SLOTS A 
													WHERE MRD_ID = '".$row['MRD_ID']."'
													ORDER BY START_TIME";
							$qry_ts = sqlsrv_query($conn, $sql_ts, array(), array("Scrollable" => 'keyset'));
							$ctr2=0;
							$num_rows_2 = sqlsrv_num_rows($qry_ts);
							while($row2 = sqlsrv_fetch_array($qry_ts, SQLSRV_FETCH_ASSOC))
							{
								$ctr2++;
								$avail_seats = intval($row2['NUM_SEATS']) - intval($row2['RSRV_CNT']);
								echo "{";
									echo '"MRTS_ID":"'.$row2['MRTS_ID'].'",';
									echo '"START_TIME_FORMAT":"'.$row2['START_TIME_FORMAT'].'",';
									echo '"END_TIME_FORMAT":"'.$row2['END_TIME_FORMAT'].'",';
									echo '"AVAIL_SEAT":"'.$avail_seats.'"';
								echo "}";
								
								if($ctr2 != $num_rows_2)
								{
									echo ",";
								}
							}
						echo ']'; //time_slots
					echo "}";
					
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
			//echo "]}";
			echo "]";
			
			sqlsrv_close($conn);
			break;
	}
?>