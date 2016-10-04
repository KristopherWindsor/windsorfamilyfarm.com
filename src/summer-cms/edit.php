<?php

include('../summer/summer.php');

start('edit');

empower();

// POST takes priority over GET data

if (isset($_POST['content']))
	{
	$cd = explode('/', $_POST['content']);
	if (count($cd) < 2)
		crash('The content specified is invalid!');
	$cat = array_search($cd[0], $summer_cms_cats_more) + 1;
	$content = $cd[1];
	}
elseif (isset($_GET['cat']) && isset($_GET['content']))
	{
	$cat = $_GET['cat'];
	$content = $_GET['content'];
	}
else
	crash('The category or content was not specified!');

$cat = intval($cat);
if ($cat < 1 or $cat > count($summer_cms_cats_more))
	crash('An invalid category was specified!');

$content = validate_filename($content);
if ($content == '')
	crash('The filename of the content specified is blank!');

if (!file_exists('content/' . $summer_cms_cats_more[$cat - 1] . "/$content.txt"))
	crash('The specified content does not exist!');

$page_data = get_cms_pagedata($summer_cms_cats_more[$cat - 1], $content);

show_content_minimized($summer_cms_cats_more[$cat - 1], $content);

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
		<legend class="legend">Edit the <?php echo $content . ' content for the ' . $summer_cms_cats_more[$cat - 1]; ?> section</legend>
		<form action="summer-cms/process.php?action=content_edit" method="post" enctype="multipart/form-data">
			<div>
				<input type="hidden" name="cat" value="<?php echo $cat; ?>">
				<input type="hidden" name="filename_original" value="<?php echo $content; ?>">

				<label for="filename">Filename</label>
					<input type="text" id="filename" name="filename" value="<?php echo $content; ?>"><br>
				<label for="title">Title</label>
					<input type="text" id="title" name="title" value="<?php echo htmlspecialchars($page_data['title']); ?>"><br>

				<!-- for JS, not server processing -->
				<label for="select_img" title="Adds images and links to the content field below">Quick insert</label>
					<select name="select_img" id="select_img" onChange="quickinsert('img')">
						<option value="">- Select Image -<?php echo $img_f; ?></option>
					</select>
					<select name="select_url" id="select_url" onChange="quickinsert('url')">
						<option value="">- Select File -<?php echo $url_f; ?></option>
					</select><br>

				<label for="content">Content</label>
					<textarea name="content" id="content" rows="8" cols="48"><?php
						echo htmlspecialchars(file_get_contents('content/' . $summer_cms_cats_more[$cat - 1] . "/$content.txt")); ?></textarea><br>
				<label for="password">Protection</label>
					<input type="password" id="protection" name="protection" value="<?php echo htmlspecialchars($page_data['password']); ?>"><br>
				<label for="updatetimestamp">Update time?</label>
					<input type="checkbox" name="updatetimestamp" id="updatetimestamp"><br>
				<input type="submit" value="Change">
			</div>
		</form>
	</fieldset>
</div>

<div class="summer_section">
	<a href="summer-cms/delete.php?cat=<?php echo $cat; ?>&content=<?php echo $content; ?>">Delete this content</a>
</div>

<?php finish(); ?>
