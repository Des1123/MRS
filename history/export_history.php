<?php
	include('../conn.php');
	$strMrdId = (!isset($_GET['selMovie'])) ? "0" : $_GET['selMovie'];
	$strMrtsId = (!isset($_GET['selTimeSlot'])) ? "0" : $_GET['selTimeSlot'];
	
	$sql = "SELECT 
						USER_FROM,
						FORMAT(MAINTAIN_DATE, 'MM/dd/yyyy hh:mm:ss') AS MAINTAIN_DATE, 
						CUSTOMER_NAME, 
						STATUS,
						(CASE 
							WHEN STATUS = 'C' THEN 'Cancelled'
							WHEN STATUS = 'R' THEN 'Reserved'
							WHEN STATUS = 'U' THEN 'Used'
							ELSE ''
						 END
						)AS STATUS_DESCT,
						SUBSTRING((SELECT ','+ A.SEAT_LIST  AS [text()] 
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
					ORDER BY MAINTAIN_DATE DESC";
	
	$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
	
	$sql2 = "SELECT TITLE, FORMAT(START_DATE, 'MM-dd-yyyy') AS START_DATE FROM MR_DETAILS WHERE MRD_ID = '".$strMrdId."'";
	$qry2 = sqlsrv_query($conn, $sql2, array(), array("Scrollable" => 'keyset'));
	$row2 = sqlsrv_fetch_array($qry2, SQLSRV_FETCH_ASSOC);
	
	$sql3 = "SELECT FORMAT(START_TIME, 'hh\:mm\:ss') AS START_TIME, FORMAT(END_TIME, 'hh\:mm\:ss') AS END_TIME FROM MR_TIME_SLOTS WHERE MRTS_ID = '".$strMrtsId."'";
	$qry3 = sqlsrv_query($conn, $sql3, array(), array("Scrollable" => 'keyset'));
	$row3 = sqlsrv_fetch_array($qry3, SQLSRV_FETCH_ASSOC);
	
	$title = $row2['TITLE'].' '.$row2['START_DATE'];
	
	header("Content-Type: application/xls");    
	header("Content-Disposition: attachment; filename=".$title.".xls");  
	header("Pragma: no-cache"); 
	header("Expires: 0");
	
	echo '<table border="1">';
	echo '<tr><td colspan=5>'.$title.' ('.$row3['START_TIME'].' - '.$row3['END_TIME'].')</td></tr>';
	echo '<tr><th>Customer Name</th><th>Seat List</th><th>Status</th><th>Maintain Date</th><th>Last Modified By</th></tr>';
	while($row = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC))
	{
			echo "<tr><td>".$row['CUSTOMER_NAME']."</td><td>".$row['SEAT_LIST']."</td><td>".$row['STATUS_DESCT']."</td><td>".$row['MAINTAIN_DATE']."</td><td>".$row['USER_FROM']."</td></tr>";
	}
	echo '</table>';
	
	sqlsrv_close($conn);
	exit();
?>