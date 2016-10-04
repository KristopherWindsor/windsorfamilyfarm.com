<?php include('../summer/summer.php'); start('login'); ?>

<div class="summer_section">
	<fieldset>
		<legend class="legend">Login to the admin control panel</legend>
		<form action="summer-cms/process.php?action=login" method="post" enctype="multipart/form-data">
			<div>
				<label for="name">Name</label>
					<input type="text" name="name" id="name"><br>
				<label for="password">Password</label>
					<input type="password" name="password" id="password"><br>
				<input type="submit" value="Login">
			</div>
		</form>
	</fieldset>
</div>

<?php

if (!isset($_COOKIE['admin_name']) && !isset($_COOKIE['admin_password']))
	{finish(); die();}

?>

<div class="summer_section">
	<fieldset>
		<legend class="legend">Logoff</legend>
		 <form action="summer-cms/process.php?action=logoff" method="post" enctype="multipart/form-data">
			<div>
				<input type="submit" value="Logoff">
			</div>
		</form>
	</fieldset>
</div>

<?php finish(); ?>
