<!DOCTYPE html>

<head>
	<title>Movie Reservation System - Manage</title>
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
	<br>
	<div class="container-fluid">
		<div id="divMovies" class="card">
			<div class="card-header">
				<span id="spanAddMovie" class="fas fa-plus-circle" style="font-size:24px; cursor:pointer;" title="Insert Movies"></span>&emsp;
				<span id="spanDeleteMovie" class="fas fa-trash-alt" style="font-size:24px; cursor:pointer;" title="Delete Movies"></span>&emsp;
				<span id="spanRefreshMovie" class="fas fa-sync" style="font-size:24px; cursor:pointer;" title="Refresh Movies"></span>&emsp;
			</div>
			<div class="card-body">
				<table id="tblMovies" class="table table-sm table-hover" width="100%">
					<thead>
						<td data-orderable="false"></td>
						<td><strong>Title</strong></td>
						<td data-orderable="false"><strong>Description</strong></td>
						<td><strong>Seat Price</strong></td>
						<td><strong>Showing Date</strong></td>
						<td data-orderable="false"><strong>Last Modified by</strong></td>
            <td data-orderable="false"><strong>Maintain Date</strong></td>
            <td data-orderable="false"></td>
					</thead>
				</table>
			</div>
		</div>
    <br>
    <div id="divTimeSlots" class="card" style="display:none;">
			<div class="card-header"><h4><span id="spanTimeSlotTitle"></span></h4></div>
			<div class="card-body">
				<div class="container-fluid">
  				<div class="row">
          	<div class="col-md-4">
            	<h5>Movie Poster</h5>
              <div id="divPosterPreview" class="card" style="text-align:center;">
              	--- No image available for this Movie ---
              </div>
              <br>
              <iframe name="iframeUploadFiles" id="iframeUploadFiles" src="about:blank" height="60%" width="60%" style="display:none;"></iframe> 
            	<form id="frmUploadPoster" method="post" enctype="multipart/form-data" target="iframeUploadFiles">
              	<input type="hidden" id="hidMrdId" name="hidMrdId" value="">
              	<input type="file" name="txtFile" id="txtFile" value="" accept=".jpeg,.jpg,.png,.gif">
								<input type="submit" id="btnUpload" name="btnUpload" class="btn btn-sm btn-primary" value="Upload">
        			</form>
            </div>
          	<div class="col-md-8">
            	<h5>Time Slots</h5>
              <span>
              	<span id="spanAddTS" class="fas fa-plus-circle" style="font-size:20px; cursor:pointer;" title="Insert Time Slot"></span>&emsp;
                <span id="spanDeleteTS" class="fas fa-trash-alt" style="font-size:20px; cursor:pointer;" title="Delete Time Slot"></span>&emsp;
                <span id="spanRefreshTS" class="fas fa-sync" style="font-size:20px; cursor:pointer;" title="Refresh Time Slot"></span>&emsp;
              </span>
              <br>
              <table id="tblTimeSlots" class="table table-sm table-hover" width="100%">
                <thead>
                  <td data-orderable="false"><strong></strong></td>
                  <td><strong>Start Time</strong></td>
                  <td><strong>End time</strong></td>
                  <td><strong>No. of Seats</strong></td>
                  <td><strong>No. of Reserved Seats</strong></td>
                  <td><strong>Maintain Date</strong></td>
                  <td><strong>Last Modified by</strong></td>
                  <td data-orderable="false"></td>
                </thead>
              </table>
            </div> <!-- col-md-8 -->
          </div> <!-- row -->
      	</div> <!-- container -->
			</div>
		</div>
	</div>
	
	<!-- Modal For Movie record insert -->
	<div id="divInsertMovie" class="modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><span id="spanModalTitle">Insert Movie Details</span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="frmMovie">
						<table id="tblMovieForm" class="table table-sm">
							<tbody>
								<tr>
									<td class="cols-sm-3"><strong>Title</strong></td>
									<td><strong>:</strong></td>
									<td class="cols-sm-9">
										<input type="text" id="txtTitle" class="form-control" maxlength="75">
									</td>
								</tr>
								<tr>
									<td><strong>Description</strong></td>
									<td><strong>:</strong></td>
									<td>
										<textarea id="txtDesct" class="form-control" rows="5" style="resize: none;"></textarea>
									</td>
								</tr>
								<tr>
									<td><strong>Showing Date</strong></td>
									<td><strong>:</strong></td>
									<td>
										<input type="text" id="txtStartDate"> 
									</td>
								</tr>
								<tr>
									<td><strong>Seat Price</strong></td>
									<td><strong>:</strong></td>
									<td>
										<input type="text" id="txtSeatPrice" class="form-control"> 
									</td>
								</tr>
							</tbody>
						</table>
					</form>
          
          <form id="frmTimeSlots">
						<table id="tblMovieForm" class="table table-sm">
							<tbody>
								<tr>
									<td class="cols-sm-3"><strong>Start time</strong></td>
									<td><strong>:</strong></td>
									<td class="cols-sm-9">
										<input type="text" id="txtStartTime"> 
									</td>
								</tr>
                <tr>
									<td><strong>End Time</strong></td>
									<td><strong>:</strong></td>
									<td>
										<input type="text" id="txtEndTime"> 
									</td>
								</tr>
								<tr>
									<td><strong>Number of Seats Available</strong></td>
									<td><strong>:</strong></td>
									<td>
										<input type="text" id="txtNumSeat" class="form-control" placeholder="18-50" maxlength="2"> 
									</td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" id="btnSave" class="btn btn-primary">Save</button>
					<button type="button" id="btnCloseModal" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
  
	<script src="manage/control.js"></script>
</body>

</html>