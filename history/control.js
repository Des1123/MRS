$(function(){
	hist.init();
});

var hist = {
	table : null,
	path : "history/ajax.php",
	init : function(){
		hist.table = $('#tblHistory').DataTable({
			"lengthChange": false,
			"searching" : false,
			ajax : {
				method : "post",
				url : hist.path,
				data : function(d){
					d.strTodo = "displayHistory",
					d.selMovie = $('#selMovie').val(),
					d.selTimeSlot = $('#selTimeSlot').val()
				}
			},
			columns : [
				{data : "CUSTOMER_NAME"},
				{data : "SEAT_LIST"},
				{data : "STATUS", render : function(data){
					var wording = '';
					
					switch(data)
					{
						case "R" : wording = "Reserved"; break;
						case "C" : wording = "Cancelled"; break;
						case "U" : wording = "Used"; break;
						default : wording = ""; break;
					}
					return wording;
				}},
				{data : "MAINTAIN_DATE"}
			],
			"order" : [[3, "desc"]]
		});
		
		$('#selMovie').change(function(){
			hist.table.clear().draw();
			hist.getTimeSlot();
		});
		
		$('#selTimeSlot').change(function(){
			hist.table.ajax.reload();
		});
		
		$('#btnExport').click(function(){
			if($('#selMovie').val() != null && $('#selTimeSlot').val() != null)
			{
  			window.open("history/export_history.php?selMovie="+$('#selMovie').val()+"&selTimeSlot="+$('#selTimeSlot').val());
			}
		});
	},//init
	
	getTimeSlot : function(){
		if($('#selMovie').val() !=0)
		{
			$.ajax({
				method : "post",
				url : hist.path,
				data : {
					strTodo : "getTimeSlot",
					strMrdId : $('#selMovie').val()
				},
				success : function(data)
				{
					var res = JSON.parse(data);
					
					$('#selTimeSlot').empty().append('<option value="0"  selected disabled>Select...</option>');
					
					$.each(res, function (index, value) {
						$('#selTimeSlot').append('<option value="'+res[index].MRTS_ID+'">'+res[index].START_TIME+' - '+res[index].END_TIME+'</option>');
					});
				}
			});
		}
	},//getTimeSlot
}//mrs