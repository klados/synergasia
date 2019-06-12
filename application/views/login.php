<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<div class="container">

			<br>
			<?php echo validation_errors('<div class="alert alert-danger alert-dismissible">','</div>'); ?>	
			<br>
			<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Login to the platform:</h3>
					</div>
					<div class="panel-body">
						<form method='post' role="form">

							<div class="form-group">
								<input type="email" name="email" id="email" class="form-control input-sm" placeholder="Email Address">
							</div>

							<div class="form-group">
								<input type="password" name="password" id="password" class="form-control input-sm" placeholder='Password'>
							</div>

							<input type="submit" value="Submit" name='login' class="btn btn-info btn-block">

						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
