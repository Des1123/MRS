<?php 
    include_once('conn.php');

    if($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST['btnLogin']))
    {
			$sql = "SELECT COUNT(*)
							FROM MR_USER_INFO A 
							WHERE
								USERNAME = '".$_POST['txtUsername']."'";
			
			$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
			$num_rows = sqlsrv_num_rows($qry);
			
			if($num_rows != 0)
			{
				$sql = "SELECT *
							FROM MR_USER_INFO A 
							WHERE
								USERNAME = '".$_POST['txtUsername']."' AND
								PASSWORD = '".$_POST['txtPassword']."'";
				
				$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
				$num_rows_2 = sqlsrv_num_rows($qry);
				
				if($num_rows_2 != 0)
				{
					$row = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC);
					$_SESSION['username'] = $row['USERNAME'];
					$_SESSION['user_type'] = $row['USER_TYPE'];	
				}
				else
				{
					echo "<script language='javascript'>alert('Invalid Credentials!');</script>";
				}
			}
			else
			{
				echo "<script language='javascript'>alert('Invalid Credentials!');</script>";
			}
		}
?>