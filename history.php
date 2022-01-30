<!DOCTYPE html>

<head>
	<title>Movie Reservation System - History</title>
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
	?>
  <br>
  <div class="container-fluid">
		<div id="divMovies" class="card">
			<div class="card-header">
      	<div class="form-inline my-2 my-lg-0">
          <strong>Movie:</strong>&emsp;
          <?php
            $seat_price = 0;
            $sql = "SELECT 
											C.*,
											FORMAT(C.START_DATE, 'MM/dd/yyyy') AS START_DATE 
										FROM MR_DETAILS C
										WHERE 
											MRD_ID IN 
											(
												SELECT 
													DISTINCT(MRD_ID) 
												FROM 
												(
													SELECT 
														MRD_ID, 
														START_TIME,
														(SELECT CONCAT(B.START_DATE,' ',A.START_TIME) FROM MR_DETAILS B WHERE MRD_ID = A.MRD_ID) AS START_DATE_TIME
													FROM MR_TIME_SLOTS A
													WHERE
													MRD_ID IN (SELECT MRD_ID FROM MR_DETAILS WHERE START_DATE <= CONVERT(DATE, GETDATE()))
												)B
												WHERE CONVERT(DATETIME,B.START_DATE_TIME) <= CONVERT(DATETIME, GETDATE())
											)";	
            
            echo '<select id="selMovie" class="form-control mr-sm-1">
                    <option value="0" selected disabled>Select...</option>';
            
            $qry = sqlsrv_query($conn, $sql, array(), array("Scrollable" => 'keyset'));
            if ($qry)    
            {    		
              while($row = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC))
              {
                echo '<option value="'.$row['MRD_ID'].'">'.$row['TITLE'].' - ('.$row['START_DATE'].')</option>';
              }
            }      
            else     
            {    
             echo "Error in statement execution.\n";    
             die( print_r( sqlsrv_errors(), true));    
            }
            echo ' </select>&emsp;';
						
						sqlsrv_close($conn);
          ?>
  
          <strong>Time Slot:</strong>&emsp;
          <select id="selTimeSlot" class="form-control mr-sm-1">
            <option value="0"  selected disabled>Select...</option>
          </select>&emsp;
          
          <button id="btnExport" type="button" class="btn btn-success ml-auto"><span class="fa fa-download"></span>&nbsp;Export as Excel</button>
        </div>
			</div>
			<div class="card-body">
				<table id="tblHistory" class="table table-sm table-hover" width="100%">
					<thead>
						<td data-orderable="false"><strong>Customer Name</strong></td>
						<td data-orderable="false"><strong>Seat List</strong></td>
						<td data-orderable="false"><strong>Status</strong></td>
						<td><strong>Maintain Date</strong></td>
					</thead>
				</table>
			</div>
		</div>
	</div>
  
	<script src="history/control.js"></script>
</body>

</html>