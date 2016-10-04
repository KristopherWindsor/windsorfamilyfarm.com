<?php

include('../summer/summer.php');

start('delete');

empower();

if (!isset($_GET['cat']) || !isset($_GET['content']))
	crash('The category or content was not specified!');

$cat = intval($_GET['cat']);
if ($cat < 1 or $cat > count($summer_cms_cats_more))
	crash('An invalid category was specified!');

$content = validate_filename($_GET['content']);
if ($content == '')
	crash('The filename of the content specified is blank!');

if (!file_exists('content/' . $summer_cms_cats_more[$cat - 1] . "/$content.txt"))
	crash('The specified content does not exist!');

show_content_minimized($summer_cms_cats_more[$cat - 1], $content);

?>

<div class="summer_section">
	<fieldset>
		<legend class="legend">Delete the <?php echo $content . ' content for the ' . $summer_cms_cats_more[$cat - 1]; ?> section</legend>
		<form action="summer-cms/process.php?action=content_delete" method="post" enctype="multipart/form-data">
			<div>
				<input type="hidden" name="cat" value="<?php echo $cat; ?>">
				<input type="hidden" name="filename" value="<?php echo $content; ?>">

				<label for="confirm">Confirm?</label>
					<input type="checkbox" name="confirm" id="confirm"><br>
				<input type="submit" value="Delete">
			</div>
		</form>
	</fieldset>
</div>

<?php finish(); ?>
