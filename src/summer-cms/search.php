<?php include('../summer/summer.php'); start('search'); ?>

<div class="summer_section">
	<fieldset>
		<legend class="legend"><img src="summer-img/google.gif" alt="Google search"></legend>
		<form method="get" action="http://www.google.com/search">
			<div>
				<input type="hidden" name="sitesearch" value="<?php echo $_SERVER['HTTP_HOST']; ?>">

				<label for="q">Search terms</label>
					<input type="text" name="q" id="q"><br>
				<input type="submit" value="Search">
			</div>
		</form>
	</fieldset>
</div>

<?php finish(); ?>
