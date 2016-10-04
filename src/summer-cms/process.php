<?php

include('../summer/summer.php');
start_nooutput('Processing...');

$success = false;
$result = 'No action specified!';
if (isset($_GET['action'])) $action = $_GET['action']; else $action = '';
$nextpage = 'summer-cms/admin.php';

function cookiemaster ($c, $v)
	{
	// set in this dir and the parent dir
	setcookie($c, $v, time() + 60 * 60 * 24 * 3650);
	setcookie($c, $v, time() + 60 * 60 * 24 * 3650, dirname(dirname($_SERVER['SCRIPT_NAME'])));
	}

function changepassword ()
	{
	global $success;

	if (isset($_POST['name'])) $name = $_POST['name']; else return 'No name was given!';
	if (isset($_POST['password'])) $password = $_POST['password']; else return 'No password was given!';

	if ($name == '' || $password == '')
		return 'The name or password is blank!';

	file_put_contents('data/admin-name.txt', $name);
	file_put_contents('data/admin-password.txt', sha1($password));

	cookiemaster('admin_name', $name);
	cookiemaster('admin_password', sha1($password));
	$success = true;

	return 'The password has been changed!';
	}

function content_create ()
	{
	global $nextpage, $success, $summer_cms_cats_more;

	if (!isset($_POST['cat']) || !isset($_POST['filename']) || !isset($_POST['title']) || !isset($_POST['content']) || !isset($_POST['protection']))
		return 'Some data is missing!';

	$cat = intval($_POST['cat']);
	$filename = validate_filename(stripslashes($_POST['filename']));
	$title = validate_text(stripslashes($_POST['title']));
	$content = stripslashes($_POST['content']);
	$protection = validate_text(stripslashes($_POST['protection']));

	if ($cat < 1 or $cat > count($summer_cms_cats_more))
		return 'The specified category is invalid!';

	if ($filename == '' || $title == '' || $content == '')
		return 'The filename, title, or content field is blank!';

	if (file_exists('content/' . $summer_cms_cats_more[$cat - 1] . "/$filename.txt"))
		return 'This content already exists!';

	file_put_contents('content/' . $summer_cms_cats_more[$cat - 1] . "/$filename.txt", $content);
	set_cms_pagedata($summer_cms_cats_more[$cat - 1], $filename, array($title, time(), $protection, 0));

	$success = true;
	$nextpage = "summer-cms/edit.php?cat=$cat&content=$filename";

	return 'Page created!';
	}

function content_delete ()
	{
	global $success, $summer_cms_cats_more;

	if (!isset($_POST['cat']) || !isset($_POST['filename']))
		return 'Some data is missing!';

	if (!isset($_POST['confirm']))
		return 'The action to delete this file was not confirmed!';

	$cat = intval($_POST['cat']);
	if ($cat < 1 or $cat > count($summer_cms_cats_more))
		return 'The specified category is invalid!';

	$filename = validate_filename(stripslashes($_POST['filename']));
	if ($filename == '')
		crash('The filename of the content specified is blank!');

	if (!file_exists('content/' . $summer_cms_cats_more[$cat - 1] . "/$filename.txt"))
		crash('The specified content does not exist!');


	if (!unlink('content/' . $summer_cms_cats_more[$cat - 1] . "/$filename.txt") || !unlink('data/content/' . $summer_cms_cats_more[$cat - 1] . "-$filename.txt"))
		return 'The content cannot be deleted!';

	$success = true;
	return 'The content has been deleted!';
	}

function content_edit ()
	{
	global $success, $summer_cms_cats_more;

	if (!isset($_POST['cat']) || !isset($_POST['filename_original']) || !isset($_POST['filename']) || !isset($_POST['title']) || !isset($_POST['content']) || !isset($_POST['protection']))
		return 'Some data is missing!';

	$cat = intval($_POST['cat']);
	$filename_original = validate_filename(stripslashes($_POST['filename_original']));
	$filename = validate_filename(stripslashes($_POST['filename']));
	$title = validate_text(stripslashes($_POST['title']));
	$content = stripslashes($_POST['content']);
	$protection = validate_text(stripslashes($_POST['protection']));

	if ($cat < 1 or $cat > count($summer_cms_cats_more))
		return 'The specified category is invalid!';

	if ($filename_original == '' || $filename == '' || $title == '' || $content == '')
		return 'The filename, title, or content field is blank!';

	if (!file_exists('content/' . $summer_cms_cats_more[$cat - 1] . "/$filename_original.txt"))
		return 'This content does not exist!';

	file_put_contents('content/' . $summer_cms_cats_more[$cat - 1] . "/$filename_original.txt", $content);

	$page_data = get_cms_pagedata($summer_cms_cats_more[$cat - 1], $filename_original);
	$page_data['title'] = $title;
	if (isset($_POST['updatetimestamp']))
		$page_data['time'] = time();
	$page_data['password'] = $protection;
	set_cms_pagedata($summer_cms_cats_more[$cat - 1], $filename_original, $page_data);

	// rename
	if ($filename != $filename_original)
		{
		if (!rename('content/' . $summer_cms_cats_more[$cat - 1] . "/$filename_original.txt", 'content/' . $summer_cms_cats_more[$cat - 1] . "/$filename.txt") ||
			!rename('data/content/' . $summer_cms_cats_more[$cat - 1] . "-$filename_original.txt", 'data/content/' . $summer_cms_cats_more[$cat - 1] . "-$filename.txt"))
			return 'The file cannot be renamed!';
		}

	$success = true;
	return 'Page changed!';
	}

