<?php

include('../summer/summer.php');

start('create');

empower();

if (isset($_POST['cat'])) $cat = intval($_POST['cat']); else crash('No category was specified!');
if ($cat < 1 or $cat > count($summer_cms_cats_more))
	crash('An invalid category was specified!');

// editing tools: search for img in img/ and files in files/ and img/

$img = get_filelist('../img');
sort($img);
foreach ($img as $c => $img_one)
	$img[$c] = 'img/' . $img[$c];
if (count($img) >= 1)
	$img_f = '<option>' . implode('</option><option>', $img) . '</option>';
else
	$img_f = '';

$list = get_filelist('../files');
foreach ($list as $c => $list_one)
	$list[$c] = 'files/' . $list[$c];
$url = $list;
$list = get_filelist('../img');
foreach ($list as $c => $list_one)
	$list[$c] = 'img/' . $list[$c];
$url += $list;
sort($url);
if (count($url) >= 1)
	$url_f = '<option>' . implode('</option><option>', $url) . '</option>';
else
	$url_f = '';

?>

<div class="summer_section">
	<fieldset>
		<legend class="legend">Create new <?php echo $summer_cms_cats_more[$cat - 1]; ?> content</legend>
		<form action="summer-cms/process.php?action=content_create" method="post" enctype="multipart/form-data">
			<div>
				<input type="hidden" name="cat" value="<?php echo $cat; ?>">

				<label for="filename">Filename</label>
					<input type="text" id="filename" name="filename"><br>
				<label for="title">Title</label>
					<input type="text" id="title" name="title"><br>

				<!-- for JS, not server processing -->
				<label for="select_img" title="Adds images and links to the content field below">Quick insert</label>
					<select name="select_img" id="select_img" onChange="quickinsert('img')">
						<option value="">- Select Image -<?php echo $img_f; ?></option>
					</select>
					<select name="select_url" id="select_url" onChange="quickinsert('url')">
						<option value="">- Select File -<?php echo $url_f; ?></option>
					</select><br>

				<label for="content">Content</label>
					<textarea name="content" id="content" rows="8" cols="48"></textarea><br>
				<label for="password">Protection</label>
					<input type="protection" id="protection" name="protection"><br>
				<input type="submit" value="Create">
			</div>
		</form>
	</fieldset>
</div>

<?php finish(); ?>
