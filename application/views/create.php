<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<div class='container'>

<br>
		<?php echo validation_errors('<div class="alert alert-danger alert-dismissible">','</div>'); ?>	
<form method='post'>

	<div class='form-group'>
		<label for='title'>Name the project:</label>
		<input type='text' name='name' class='form-control' id='title' placeholder='Give a name to the project'>
	</div>
	
	<div class='form-group'>
		<label for='desc'>Sort Description:</label>
		<textarea class='form-control' id='desc' name='desc' placeholder='I will tell you a story about...'></textarea>
	</div>

	<div class='form-group'>
		<label for='cat'>Select field of study:</label>
  	<select class="form-control" id="cat" name='category'>
  		<option>Medicine and health</option>
  		<option>Engineering and technology</option>
  		<option>Mathematics</option>
			<option>Computer Science</option>
			<option>Physics</option>
			<option>Space sciences</option>
			<option>Earth sciences</option>
			<option>Chemistry</option>
			<option>Biology</option>
			<option>Sociology</option>
			<option>Psychology</option>
			<option>Political science</option>
			<option>Law</option>
			<option>Human geography</option>
			<option>Economics</option>
			<option>Arts</option>
			<option>History</option>
			<option>Languages and literature</option>
			<option>Philosophy</option>
			<option>Theology</option>
  		<option>Other</option>
  	</select>
	</div>

	<!-- <div id='otherLocationOption'></div> -->

	<button type='submit' class='btn btn-primary'>Create</button>

</form>
</div>
