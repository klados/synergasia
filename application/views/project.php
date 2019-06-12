<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<link href="https://cdn.quilljs.com/1.0.0/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>

<script type='text/javascript' src="<?php echo base_url(); ?>assets/js/project.js"></script>
<link href="<?php echo base_url(); ?>assets/css/project.css" rel='stylesheet'>

<div class='container'>

	<br>
	<ul class="nav justify-content-between">
		<li class="nav-item">
			<h5 class='col-10'><?php echo $project->name;?></h5> 
		</li>
		<li class="nav-item">
			<a class="btn btn-success" data-toggle="modal" data-target="#addRecord" href="#">New Note</a>
		</li>
		<li class="nav-item">
			<a class="btn btn-success" data-toggle="modal" data-target="#friendsModal" href="#">Friends</a>
		</li>
		<li class="nav-item">
			<a tabindex="0" class="btn btn-info" role="button" data-toggle="popover" data-trigger="focus" title="<?php echo $project->category; ?>" data-content="<?php echo $project->description; ?>">info</a>
		</li>
	</ul>
	<br>

	<div class='table-responsive'>	
	<table id='dataTable' class='table table-striped'>
	    <thead>
      <tr>
        <th>Name</th>
        <th>Type</th>
				<th>Date</th>
        <th>Delete</th>
      </tr>
    </thead>
		<?php if(!empty($notes)): ?>
		<?php foreach( $notes as $row ): ?>
			<tr>
				<?php if($row['type'] == 'link'): ?>
				<td><a target='_black' href="<?php echo $row['data']; ?>"><?php echo $row['description'];?></a></td>
				<?php elseif($row['type'] == 'notes'): ?>
				<td><a href='#' data-note_id='<?php echo $row['id']; ?>' data-src='<?php echo addslashes($row["data"]);?>' data-toggle="modal" data-target="#editorModal" class='openInEditor' ><?php echo $row['description'];?></a></td>
				<?php elseif($row['type'] == 'file'): ?>
				<td><a target='_black' href="<?php echo base_url('/app/showProtectedFile?id='.$row['id']);?>"><?php echo $row['description'];?></a></td>
				<?php endif;?>
				<td><?php echo $row['type'];?></td>
				<td><?php echo $row['date'];?></td>
				<td><button data-id='<?php echo $row['id'];?>' class='btn btn-warning deleteItem'>Delete</button></td>
			</tr>
		<?php endforeach; ?>
		<?php endif; ?>
  	<tbody>
		</tbody>
	</table>
	</div>

<!-- The Modal -->
<div class="modal" id="addRecord">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Store to this Project</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
				<ul class='nav nav-pills' role='tablist' id='ca_tab' >
					<li class='nav-item'><a class="nav-link active" data-toggle="pill" href="#link">link</a></li>
					<li class='nav-item'><a class="nav-link" data-toggle="pill" href="#notes">notes</a></li>
					<li class='nav-item'><a class="nav-link" data-toggle="pill" href="#file">file</a></li>
				</ul>

				<div class="form-group">
					<label for="name">Name the note:</label>
					<input type="text" class="form-control" id="nameInput" placeholder='How will you remember me?'>
				</div>

				<div class="tab-content">
					<div class="tab-pane active" id="link">
  					<div class="form-group">
    					<label for="linkInput">Store a link:</label>
    					<input type="text" class="form-control" id="linkInput" placeholder='http(s)://'>
  					</div>
					</div>
					<div class="tab-pane" id="notes">
						<div class="form-group">
  						<label for="notesInput">Type some text:</label>
  						<!-- <textarea class="form&#45;control" id="notesInput"></textarea> -->
							<div id='notesInput' style="height:100px" class='form-control'></div>

						</div>
					</div>
					<div class="tab-pane" id="file">
						<div class="custom-file">
						<?php echo form_open_multipart();?>
							<input type="file" name='src' class="custom-file-input" id="fileInput">
							<label class="custom-file-label" for="fileInput">Choose file</label>
						<?php form_close(); ?>
						</div>
					</div>
				</div>

  		</div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        <button id='saveNotes' type="button" class="btn btn-success" data-dismiss="modal">Store</button>
      </div>

  	</div>
  </div>
</div>

<!-- Modal Friends-->
<div class="modal" id="friendsModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Friends</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

				<nav>
					<div class="nav nav-tabs" id="members" role="tablist">
						<a class="nav-item nav-link active show" id="nav-fnd-tab" data-toggle="tab" href="#nav-fnd" role="tab" aria-controls="nav-home">Find Friend</a>
						<a class="nav-item nav-link" id="nav-fnds-tab" data-toggle="tab" href="#nav-fnds" role="tab" aria-controls="nav-home">My Friends</a>
						<a class="nav-item nav-link" id="nav-mem-tab" data-toggle="tab" href="#nav-mem" role="tab" aria-controls="nav-profile">Members of Project</a>
					</div>
				</nav>
				<div class="tab-content" id="nav-tabContent">
					<div class="tab-pane active show" id="nav-fnd" role="tabpanel" aria-labelledby="nav-fnd-tab">
						<br>
						<input id='searchForAFriend' class="form-control form-control-lg form-control-borderless" type="search" placeholder="Search your friends">
						<br>
						<ul id='listOfSearchedFriends' class="list-group">
						</ul>
					</div>


					<div class="tab-pane" id="nav-fnds" role="tabpanel" aria-labelledby="nav-fnds-tab">
						<br>
						<ul id='listOfFriends' class='list-group'>
						</ul>	
					</div>

					<div class="tab-pane" id="nav-mem" role="tabpanel" aria-labelledby="nav-mem-tab">
						<ul id='projectMem' class="list-group">
						</ul>
					</div>

				</div>

  		</div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        <!-- <button id='saveNotes' type="button" class="btn btn&#45;success" data&#45;dismiss="modal">Store</button> -->
      </div>

  	</div>
  </div>
</div>


<!-- Modal Editor-->
<div class="modal" id="editorModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><span id='modalEditorTitle'></span></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

				<h5 id='nameOfTheNote'></h5>

				<div id='notesModal' style="height:100px" class='form-control'></div>

  		</div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        <button data-note_id="" id='updateNote' type="button" class="btn btn-success" data-dismiss="modal">Update</button>
      </div>

  	</div>
  </div>
</div>


	<script>
		var project=`<?php echo $projectId; ?>`;
		var base_url = '<?php echo base_url() ?>';
  </script>
</div>
