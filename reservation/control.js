$(function(){
	rsrv.init();
});

var rsrv = {
	path : "reservation/ajax.php",
	user_rsrv : [],
	taken_seat : null,
	mode : null,
	init : function(){
		rsrv.loadMovie();
		
		$('#selTimeSlot').change(function(){
			rsrv.loadMovie();
			$('#frmTrans').trigger('reset');
			$('#frmTrans, #frmCancel').css('display', 'none');
		});
		
		$(document).on('click', 'button.seat', function(){
			var seat_no = $(this).attr('seat_no');
			var seat_price = parseFloat($('#spanSeatPrice').text());
			var btnClass = (rsrv.mode == 'reserve') ? 'btn-success' : 'btn-warning'; 
			
			if($(this).hasClass('btn-success') == true)
			{
				$(this).removeClass('btn-success').addClass('btn-primary');
				$('#ulSelectedSeats').append('<li>'+seat_no+'</li>');
			}
			else if(($(this).hasClass('btn-warning') == true))
			{
				$(this).removeClass('btn-warning').addClass('btn-primary');
				$('#ulCanceledSeats').append('<li>'+seat_no+'</li>');
			}
			else
			{
				$(this).removeClass('btn-primary').addClass(btnClass);
				$('li:contains("'+seat_no+'")').remove();
			}
			
			$('#spanTotalPrice').empty().append(parseFloat($('#ulSelectedSeats li').length * seat_price));
		});
		
		$('#chkCancelAll').click(function(){
			if($(this)[0].checked == true)
			{
				$('#divSeats button.btn-warning').click();
				$('#divSeats button.btn-warning').removeClass('btn-warning').addClass('btn-primary');
			}
			else
			{
				$('#divSeats button.btn-primary').click();
				$('#divSeats button.btn-primary').removeClass('btn-primary').addClass('btn-warning');
			}
		});
		
		$('#btnShowRsrv, #btnCancelRsrv').click(function(e){
			e.preventDefault();
			if($(this)[0].id == 'btnShowRsrv')
			{
				rsrv.mode = 'reserve';
				$('#frmTrans').css('display', 'block');
				$('#frmCancel').css('display', 'none');
				
				$('#divSeats :button').attr('disabled', false);
				$('#ulCanceledSeats').empty();
				
				for(var l=0; l<rsrv.taken_seat.length; l++)
				{
					var btn = $('button.seat[seat_no="'+rsrv.taken_seat[l]+'"]');
					btn.prop('disabled', true);
					btn.removeClass('btn-warning').removeClass('btn-primary').addClass('btn-danger');
				}
			}
			else
			{
				rsrv.mode = 'cancel';
				$('#frmTrans').css('display', 'none');
				$('#frmCancel').css('display', 'block');
				
				$('#divSeats :button').attr('disabled', true);
				
				if(rsrv.user_rsrv != null)
				{
					for(var l=0; l<rsrv.user_rsrv.length; l++)
					{
						var btn = $('button.seat[seat_no="'+rsrv.user_rsrv[l]+'"]');
						btn.prop('disabled', false);
						btn.removeClass('btn-danger').addClass('btn-warning');
					}
				}
			}
		});
		
		$('#btnResetCurrent').click(function(){
			if(confirm('Clear current selection?'))
			{
				$('button.btn-primary').removeClass('btn-primary').addClass('btn-success');
				$('#spanTotalPrice, #ulSelectedSeats').empty();
			}
		});
		
		$('#btnSaveTrans').click(function(){
			var arrSelected = [];
			$('#ulSelectedSeats li').each(function(){
				arrSelected.push($(this).text());
			});
			
			if($('#txtCustomerName').val().trim() == '')
			{
				alert('Please input customer name.');
				return false;
			}
			
			if(arrSelected != 0)
			{
				if(confirm('Proceed in reservation?'))
				{
					$.ajax({
						method : "post",
						url : rsrv.path,
						data : {
							strTodo : "saveReservation",
							strMrdId : $('#hidSelectedMrdId').val(),
							selTimeSlot : $('#selTimeSlot').val(),
							txtCustomerName : $('#txtCustomerName').val().trim(),
							strStatus : "R",
							lstSelected : arrSelected.join(',')
						},
						success : function(data){
							var res = (data.trim()).split(",");	
							alert(res[1]);
							
							if(res[0]== "Error")
							{
								return false;
							}
							
							rsrv.user_rsrv = ((rsrv.user_rsrv == null) ? arrSelected : rsrv.user_rsrv.concat(arrSelected));
							rsrv.taken_seat = ((rsrv.taken_seat == null) ? arrSelected : rsrv.taken_seat.concat(arrSelected));
							$('button.btn-primary').removeClass('btn-primary').addClass('btn-danger').prop('disabled', true);
							$('#spanTotalPrice, #ulSelectedSeats').empty();
							$('#txtCustomerName').val('');
						}
					});
				}
			}
			else
			{
				alert('Please select seat before proceeding.');
			}
		});
		
		$('#btnSaveCanceled').click(function(){
			var arrSelected = [];
			$('#ulCanceledSeats li').each(function(){
				arrSelected.push($(this).text());
			});
			
			if(arrSelected != 0)
			{
				if(confirm('Proceed in cancellation?'))
				{
					$.ajax({
						method : "post",
						url : rsrv.path,
						data : {
							strTodo : "saveCancellation",
							strMrdId : $('#hidSelectedMrdId').val(),
							selTimeSlot : $('#selTimeSlot').val(),
							strStatus : "C",
							lstSelected : arrSelected.join(',')
						},
						success : function(data){
							var res = (data.trim()).split(",");	
							alert(res[1]);
							
							if(res[0]== "Error")
							{
								return false;
							}
							
							$('button.btn-primary').removeClass('btn-primary').addClass('btn-success').prop('disabled', false);
							$('#ulCanceledSeats').empty();
							
							rsrv.taken_seat = rsrv.taken_seat.filter(function(val) {
								return arrSelected.indexOf(val) == -1;
							});
							
							rsrv.user_rsrv = rsrv.user_rsrv.filter(function(val) {
								return arrSelected.indexOf(val) == -1;
							});
							
							$('#btnShowRsrv').click();
						}
					});
				}
			}
			else
			{
				alert('Please select seat/s to be canceled.');
			}
		});
	},//init
	
	loadMovie : function(){
		$.ajax({
			method : "post",
			url : rsrv.path,
			data : {
				strTodo : "loadSeat",
				strMrdId : $('#hidSelectedMrdId').val(),
				selTimeSlot : $('#selTimeSlot').val(),
				strStatus : 'R'
			},
			success : function(data){
				rsrv.populateSeat(data);
				($('#selTimeSlot option:selected').attr('dsbl') == 'D') ? $('#divSeats button, #btnShowRsrv, #btnCancelRsrv').prop('disabled', true) : $('#btnShowRsrv, #btnCancelRsrv').prop('disabled', false);
				$('#divSeats button').prop('disabled', true);
			}
		});
	},//loadMovie
	
	populateSeat : function(data){
		rsrv.taken_seat = [];
		var res = JSON.parse(data);
		var num_seats = res[0].NUM_SEATS, rows = 0, seats = [];
		
		if(num_seats >= 18 && num_seats <= 30)
		{
			rows = 3;
		}
		else if(num_seats >= 31 && num_seats <= 40)
		{
			rows = 4;
		}
		else if(num_seats >= 41 && num_seats <= 50)
		{
			rows = 5;
		}
		
		var curr_num = 0, rem = 0, label = '', excess = 0, excess_per_row = 1;
		curr_num = parseInt(num_seats/rows); //min num seat per row
		excess = num_seats - (curr_num * rows);
		
		for(var i=rows; i>0; i--)
		{
			label = String.fromCharCode(i+64);
			excess_per_row = (excess !=0 ? excess_per_row : 0);
			total_row_num = curr_num+excess_per_row;
			excess -= excess_per_row;		
			seats.push({letter : label, num : total_row_num});
		}
		
		$('#divSeats').empty();
		$.each(seats, function (index, value) {
			var tr = '<div class="row justify-content-center">';
			for(var j=0; j<(parseInt(value.num)); j++)
			{
				tr += "<div><button type='button' class='btn btn-success seat' style='cursor:pointer;' seat_no='"+value.letter+(j+1)+"'><i class='fas fa-couch' style='font-size:24px;'></i><br>"+value.letter+(j+1)+"</button></div>";
			}
			
			tr += '</div>';
			$('#divSeats').append(tr);
		});
		
		$('#divSeats').append('<div class="row justify-content-center" style="paading-top:1rem;"><h3>S C R E E N</h3></div>');
		
		if(res[0].TAKEN_SEATS != null)
		{
			var arrTakenSeat = res[0].TAKEN_SEATS.split(',');
			rsrv.taken_seat = arrTakenSeat;
			
			for(var k=0; k<arrTakenSeat.length; k++)
			{
				var btn = $('button.seat[seat_no="'+arrTakenSeat[k]+'"]');
				btn.prop('disabled', true);
				btn.removeClass('btn-success').addClass('btn-danger');
			}
		}
		rsrv.user_rsrv = res[0].USER_RSRV_SEATS != null ? res[0].USER_RSRV_SEATS.split(',') : null;
	},//populateSeat
}//rsrv