function file_delete ()
	{
	global $success;

	if (isset($_POST['filename'])) $filename = file_validatefilename($_POST['filename']); else return 'No file was specified!';
	if (isset($_POST['confirm'])) $confirm = $_POST['confirm']; else return 'The action to delete this file was not confirmed!';

	if ($filename == '')
		return 'The filename is invalid!';

	if (!file_exists("../$filename"))
		return 'The file does not exist!';

	if (!unlink("../$filename"))
		return 'The file cannot be deleted!';

	$success = true;
	return 'The file was deleted!';
	}

function file_rename ()
	{
	global $success;

	if (isset($_POST['oldfilename'])) $old = file_validatefilename($_POST['oldfilename'], true); else return 'No file was specified!';
	if (isset($_POST['newfilename'])) $new = file_validatefilename($_POST['newfilename'], true); else return 'No new name was given!';

	if ($old == '' || $new == '')
		return 'The specified file or the new filename is invalid!';

	// ensure old exists, new doesn't exists, filenames are valid
	if (!file_exists("../$old"))
		return 'The file does not exist!';
	if (file_exists("../$new"))
		return 'A file with the new filename already exists!';

	if (!rename("../$old", "../$new"))
		return 'The file cannot be renamed!';

	$success = true;
	return 'The file has been renamed!';
	}

function file_upload ()
	{
	global $success;

	$r = $r_one = '';

	for ($i = 1; $i <= 3; $i ++)
		{
		if (isset($_POST['filename' . $i]))
			{
			$r_one = file_upload_one($i);
			if ($r_one != '')
				$r .= '<dt>' . validate_text($_POST['filename' . $i]) . '</dt><dd>' . $r_one . '</dd>';
			}
		}

	if ($r == '')
		return 'No files were uploaded because data was missing!';

	$success = true;
	return '<dl>' . $r . '</dl><form action="#"><div class="bbcodecontainer" id="bbcodecontainer"><input type="text" name="bbcode" id="bbcode"></div></form>';
	}

function file_upload_one ($filenumber)
	{
	// returns message null / error / success

	if (!isset($_POST['filename' . $filenumber]) || !isset($_FILES['file' . $filenumber]))
		return '';

	if ($_FILES['file' . $filenumber]['error'] == 4)
		return '';

	$filename = strtolower($_POST['filename' . $filenumber]);

	// remove file extension from file name to avoid scripting
	$whitelist = array('.7z', '.a', '.avi', '.bas', '.bi', '.bmp', '.chm', '.dll', '.doc', '.docx', '.exe', '.fla', '.flac', '.gif', '.jpg', '.mid', '.mod',
		'.mp3', '.mp4', '.mpg', '.mpeg', '.oga', '.ogg', '.ogv', '.pdb', '.pdf', '.png', '.prc', '.psd', '.rar', '.rc', '.swf', 'tar.bz2', '.tar.gz', '.txt', '.wmv', '.xls', '.xm', '.zip');
	$fileextension = '.txt';
	foreach ($whitelist as $item)
		{
		if (strpos($filename, $item) + strlen($item) == strlen($filename) && strpos($filename, $item) !== false)
			{
			$fileextension = $item;
			$filename = substr($filename, 0, strlen($filename) - strlen($item));
			}
		}

	validate_filename($filename);

	if ($_FILES['file' . $filenumber]['error'] != 0)
		return "Upload error #" . $_FILES['file' . $filenumber]['error'] . " has occured!";

	if ($fileextension == '.jpg' || $fileextension == '.gif' || $fileextension == '.png')
		$filer = "img/$filename$fileextension";
	else
		$filer = "files/$filename$fileextension";
	$download_destination = getcwd() . "/../$filer";

	if (file_exists($download_destination) && !isset($_POST['overwrite']))
		return "The file already exists!";

	if (move_uploaded_file($_FILES['file' . $filenumber]['tmp_name'], $download_destination))
		{
		chmod($download_destination, 0777);
		$r = "Success! <a href=\"$filer\">$filename</a> <a href=\"javascript:showbbcode('[url]http://" . $_SERVER['HTTP_HOST'] . '/' . $filer . '[/url]\')">[url]</a>';
		if ($fileextension == '.jpg' || $fileextension == '.png' || $fileextension == '.gif')
			$r .= " <a href=\"javascript:showbbcode('[img]http://" . $_SERVER['HTTP_HOST'] . '/' . $filer . '[/img]\')">[img]</a>';
		return $r . '!';
		}
	else
		return "This file cannot be uploaded!";
	}

