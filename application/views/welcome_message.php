<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<div id="container">

	<br>
	<h1 class='d-flex justify-content-around'>Synergasia</h1>
	<h4 class='d-flex justify-content-around'>Collaborate online</h4>
 	<?php //echo $this->session->userdata['username']; ?>

	<div class='jumbotron d-flex justify-content-around'>
		<?php echo anchor('app/create','Create Project', array('class'=>'btn btn-info')); ?>
		<?php echo anchor('app/dashboard','Dashboard', array('class'=>'btn btn-info')); ?>
	</div>


	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>
