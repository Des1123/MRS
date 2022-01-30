<!DOCTYPE html>

<head>
	<title>Movie Reservation System - Reservation</title>
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.3/datatables.min.css"/>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/zebra_datepicker@latest/dist/css/bootstrap/zebra_datepicker.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> -->
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.3/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/zebra_datepicker@latest/dist/zebra_datepicker.min.js"></script>

<body>
	<?php
		include_once('session.php');
		include('navbar.php');
		include('conn.php');
		
		echo '<input type="hidden" id="hidSelectedMrdId" value="'.$_POST['hidMrdId'].'">';
	?>
  
  <div class="wrapper">
    <div class="card bg-light mb-3">
      <div class="card-body" style="padding-top:unset !important; text-align:center;">
        <?php
					$seat_price = 0;
					$sql = "SELECT 
										A.TITLE,
										A.SEAT_PRICE,
										FORMAT(START_DATE, 'MM/dd/yyyy') AS START_DATE_FORMAT
									FROM MR_DETAILS A
									WHERE 
										MRD_ID = '".$_POST['hidMrdId']."'";	
					
					$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
					
					if ($qry)    
					{    		
						$row = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC);
						echo '<h5 class="card-title">'.$row['TITLE'].'</h5>';
						echo '<span id="spanShowingDate">'.$row['START_DATE_FORMAT'].'</span><br>';
						
						$seat_price = $row['SEAT_PRICE'];
					}      
					else     
					{    
					 echo "Error in statement execution.\n";    
					 die( print_r( sqlsrv_errors(), true));    
					}
				
					/*$sql = "SELECT 
									MRTS_ID, 
									FORMAT(START_TIME, 'hh\:mm\:ss') AS START_TIME_FORMAT, 
									FORMAT(END_TIME, 'hh\:mm\:ss') AS END_TIME_FORMAT 
								FROM MR_TIME_SLOTS A 
								WHERE
									MRD_ID = '".$_POST['hidMrdId']."'
									ORDER BY START_TIME";	*/
									
					$sql = "SELECT 
						MRTS_ID, 
						FORMAT(START_TIME, 'hh\:mm\:ss') AS START_TIME_FORMAT, 
						FORMAT(END_TIME, 'hh\:mm\:ss') AS END_TIME_FORMAT,
						(
							CASE
								WHEN CONVERT(DATETIME,B.START_DATE_TIME) <= CONVERT(DATETIME, GETDATE()) THEN 'D'
								ELSE 'A'
							END
						) AS DSBL
					FROM 
					(
						SELECT 
							MRTS_ID, START_TIME, END_TIME,
							(SELECT CONCAT(B.START_DATE,' ',A.START_TIME) FROM MR_DETAILS B WHERE MRD_ID = A.MRD_ID) AS START_DATE_TIME
						FROM MR_TIME_SLOTS A
						WHERE
						MRD_ID IN (SELECT MRD_ID FROM MR_DETAILS WHERE MRD_ID = '".$_POST['hidMrdId']."')
					)B 
					ORDER BY START_TIME
					";
					
					$qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
					
					echo '<select id="selTimeSlot">';
					
					if($qry)    
					{    		
						while( $row = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC)) 
						{
							echo '<option value="'.$row['MRTS_ID'].'" dsbl="'.$row['DSBL'].'">'.$row['START_TIME_FORMAT'].' - '.$row['END_TIME_FORMAT'].'</option>';
						}
					}      
					else     
					{    
					 echo "Error in statement execution.\n";    
					 die( print_r( sqlsrv_errors(), true));    
					}
					
					echo '</select>';
					
					sqlsrv_close($conn);
				?>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col d-flex">
        	<div id="divSeats" class="container"></div>
        </div>
        <div class="border-left col-3">
  				<h5>Transaction</h5>
          <button type="button" id="btnShowRsrv" class="btn btn-sm btn-info"><span class="fas fa-plus-circle"></span>&nbsp;Reservation</button>
          <button type="button" id="btnCancelRsrv" class="btn btn-sm btn-warning"><span class="fas fa-window-close"></span>&nbsp;Cancel Reservation</button>
          <div>
          	<br>
          	<form id="frmTrans" style="display:none;">
            	<table class="table table-sm">
              	<tr>
                	<td class="col-sm-3"><strong>Customer Name</strong></td>
                  <td><strong>:</strong></td>
                  <td class="col-sm-9"><input type="text" id="txtCustomerName" class="form-control"></td>
                </tr>
                <tr>
                	<td><strong>Seat Price</strong></td>
                  <td><strong>:</strong></td>
                  <td>&#8369;&nbsp;<strong><span id="spanSeatPrice"><?php echo $seat_price; ?></span></strong></td>
                </tr>
                <tr>
                	<td><strong>Selected Seats</strong></td>
                  <td><strong>:</strong></td>
                  <td><ul id="ulSelectedSeats"></ul></td>
                </tr>
                <tr>
                	<td><strong>Total Price</strong></td>
                  <td><strong>:</strong></td>
                  <td>&#8369;&nbsp;<strong><span id="spanTotalPrice"></span></strong></td>
                </tr>
                <tr>
                	<td colspan="100%" align="right">
                  	<input type="button" id="btnSaveTrans" class="btn btn-sm btn-primary" value="Proceed">
                    <input type="button" id="btnResetCurrent" class="btn btn-sm btn-Secondary" value="Clear">
                  </td>
                </tr>
              </table>
            </form>
            
            <form id="frmCancel" style="display:none;">
            	<strong>Seats to be Canceled:</strong><br><ul id="ulCanceledSeats"></ul>
              <input type="checkbox" id="chkCancelAll" name="chkCancelAll">&nbsp;<strong>Cancel All</strong><br><br>
              <input type="button" id="btnSaveCanceled" class="btn btn-sm btn-primary" value="Proceed">
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
	
	<script src="reservation/control.js"></script>
</body>

</html>