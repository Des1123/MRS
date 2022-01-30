$(function(){
	hmpg.init();
});

var hmpg = {
	path : "homepage/ajax.php",
	init : function(){
		hmpg.loadMovie();
		
		$(document).on('click', 'div.movie', function(){
			$('#hidMrdId').val($(this).attr('mrd_id'));
			$('#frmReservation').submit();
		});
	},//init
	
	loadMovie : function(){
		$.ajax({
			method : "post",
			url : hmpg.path,
			data : {
				strTodo : "loadMovie"
			},
			success : function(data){
				hmpg.populateMovieList(data);
			}
		});
	},//loadMovie
	
	populateMovieList : function(data){
		var res = JSON.parse(data);
		var listName, img, ctrC = 0, ctrN = 0;
		
		ctrN = res.filter(({CATEGORY:c}) => c === 'N').length;
		ctrC = res.filter(({CATEGORY:c}) => c === 'C').length;
		
		$.each(res, function (index, res) {
			(res.CATEGORY == 'N') ? listName = 'divNowShowing' : listName = 'divComingSoon';
			(res.POSTER_LINK == null) ? img = 'No Image Available' : img = '<img class="responsive mx-auto" style="display:block;" src="manage/posters/'+decodeURIComponent(res.POSTER_LINK)+'"/>';
			var time_slots = '';
			for(var i =0; i<res.TIME_SLOTS.length; i++)
			{
				var toAdd = '<tr><td><strong>'+res.TIME_SLOTS[i].START_TIME_FORMAT+' - '+res.TIME_SLOTS[i].END_TIME_FORMAT+'</strong></td><td align="center" valign="middle"><strong>'+res.TIME_SLOTS[i].AVAIL_SEAT+'</strong></td></tr>';
				time_slots += toAdd;
			}
			
			$('#'+listName).append('<div class="list-group-item list-group-item-action flex-column align-items-start movie" mrd_id ='+res.MRD_ID+'>'+
																'<div class="row">'+
																	'<div class="col-4">'+
																		img+
																	'</div>'+
																	'<div class="row col-8">'+
																		'<div class="row">'+
																			'<h5 class="mb-1">'+res.TITLE+'</h5>'+
																			'<br><p>'+decodeURIComponent(res.DESCT)+'</p>'+	
																		'</div>'+
																		'<div class="row">'+
																			'<div class="col-4">Date of Showing:&emsp;<strong>'+res.START_DATE_FORMAT+'</strong></div>'+
																				'<div class="col-8"><table class="table table-sm table-borderless"><thead><td>Time Slots</td><td>No. of Remaining Available Seats</td></thead><tbody>'+time_slots+'</tbody></table></div>'+
																			'</div>'+
																		'</div>'+
																	'</div>'+
																'</div>'+
															'</div>');
		});
		
		(ctrN == 0) ? $('#divNowShowing').append('<div class="list-group-item list-group-item-action flex-column align-items-start">No Movie Available</div>') : '';
		(ctrC == 0) ? $('#divComingSoon').append('<div class="list-group-item list-group-item-action flex-column align-items-start">No Movie Available</div>') : '';
	},//populateMovieList
}//hmpg