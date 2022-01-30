<!DOCTYPE html>

<head>
	<title>Movie Reservation System - Homepage</title>
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
	?>
  
  <div class="wrapper">
  	<div class="jumbotron jumbotron-fluid">
      <div class="container">
        <h1 class="display-4">Movie Reservation System</h1>
        <p class="lead">This is a demo project only.</p>
      </div>
    </div>
    
    <div class="container">
      <div class="row">
        <div class="col-lg-10 offset-lg-1">	
        	<div id="divNowShowing" class="list-group">
          	<div class="list-group-item list-group-item-action flex-column list-group-item-secondary align-items-start">
            	<div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">Now Showing</h5>
              </div>
            </div> <!-- list-group-item -->
					</div> <!-- list-group now showing -->
          <br>
          <div id="divComingSoon" class="list-group">
          	<div class="list-group-item list-group-item-action flex-column list-group-item-secondary align-items-start">
            	<div class="d-flex w-100 justify-content-between">
              	<h5 class="mb-1">Coming Soon</h5>
              </div>
            </div> <!-- list-group-item -->
          </div> <!-- list-group -->
        </div> <!-- col-lg-10 offset-lg-1 -->
      </div>
      
      <form id="frmReservation" method="post" action="reservation.php">
 				<input type="hidden" id="hidMrdId" name="hidMrdId" value="">
      </form> 
    </div>
  </div>
	
	<script src="homepage/control.js"></script>
</body>

</html>