<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<script type='text/javascript' src="<?php echo base_url(); ?>assets/js/dashboard.js"></script>

<div class='container'>

	<br>
  	<input class="form-control" id="searchProject" type="text" placeholder="Search...">
	<br>

	<?php if( count($projects) == 0 ):  ?>
	<div class="alert alert-info">
		You do not have any projects at the moment! <?php echo anchor('app/create','Create one', array('class'=>'nav-link')); ?> 
	</div>
	<?php endif; ?>

	<div id='projects' class='row justify-content-center'>

		<?php foreach($projects as $project): ?>
		<div class="ca col-auto mb-3">
		<div class="card">
			<div class="card-body">
			<h5 class="card-title"><b>Title: </b><span><?php echo $project['name']; ?></span></h5>
			<h6 class="card-subtitle"><b>Created Day:</b><?php echo $project['created_at']; ?></h6>
				<span class="card-text"><b>Category:</b> <?php echo $project['category']; ?> </span>
				<br>
				<span class="card-text"><b>Description:</b> <?php echo $project['description']; ?> </span>
				<br>
				<span class="card-text"><b>Level:</b> <?php echo ($project['owner_email']== $this->session->userData('email'))? 'Admin': 'User'; ?> </span>
				<br>
				<a href="#" data-id="<?php echo $project['id']; ?>" class="btn btn-danger delete">Delete Project</a>
				<?php echo anchor('app/project/'.$project['id'],'Open Project', array('class'=>'btn btn-success')); ?>
			</div>
		</div>
		</div>
		<?php endforeach; ?>
		
	</div>

		<script> var base_url = '<?php echo base_url() ?>'; </script>
</div>
