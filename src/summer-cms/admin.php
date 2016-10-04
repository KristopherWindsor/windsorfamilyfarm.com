<?php

include('../summer/summer.php');

start('admin');

empower();

$option_cats = $option_pages = $cmspagestats = '';
$option_none = '<option value="">- Select -</option>';

foreach ($summer_cms_cats_more as $cc => $cat_one)
	{
	$option_cats .= '<option value="' . ($cc + 1) . '">' . $cat_one . '</option>';

	$page_list = get_cms_pagelist($cat_one);
	if (count($page_list) > 0)
		{
		$option_pages .= '<option value="">' . $cat_one . '</option>';

		$cmspagestats .= '<li>' . ucwords($cat_one) . '<ul class="summer_cms_pagelist">';

		foreach ($page_list as $page_one)
			{
			$option_pages .= '<option value="' . "$cat_one/$page_one" . '">&mdash; ' . $page_one . '</option>';

			$page_data = get_cms_pagedata($cat_one, $page_one);
			$cmspagestats .= '<li>' . htmlspecialchars($page_data['title']) . ': ' . $page_data['hits'] . '</li>';
			}

		$cmspagestats .= '</ul></li>';
		}
	}

?>

<!-- main, blog, faq, more, news: create, edit, delete -->
<div class="summer_section">
	<fieldset>
		<legend class="legend" title="Here you can make a new blog post, etc.">Create new content</legend>
		<form action="summer-cms/create.php" method="post" enctype="multipart/form-data">
			<div>
				<label for="cat" title="New Main content is not visible on the site unless embedded in static pages">Category</label>
					<select name="cat" id="cat"><?php echo $option_none . $option_cats; ?></select><br>
				<input type="submit" value="Create">
			</div>
		</form>
	</fieldset>
</div>

<div class="summer_section">
	<fieldset>
		<legend class="legend" title="This lets you edit blog posts, etc., particularly Main content">Edit content</legend>
		<form action="summer-cms/edit.php" method="post" enctype="multipart/form-data">
			<div>
				<label for="content" title="Select a section of content (not a category)">Content</label>
					<select name="content" id="content"><?php echo $option_none . $option_pages; ?></select><br>
				<input type="submit" value="Edit">
			</div>
		</form>
	</fieldset>
</div>

<br>

<?php

$option_files = '';
$files = get_filelist('../files');
foreach ($files as $file_one)
	$option_files .= '<option>files/' . $file_one . '</option>';
$files = get_filelist('../img');
foreach ($files as $file_one)
	$option_files .= '<option>img/' . $file_one . '</option>';

?>

<!-- file manager -->
<div class="summer_section">
	<fieldset>
		<legend class="legend" title="You can upload files and pictures for hot-linking or personal use">Upload files</legend>
		<form action="summer-cms/process.php?action=file_upload" method="post" enctype="multipart/form-data">
			<div>
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo (ini_get('upload_max_filesize') * 1024 * 1024); ?>">
				<label for="file1">#1 File</label>
					<input type="file" id="file1" name="file1" onChange="changefilename('1')"><br>
					<label for="filename1">#1 Filename</label>
					<input type="text" id="filename1" name="filename1"><br>
				<label for="file2">#2 File</label>
					<input type="file" id="file2" name="file2" onChange="changefilename('2')"><br>
					<label for="filename2">#2 Filename</label>
					<input type="text" id="filename2" name="filename2"><br>
				<label for="file3">#3 File</label>
					<input type="file" id="file3" name="file3" onChange="changefilename('3')"><br>
					<label for="filename3">#3 Filename</label>
					<input type="text" id="filename3" name="filename3"><br>
				<label for="overwrite" title="This lets your uploaded files overwrite old files on the site">Overwrite?</label>
					<input type="checkbox" name="overwrite" id="overwrite"><br>
				<input type="submit" value="Upload">
			</div>
		</form>
	</fieldset>
</div>

