$(document).ready(function(){

	var noteToUpdate; //pointer to the li of the note that the user just update
	var activeTab = null;
	$('a[data-toggle="pill"]').on('click', function (e) {
		activeTab = $(e.target).attr('href');
		// console.log('active tab', $(e.target).attr('href') );
	});

	// $('#members a').on('click', function (e) {
	// 	e.preventDefault()
	// 	$(this).tab('show')
	// });


	$(document).on('click','#saveNotes',function(){
		let type;
		let src;
		// let extention;
		let projectId = project;

		if(activeTab == null || activeTab == '#link'){
			type = 'link';
			src  = $('#linkInput').val();
		}
		else if( activeTab == '#notes' ){
			type = 'notes';
			src = quill.getContents();
			src = JSON.stringify(src.ops);
		}
		else if( activeTab == '#file' ){
			type = 'file';
			// src = $('#fileInput').val();
			src =  $('input[type=file]')[0].files[0];
			// console.log('name of file', src.name, src.name.split('.').pop());
			// extention = '.'+src.name.split('.').pop()
		}

		// let	data = {
		// 	src: src,
		// 	type: type,
		// 	name: $('#nameInput').val(),
		// 	project: projectId
		// }

		let formData = new FormData();
		formData.append('src', src);
		formData.append('type', type);
		formData.append('name', $('#nameInput').val());
		formData.append('project', projectId);


		$.ajax({
			type: "POST",
			url: base_url+'app/addToProject',
			processData: false,
			contentType: false,
			// data: data,
			data: formData,
			success: function(data){
				if(data<0){
					if(data == -2){
						alert('Your session has expired, please refresh the page');
					}
					else if(data == -3){
						alert('Wrong input values');
					}
					else if(data == -4){
						alert('Database error, please try later');
					}
					else if(data == -5){
						alert('problem with the file');
					}
					else{
						alert('Error, please try later');
					}
				}
				else{
					
					let nameField;
					if(type == 'link'){
						nameField = `<td><a target='_black' href='`+src+`'>`+$('#nameInput').val()+`</a></td>`;
					}	
					else if(type == 'notes'){
						nameField = `<td><a href='#' data-note_id='`+data+`' data-toggle='modal' data-target='#editorModal' data-src='`+src.replace(/\\/g, "\\\\").replace(/\"/,'\\\"') +`' class='openInEditor' >`+$('#nameInput').val()+`</a></td>`;
					}
					else if(type == 'file'){
						nameField = `<td><a target='_black' href='`+base_url+`/app/showProtectedFile?id=`+data+`'>`+ $('#nameInput').val() +`</a></td>`;
						// data = data + extention;
					}

					let str = `
						<tr>
						`+nameField+`
						<td>`+type+`</td>
						<td>`+currentDate()+`</td>
						<td><button data-id='`+data+`' class='btn btn-warning deleteItem'>Delete</button></td>
						</tr>
						`;

					$('#dataTable tbody tr:last').after(str);
					$('#fileInput').val('');
					$('#linkInput').val('');
					$('#notesInput').val('');
					$('#nameInput').val('');

				}
			},
			error: function(er){
				alert(er);
			}
		});


	});


	function currentDate(){
		var today = new Date();
		var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
		var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
		return  (date+' '+time);
	}

  $('[data-toggle="popover"]').popover();

	var toolbarOptions =[
		[{ 'header': [1, 2, 3, 4, 5, 6, false] }],
		['bold', 'italic', 'underline', 'strike'],
		[{ 'list': 'ordered'}, { 'list': 'bullet' }]
	];

	// insert new note
	var quill = new Quill('#notesInput', {
  	theme: 'snow',
		modules: {
  		toolbar: toolbarOptions
  	},
		placeholder: 'A long time ago in a galaxy far, far away....'
  });

	// display/update notes
	var quillModal = new Quill('#notesModal', {
  	theme: 'snow',
		modules: {
  		toolbar: toolbarOptions
  	},
		placeholder: ''
  });

	$(document).on('click','.openInEditor', function(){
		let text = $(this).attr('data-src');
		let noteId = $(this).data('note_id');
		noteToUpdate = $(this);

		// console.log('open editor', text.replace(/\\n/g, 'n').replace(/\\"/g, '\"'));
		$('#modalEditorTitle').text($(this).text());
		$('#updateNote').attr('data-note_id', noteId);
		quillModal.setContents(JSON.parse(text.replace(/\\n/g, 'n').replace(/\\"/g, '\"')));
		// document.querySelector("#notesModal").innerHTML = text;
	});

	$(document).on('click','.deleteItem', function(){

		if(!confirm('Are you sure?')) return;

		let id = $(this).data('id');
		let t = $(this);
		let projectId = project;

		$.ajax({
			type: "POST",
			url: base_url+ 'app/deleteItemFromProject',
			data: {id: id, projId: projectId},
			success: function(data){
				// console.log('data', data)
				if(data>0){
					$(t).parent().parent().remove();
				}
				else{
					alert('Database error, item still exists');
				}
			},
			error: function(er){
				alert(er);
			}
		});

	});

	
	$('#updateNote').click(function(){
		let projectId = project;
		let noteId = $(this).data('note_id');
		let src= quillModal.getContents();
		src = JSON.stringify(src.ops);
		
		$.ajax({
			type: "POST",
			url: base_url+'app/updateNote',
			data: {noteId: noteId, projectId: projectId, src:src},
			success: function(data){
				// console.log('data', data)
				if(data<0){
					if(data==-2) alert('Session time out, refresh the page');
					else if(data == -3 )alert('Incorrect input data');
					else if(data == -4) alert('You are not a member of the project');
					else if(data == -5) alert('Error. please try later');
					else if(data == -6) alert('You can not do that');
				}
				else{
					// quillModal.setContents(JSON.parse(data.replace(/\\n/g, 'n').replace(/\\"/g, '\"')));
					$(noteToUpdate).attr('data-src',src.replace(/\\/g, "\\\\").replace(/\"/,'\\\"') );
					console.log('noteToUpdate', noteToUpdate , src);
				}
			},
			error: function(er){
				alert('Database error, data did not updated');
			}
		});
	});

	
	// search for a friend
	$('#searchForAFriend').keydown(function(){
		let txt = $(this).val();

		$.ajax({
			type: "POST",
			url: base_url+'users/searchForAFriendBasedOnUsername',
			data: {searchString: txt},
			success: function(data){
				console.log('data', data)
				data = JSON.parse(data);
				if(data < 0){
					alert('Database Problem, please try later');
				}
				else{
					$('#listOfSearchedFriends').empty();
					
					if(data.length == 0)
						$('#listOfSearchedFriends').append(`<li class='list-group-item d-flex justify-content-between align-items-center'>No mach results </li>`);

					for(i in data){
						$('#listOfSearchedFriends').append(`
							<li class="list-group-item d-flex justify-content-between align-items-center">
								`+ data[i].username+`
								<button class="addNewFriend btn btn-primary" data-id=`+data[i].id+`>Follow</button>
							</li>
							`);
					}
				}
			},
			error: function(er){
				alert('error: '+er);
			}
		});
	});


	$(document).on('click','.addNewFriend',function(){
		let id = $(this).data('id');
		let row_elem = this;

		$.ajax({
			type: "POST",
			url:base_url +'users/createFriendship',
			data: {friendId: id},
			success: function(data){
				console.log('data', data)
				data = JSON.parse(data);
				if(data < 0){
					if(data == -1){
						alert('Session expired, please refresh the page');
					}
					else if(data == -2){
						alert('Invilid input data');
					}
					else if(data == -3){
						$(row_elem).parent().remove();
						alert('You are already a follower');
					}
					else if(data == -4){
						$(row_elem).parent().remove();
						alert('You can not follow yourself');
					}
					else alert('Database Problem, please try later');
				}
				else{
					$(row_elem).parent().remove();
				}
			},
			error: function(er){
				alert('error: '+er);
			}
		});
	});


	//delete user from friend list
	$(document).on('click','.unfollow',function(){
		let thisElem = this;
		let friendId = $(this).data('id');

		if(confirm('Are you sure you want to unfollow that user?')){
			$.ajax({
				type: 'POST',
				url: base_url+'users/removeFromFriendList',
				data: {friendId: friendId},
				success: function(data){
					if(data<0){
						if(data == -1) alert('Session expired, please refresh the page');
						else if(data == -2) alert('Target user unknown, nothing to do');

					}
					else{
						$(thisElem).parent().remove();
					}
				},
				error: function(){
					alert('Error, database failed');
				}
			});
		}
	});


	$('#nav-fnds-tab').click(function(){
		let thisElem = this;

		$('#listOfFriends').empty();
		$.ajax({
			type: "POST",
			url: base_url+'users/getAllFriends',
			//data: {},
			success: function(data){
				console.log('print all friends', data);
				data = JSON.parse(data);

				if(data == -1) alert('Session expired, please refresh the page');

				for(i in data){
					$('#listOfFriends').append(`
						<li class="list-group-item d-flex justify-content-between align-items-center">
						`+data[i].username+`
						<button class="unfollow btn btn-warning" data-id=`+data[i].id+`>Unfollow</button>
						<button class="addFriendToProject btn btn-primary" data-id=`+data[i].id+`>Add to Project</button>
						</li>`);
				}

			},
			error: function(er){
				alert('Error, database failed');
			}
		});
	});


	$(document).on('click','.addFriendToProject', function(){
		let friendId = $(this).data('id');
		let projectId = project;
		let this_elem = this;

		$.ajax({
			type: "POST",
			url: base_url+'users/addFriendToProject',
			data: {friendId: friendId, projectId: projectId},
			success: function(data){
				if(data<0){
					if(data == -1) alert('Session expired, please refresh the page');
					else if(data == -2 || data == -3) alert('You are not the owner of the project');
					else if(data == -4) alert('User is already member of the project');
				}
				else{
					$(this_elem).parent().remove();
				}
			},
			error: function(er){
				alert('Error, database failed');
			}
		});

	});


	//get all members of the project
	$('#nav-mem-tab').click(function(){
		let projectId = project;

		$.ajax({
			type: "POST",
			url:base_url+ 'users/getMembersOfProject',
			data: {projectId: projectId},
			success: function(data){
				data = JSON.parse(data);
				if(data < 0){
					if(data == -1) alert('Session expired, please refresh the page');
					else if (data == -2) alert('You are not the owner of the project');
				}
				else{
					$('#projectMem').empty();
					for( i in data ){
						$('#projectMem').append(`
						<li class="list-group-item d-flex justify-content-between align-items-center">
							`+ data[i].username+`
							<button title='kick user from project' class="removeFriendFromProject btn btn-warning" data-id=`+data[i].id+`>Kick</button>
						</li>
						`);
					}
				}
			},
			error: function(er){
				alert('Error, database failed');
			}
		});

	});


	// remove user from project
	$(document).on('click','.removeFriendFromProject', function(){
		let projectId = project;
		let userId = $(this).data('id');
		let thisElem = this;

		if(confirm('Are you sure you want to kick this user from the project?')){
			$.ajax({
				type:'POST',
				url: base_url+'users/removeMemberFromProject',
				data:{projectId: projectId, userId: userId},
				success: function(data){
					data = JSON.parse(data);
					if(data<0){
						if(data == -1) alert('Session expired, please refresh the page');
					}
					else{
						$(thisElem).parent().remove();	
					}
				},
				error: function(){
					alert('Error, database failed');
				}
			});
		}

	});

});
