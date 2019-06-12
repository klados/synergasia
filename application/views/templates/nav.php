<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<?php echo anchor('welcome/index','Synergasia', array('class'=>'navbar-brand')); ?>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
				<?php echo anchor('welcome/index','Home', array('class'=>'nav-link')); ?>
      </li>
      <li class="nav-item">
				<?php echo anchor('app/dashboard','Dashboard', array('class'=>'nav-link')); ?>
      </li>
      <li class="nav-item">
				<?php echo anchor('app/create','New Project', array('class'=>'nav-link')); ?>
      </li>
      <li class="nav-item">
				<?php echo anchor('welcome/about','About', array('class'=>'nav-link')); ?>
      </li>
    </ul>

		<ul class='navbar-nav ml-auto'>
			<?php if($this->session->userdata('email')): ?>
      <li class="nav-item">
				<?php echo anchor('auth/logout','Logout', array('class'=>'nav-link')); ?>
      </li>
			<?php else: ?>
      <li class="nav-item">
				<?php echo anchor('auth/login','Login', array('class'=>'nav-link')); ?>
      </li>
      <li class="nav-item">
				<?php echo anchor('auth/register','Register', array('class'=>'nav-link')); ?>
      </li>
			<?php endif ?>
		</ul>

  </div>
</nav>

	<div class='container'>
	<?php if($this->session->flashdata('error')): ?>
		<div class='alert alert-danger'>
 		<?php echo $this->session->flashdata('error');?>
		</div>
	<?php endif ?>

	<div class='container'>
	<?php if($this->session->flashdata('success')): ?>
		<div class='alert alert-success'>
 		<?php echo $this->session->flashdata('success');?>
		</div>
	<?php endif ?>
	</div>
