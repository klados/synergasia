$( document ).ready(function() {
	console.log( "ready!" );

	$(document).on('click','.delete',function(){

		let id = $(this).data('id');
		console.log('delete project', id);
		let ans = confirm('Are you sure you want to delete this project');
		let t = $(this);

		if(ans){
			$.ajax({
				type: "POST",
				url: base_url+'app/deleteProject',
				data: {id:id},
				success: function(data){
					if(data<0){
						if(data == -3)alert('You do not own the project');
						else alert('Problem, refresh the page');
					}
					else{
						$(t).parent().parent().remove();
						console.log('data', data)
					}
				},
				error: function(er){
					alert(er);
				}
				// dataType: dataType
			});



		}

	});


	$('#searchProject').keyup(function(){
		let searchText = $(this).val().toLowerCase();
		console.log('search', $(this).val().toLowerCase());

		$("#projects .ca").filter(function() {
			console.log('>>',$(this).find('.card .card-title span').text().toLowerCase(), $(this).find('.card .card-title span').text().toLowerCase().indexOf(searchText));

  		$(this).toggle($(this).find('.card .card-title span').text().toLowerCase().indexOf(searchText) >= 0);
  	});

	});

});