<div class="summer_section">
	<fieldset>
		<legend class="legend">Rename a file</legend>
		<form action="summer-cms/process.php?action=file_rename" method="post" enctype="multipart/form-data">
			<div>
				<label for="file">File</label>
					<select name="oldfilename" id="file" onChange="changefilename('')"><?php echo $option_none . $option_files; ?></select><br>
				<label for="filename">New filename</label>
					<input type="text" id="filename" name="newfilename"><br>
				<input type="submit" value="Rename">
			</div>
		</form>
	</fieldset>
</div>

<div class="summer_section">
	<fieldset>
		<legend class="legend">Delete a file</legend>
		<form action="summer-cms/process.php?action=file_delete" method="post" enctype="multipart/form-data">
			<div>
				<label for="filedelete">File</label>
					<select name="filename" id="filedelete"><?php echo $option_none . $option_files; ?></select><br>
				<label for="confirm">Confirm?</label>
					<input type="checkbox" name="confirm" id="confirm"><br>
				<input type="submit" value="Delete">
			</div>
		</form>
	</fieldset>
</div>

<br>

<!-- stats (read only) -->
<div class="summer_section">
	Page hit statistics:
	<ul class="summer_pagelist"><?php echo $cmspagestats; ?></ul>
</div>

<br>

<!-- meta description and keywords, page opening and closing, site title -->
<div class="summer_section">
	<fieldset>
		<legend class="legend" title="This settings do not need to be changed much after initially set up">Site settings</legend>
		<form action="summer-cms/process.php?action=sitesettings" method="post" enctype="multipart/form-data">
			<div>
				<label for="title">Title</label>
					<input type="text" id="title" name="title" value="<?php echo htmlspecialchars(file_get_contents('data/site-title.txt')); ?>"><br>

				<label for="description">Description</label>
					<input type="text" id="description" name="description" value="<?php echo htmlspecialchars(file_get_contents('data/description.txt')); ?>"><br>
				<label for="keywords" title="These tags are used by search engines">Keywords</label>
					<input type="text" id="keywords" name="keywords" value="<?php echo htmlspecialchars(file_get_contents('data/keywords.txt')); ?>"><br>
				<label for="opening">Page opening</label>
					<textarea name="opening" id="opening" rows="8" cols="48"><?php echo htmlspecialchars(file_get_contents('data/page-start.txt')); ?></textarea><br>
				<label for="closing">Page closing</label>
					<textarea name="closing" id="closing" rows="8" cols="48"><?php echo htmlspecialchars(file_get_contents('data/page-finish.txt')); ?></textarea><br>
				<input type="submit" value="Change">
			</div>
		</form>
	</fieldset>
</div>

<br>

<?php

$mp = explode(';', file_get_contents('data/page-list.txt'));
$mainpages = '';
foreach ($mp as $mp_one)
	{
	$mpd = explode('`', $mp_one);
	$mainpages .= $mpd[0] . ' = ' . $mpd[1] . ' = ' . $mpd[2] . "\n";
	}

?>

<!-- change the list of main pages -->
<div class="summer_section">
	<fieldset>
		<legend class="legend" title="Changing this list does not change the actual files">Change the list of main pages</legend>
		<form action="summer-cms/process.php?action=mainpages" method="post" enctype="multipart/form-data">
			<div>
				<label for="mainpages" title="the format is: title = filename = description">Main pages</label>
					<textarea name="mainpages" id="mainpages" rows="8" cols="48"><?php echo $mainpages; ?></textarea><br>
				<input type="submit" value="Change">
			</div>
		</form>
	</fieldset>
</div>

<br>

<!-- change admin password -->
<div class="summer_section">
	<fieldset>
		<legend class="legend">Change the admin login info</legend>
		<form action="summer-cms/process.php?action=changepassword" method="post" enctype="multipart/form-data">
			<div>
				<label for="name">Name</label>
					<input type="text" name="name" id="name"><br>
				<label for="password">Password</label>
					<input type="password" name="password" id="password"><br>
				<input type="submit" value="Change">
			</div>
		</form>
	</fieldset>
</div>

<?php
if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'firefox') !== false)
	finish();
?>

<div class="summer_section">
	<a href="http://www.mozilla.com/firefox/"><img src="summer-img/get-firefox.png"></a>
</div>

<?php finish(); ?>
