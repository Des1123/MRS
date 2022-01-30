$(function(){
	mrs.init();
});

var mrs = {
	table : null,
	table2 : null,
	movie_details : null,
	ts_details : null,
	mode : null,
	action : null,
	path : "manage/ajax.php",
	init : function(){
		mrs.table = $('#tblMovies').DataTable({
			"lengthChange": false,
			ajax : {
				method : "post",
				url : mrs.path,
				data : {
					strTodo : "displayMovies"
				}
			},
			columns : [
				{data : "MRD_ID",  render : function (data, type, row, meta){
					var dsbl = (row.DSBL == 'D' ? 'disabled title="You cannot delete the past and current movie showing"' : ''); 
					return '<input type="checkbox" value="'+data+'" '+dsbl+'>';
				}},
				{data : "TITLE"},
				{data : "DESCT"},
				{data : "SEAT_PRICE"},
				{data : "START_DATE_FORMAT"},
				{data : "USER_FROM"},
				{data : "MAINTAIN_DATE_FORMAT"},
				{data : "MRD_ID",  render : function (data, type, row, meta){
					var title = (row.DSBL == 'D' ? 'title="You cannot edit the past and current movie showing"' : 'title="Edit this record"'); 
					var edit = (row.DSBL == 'D' ? '' : 'editMovie');
					return '<span id="spanEditMovie" class="fas fa-edit '+edit+'" style="cursor:pointer;" '+title+'></i>';
				}}
			],
			"columnDefs" : [
				{"width": "3%", "targets": [0,7], "className" : "dt-body-center"},
				{"width": "5%", "targets": [3,4]},
				{"width": "12%", "targets": [6]},
				{"width": "10%", "targets": [1]},
				{"width": "10%", "targets": [5], "className" : "dt-body-center"}
			],
			"order" : [[4, "desc"]]
		});
		
		$('#tblMovies').on('click', 'tr', function(e){
			var totalRecords = mrs.table.page.info().recordsTotal;
			if(totalRecords!=0)
			{
				mrs.movie_details= mrs.table.row(this).data();
				var This= $(this);
				var getTarget= $(e.target);
				mrs.updateMovieForm(This, getTarget);
				$('#hidMrdId').val(mrs.movie_details.MRD_ID);
			}
		}); //tblMovies on click tr
		
		$(document).on('click', 'span[id^="spanAdd"], span.editMovie, span.editTimeSlot', function(){
			mrs.mode = $(this).closest('div').closest('div')[0].offsetParent.id;
			var id = $(this)[0].id;
			var action_string = '';
			
			if(id.indexOf("Edit") >= 0)
			{
				mrs.action = 'UpdateData';
				action_string = 'Update';
			}
			else
			{
				mrs.action = 'AddData';
				action_string = 'Add';
				$('#frmMovie,#frmTimeSlots').trigger("reset");
				$('#txtFile').val('');
			}
			
			if(mrs.mode == 'divMovies')
			{
				$('#spanModalTitle').text(action_string+' Movie Details');
				$('#frmMovie').css('display', 'block');
				$('#frmTimeSlots').css('display', 'none');
				$('#divInsertMovie').modal('show');
			}
			else
			{
				$('#spanModalTitle').text(action_string+' Time Slots');
				$('#frmMovie').css('display', 'none');
				$('#frmTimeSlots').css('display', 'block');
				$('#txtStartTime').val('00:00:00').data('Zebra_DatePicker');
				$('#txtEndTime').val('00:00:00').data('Zebra_DatePicker');
				mrs.movie_details.DSBL == 'D' ? '' : $('#divInsertMovie').modal('show');
			}
		});
		
		mrs.table2 = $('#tblTimeSlots').DataTable({
			"lengthChange": false,
			"searching" : false,
			ajax : {
				method : "post",
				url : mrs.path,
				data : function(d){
					d.strTodo = "displayTimeSlots",
					d.strMrdId = mrs.movie_details!=null ? mrs.movie_details.MRD_ID : '' 
				}
			},
			columns : [
				{data : "MRTS_ID", render : function (data, type, row, meta){
					var dsbl = (row.RSRV_CNT != 0 || row.DSBL_TIME == 'D' ? 'disabled title="You cannot delete the past time slot and has reservations"' : ''); 
					return '<input type="checkbox" value="'+data+'" '+dsbl+'>';
				}},
				{data : "START_TIME_FORMAT"},
				{data : "END_TIME_FORMAT"},
				{data : "NUM_SEATS"},
				{data : "RSRV_CNT"},
				{data : "MAINTAIN_DATE_FORMAT"},
				{data : "USER_FROM"},
				{data : "MRTS_ID", render : function (data, type, row, meta){
					var title = (row.RSRV_CNT != 0 || row.DSBL_TIME == 'D' ? 'title="You cannot edit the past time slot and has reservations"' : 'title="Edit this record"'); 
					var edit = (row.RSRV_CNT != 0  || row.DSBL_TIME == 'D'? '' : 'editTimeSlot');
					
					return '<span id="spanEditTimeSlot" class="fas fa-edit '+edit+'" style="cursor:pointer;" '+title+'></i>';
				}}
			],
			"columnDefs" : [
				{"width": "3%", "targets": [0,6], "className" : "dt-body-center"},
			],
			"order" : [[1, "asc"]]
		});
		
		$('#tblTimeSlots').on('click', 'tr', function(e){
			var totalRecords = mrs.table.page.info().recordsTotal;
			if(totalRecords!=0)
			{
				mrs.ts_details= mrs.table2.row(this).data();
				var This= $(this);
				var getTarget= $(e.target);
				mrs.updateTimeSlot(This, getTarget);
			}
		}); //tblTimeSlots on click tr
		
		var d = new Date();
		var month = d.getMonth()+1;
		var day = d.getDate()+1;
		var date_tmrrw =  (month<10 ? '0' : '') + month + '/' +(day<10 ? '0' : '') + day+ '/'+d.getFullYear();
		
		$('#txtStartDate').Zebra_DatePicker({
			format : 'm/d/Y',
			direction: [date_tmrrw, false]
		});
		
		$('#txtStartTime').Zebra_DatePicker({
			format : 'H:i:s',
			pair : $('#txtEndTime')
		});
		$('#txtStartTime').val('00:00:00').data('Zebra_DatePicker');
		
		$('#txtEndTime').Zebra_DatePicker({
			format : 'H:i:s'
		});
		$('#txtEndTime').val('00:00:00').data('Zebra_DatePicker');
		
		$('#btnSave').click(function(){
			var blankFields = mrs.formValidation();
			
			if(blankFields == '')
			{
				(mrs.mode == 'divMovies') ? mrs.saveMovie() : mrs.saveTimeSlot();
			}
			else
			{
				alert('Please fill out the following fields:\n'+blankFields);
			}
		});//btnSave - click
		
		$('#spanDeleteMovie').click(function(){
			mrs.deleteMovie();
		});//spanDeleteMovie - Delete Data
		
		$('#spanDeleteTS').click(function(){
			mrs.deleteTimeSlot();
		});//spanDeleteTS - Delete Data
		
		$('#txtFile').change(function(){
			var File= $('#txtFile');
			var FileSize= (File[0].files[0].size);
			
			if(FileSize== 0)
			{
				alert('The file you selected is Empty!');
				$('#txtFile').val('');
				return false;
			}
			if(FileSize > 300000)
			{
				alert('File cannot exceed 3MB!');
				return false;
			}
		});
		
		$("#txtSeatPrice, #txtNumSeat").keydown(function (event) {
			if (event.shiftKey == true)
			{
				event.preventDefault();
			}
	
			if(!((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||event.keyCode == 39 || event.keyCode == 46 || ($(this)[0].id == 'txtSeatPrice' && (event.keyCode == 190 || event.keyCode == 110))))
			{
				event.preventDefault();
			}
	
			if($(this).val().indexOf('.') !== -1 && (event.keyCode == 190|| event.keyCode == 110))
			{
					event.preventDefault();
			}
    }); //allow number and period ONLY for input
		
		$('#btnUpload').click(function(){			
			var file_data = $('#txtFile').prop('files')[0];   
			var form_data = new FormData();
			form_data.append('hidMrdId',$('#hidMrdId').val());
			form_data.append('strTodo', 'uploadFile');
			form_data.append('txtFile', file_data);                      
			$.ajax({
					method : 'post',
					url: 'manage/upload.php',
					dataType: 'text', 
					cache: false,
					contentType: false,
					processData: false,
					data: form_data,
					success: function(data)
					{
						var res = (data.trim()).split(",");	
						alert(res[1]);
						
						if(res[0]== "Error")
						{
							return false;
						}
						
						$('#divPosterPreview').empty().prepend('<img class="responsive mx-auto" style="display:block;" src="'+res[2]+'"/>')
					}
			 });
		});
	},//init
	
	formValidation : function(){
		var blankFields = '';
		
		if(mrs.mode == 'divMovies')
		{
			$('#txtTitle').val().trim() != '' ? blankFields+='' : blankFields+='\n\t- Title';
			$('#txtDesct').val().trim() != '' ? blankFields+='' : blankFields+='\n\t- Description';
			$('#txtStartDate').val().trim() != '' ? blankFields+='' : blankFields+='\n\t- Showing Date';
			$('#txtSeatPrice').val().trim() != '' ? blankFields+='' : blankFields+='\n\t- Seat Price';	
		}
		else
		{
			$('#txtStartTime').val().trim() != '' ? blankFields+='' : blankFields+='\n\t- Start Time';
			$('#txtEndTime').val().trim() != '' ? blankFields+='' : blankFields+='\n\t- End Time';
			($('#txtEndTime').val() > $('#txtStartTime').val()) ? blankFields+='' : blankFields+='\n\t- End Time (Should be later than the start time.)';
			$('#txtNumSeat').val().trim() != '' ? blankFields+='' : blankFields+='\n\t- No. of Available Seats';
			parseInt($('#txtNumSeat').val()) < 18 || parseInt($('#txtNumSeat').val()) > 50 ? blankFields+='\n\t- No. of Available Seats (Input within 18 - 50 range only)' : blankFields+='' ;
		}
		
		return blankFields;
	},//formValidation
	
	updateMovieForm : function(This, getTarget){
		$('#tblMovies tbody>tr').removeClass('selected');
		$('#txtFile').val('');
		
		if(mrs.movie_details!=null)
		{
			if(!getTarget.is('input:checkbox'))
			{
				This.toggleClass('selected');
				
				mrs.table2.ajax.reload();
				$('#divTimeSlots').css('display', 'block');
				$('#spanTimeSlotTitle').text('').text(mrs.movie_details.TITLE);
				(mrs.movie_details.POSTER_LINK != null) ? $('#divPosterPreview').empty().prepend('<img class="responsive mx-auto" style="display:block;" src="manage/posters/'+mrs.movie_details.POSTER_LINK+'"/>') : $('#divPosterPreview').empty().prepend('--- No image available for this Movie ---');
				mrs.movieFormData();
			}
			else
			{
				This.toggleClass('table-danger');
			}
		}
	},//updateMovieForm
	
	movieFormData : function(){
		$('#txtTitle').val(decodeURIComponent(mrs.movie_details.TITLE));
		$('#txtDesct').val(decodeURIComponent(mrs.movie_details.DESCT));
		$('#txtStartDate').val(mrs.movie_details.START_DATE_FORMAT);
		$('#txtSeatPrice').val(mrs.movie_details.SEAT_PRICE);
	},//movieFormData
	
	saveMovie : function(){
		if(confirm('Proceed in saving the data?'))
		{
			$.ajax({
				method : "post",
				url : mrs.path,
				data : {
					strTodo : "saveMovie",
					action : mrs.action,
					strMrdId : mrs.movie_details != null ? mrs.movie_details.MRD_ID : '',
					txtTitle : encodeURIComponent($('#txtTitle').val().trim()),
					txtDesct : encodeURIComponent($('#txtDesct').val().trim()),
					txtSeatPrice : $('#txtSeatPrice').val().trim(),
					txtStartDate : $('#txtStartDate').val()
				},
				success : function(data){
					var res = (data.trim()).split(",");	
					alert(res[1]);
					mrs.table.ajax.reload();
					
					if(res[0]== "Error")
					{
						return false;
					}
					
					$('#divInsertMovie').modal('hide');
					$('#frmMovie').trigger("reset");
				}
			});
		}
	},//saveMovie
	
	updateTimeSlot : function(This, getTarget){
		$('#tblTimeSlots tbody>tr').removeClass('selected');
		
		if(mrs.ts_details!=null)
		{
			if(!getTarget.is('input:checkbox'))
			{
				This.toggleClass('selected');
				$('#divTimeSlots').css('display', 'block');
				mrs.timeSlotFormData();
			}
			else
			{
				This.toggleClass('table-danger');
			}
		}
	},//updateTimeSlot
	
	timeSlotFormData : function(){
		$('#txtStartTime').val(mrs.ts_details.START_TIME_FORMAT);
		$('#txtEndTime').val(mrs.ts_details.END_TIME_FORMAT);
		$('#txtNumSeat').val(mrs.ts_details.NUM_SEATS);
	},//timeSlotFormData
	
	saveTimeSlot : function(){
		if(confirm('Proceed in saving the data?'))
		{
			$.ajax({
				method : "post",
				url : mrs.path,
				data : {
					strTodo : "saveTimeSlots",
					action : mrs.action,
					strMrdId : mrs.movie_details.MRD_ID,
					strMrtsId : mrs.ts_details!= null ? mrs.ts_details.MRTS_ID: '',
					txtNumSeat : $('#txtNumSeat').val().trim(),
					txtStartTime : $('#txtStartTime').val(),
					txtEndTime : $('#txtEndTime').val()
				},
				success : function(data){
					var res = (data.trim()).split(",");	
					alert(res[1]);
					mrs.table2.ajax.reload();
					
					if(res[0]== "Error")
					{
						return false;
					}
					
					$('#divInsertMovie').modal('hide');
					$('#frmTimeSlots').trigger("reset");
				}
			});
		}//confirm
	},//saveTimeSlot
	
	deleteMovie : function(){
		var arrMrdId = [];
		
		$('#tblMovies tbody>tr').find('input:checkbox:checked').each(function(){
			arrMrdId.push($(this).val());
		});
		
		if(arrMrdId.length != 0)
		{
			if(confirm('Are you sure you want to delete this record/s? \n\nNote: All data assiociated to this record will also be deleted.'))
			{
				$.ajax({
					method : "post",
					url : mrs.path,
					data : {
						strTodo : "deleteMovie",
						lstMrdId : arrMrdId.join(',')
					},
					success : function(data){
						var res = (data.trim()).split(",");	
						alert(res[1]);
						mrs.table.ajax.reload();
						
						if(res[0]== "Error")
						{
							return false;
						}
					}
				});
			}
		}
		else
		{
			alert('Please select records to be deleted.');
		}
	},//deleteMovie
	
	deleteTimeSlot : function(){
		var arrMrtsId = [];
		
		$('#tblMovies tbody>tr').find('input:checkbox:checked').each(function(){
			arrMrtsId.push($(this).val());
		});
		
		if(arrMrtsId.length != 0)
		{
			if(confirm('Are you sure you want to delete this record/s?'))
			{
				$.ajax({
					method : "post",
					url : mrs.path,
					data : {
						strTodo : "deleteTimeSlot",
						lstMrdId : arrMrtsId.join(',')
					},
					success : function(data){
						var res = (data.trim()).split(",");	
						alert(res[1]);
						mrs.table2.ajax.reload();
						
						if(res[0]== "Error")
						{
							return false;
						}
					}
				});
			}
		}
		else
		{
			alert('Please select records to be deleted.');
		}
	},//deleteTimeSlot
}//mrs