function file_validatefilename ($filename)
	{
	if ($filename == '')
		return '';

	$fe = explode('/', $filename);
	if (count($fe) != 2)
		return '';

	if ($fe[0] != 'img' && $fe[0] != 'files')
		return '';

	$filename = $fe[0] . '/' . validate_filename($fe[1], true);
	if ($filename == '')
		return '';

	return $filename;
	}

function login ()
	{
	global $success;

	if (isset($_POST['name'])) $name = $_POST['name']; else return 'No name was given!';
	if (isset($_POST['password'])) $password = $_POST['password']; else return 'No password was given!';

	if ($name == '' || $password == '')
		return 'The name or password is blank!';

	if ($name != file_get_contents('data/admin-name.txt') || sha1($password) != file_get_contents('data/admin-password.txt'))
		return 'The name or password is incorrect!';

	cookiemaster('admin_name', $name);
	cookiemaster('admin_password', sha1($password));
	$success = true;

	return 'You are now logged in!';
	}

function logoff ()
	{
	global $success, $nextpage;

	if (!isset($_COOKIE['admin_name']) && !isset($_COOKIE['admin_password']))
		return 'You cannot logoff because you are not logged in!';

	cookiemaster('admin_name', '');
	cookiemaster('admin_password', '');
	$success = true;
	$nextpage = 'index.php';

	return 'You are now logged off!';
	}

function mainpages ()
	{
	global $success;

	if (!isset($_POST['mainpages']))
		return 'Some data is missing!';

	$mpf = array(); // final list
	$mp = explode("\n", $_POST['mainpages']); // input list

	foreach ($mp as $c => $mp_one)
		{
		if ($mp_one != '')
			{
			$mpd = explode('=', $mp_one);
			if (count($mpd) == 3)
				{
				$mpd = array(validate_text(trim(stripslashes($mpd[0]))), validate_text(trim(stripslashes($mpd[1]))), validate_text(trim(stripslashes($mpd[2]))));
				if ($mpd[0] == '' || $mpd[1] == '')
					{$mpd[1] = ''; $mpd[2] = ''; $mpd[3] = '';}
				$mpf[] = implode('`', $mpd);
				}
			}
		}

	if (count($mpf) == 0)
		return 'No pages were in the list!';

	file_put_contents('data/page-list.txt', implode(';', $mpf));

	$success = true;
	return 'The list of main pages has been changed!';
	}

function sitesettings ()
	{
	global $success;

	if (!isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['keywords']) || !isset($_POST['opening']) || !isset($_POST['closing']))
		return 'Some data is missing!';

	$d = array(stripslashes($_POST['title']), stripslashes($_POST['description']), stripslashes($_POST['keywords']),
		stripslashes($_POST['opening']), stripslashes($_POST['closing']));
	for ($i = 0; $i < 5; $i ++)
		$d[$i] = stripslashes($d[$i]);

	file_put_contents('data/site-title.txt', $d[0]);
	file_put_contents('data/description.txt', $d[1]);
	file_put_contents('data/keywords.txt', $d[2]);
	file_put_contents('data/page-start.txt', $d[3]);
	file_put_contents('data/page-finish.txt', $d[4]);

	$success = true;
	return 'Site settings updated!';
	}


if ($action == 'login' || $action == 'logoff')
	{
	switch ($action)
		{
		case 'login': $result = login(); break;
		case 'logoff': $result = logoff(); break;
		}
	}
else
	{
	empower();
	switch ($action)
		{
		case 'changepassword': $result = changepassword(); break;
		case 'content_create': $result = content_create(); break;
		case 'content_delete': $result = content_delete(); break;
		case 'content_edit': $result = content_edit(); break;
		case 'file_delete': $result = file_delete(); break;
		case 'file_rename': $result = file_rename(); break;
		case 'file_upload': $result = file_upload(); break;
		case 'mainpages': $result = mainpages(); break;
		case 'sitesettings': $result = sitesettings(); break;
		}
	}

start();

echo '<div class="summer_section">' . ucwords($action) . ': ' . $result . '<br>';

if ($success)
	echo '<a href="' . $nextpage . '">Continue</a></div>';
else
	echo '<a href="javascript:history.go(-1)">Go back</a></div>';

finish();

?>
