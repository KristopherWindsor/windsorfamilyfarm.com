<?php

// Summer Web Dev Suite (C) 2008 Kristopher Windsor

if (version_compare(PHP_VERSION, '4.0.0') == -1)
	die('Bad php version!');

if (version_compare(PHP_VERSION, '5.0.0') == -1)
	include('php4.php');

// globals
$summer_iscmspage = false;
$summer_isempowered = false;
$summer_isstarted = 0; // 0 = no, 1 = no output, 2 = yes
$summer_pageid = '';
$summer_pagetitle = '';
$summer_sitetitle = '';

$summer_cms_cats = array('blog', 'faq', 'news', 'more');
$summer_cms_cats_more = array('main', 'blog', 'faq', 'news', 'more');

function start ($file = '')
	{
	global $summer_cms_cats, $summer_iscmspage, $summer_isstarted, $summer_pageid, $summer_pagetitle, $summer_sitetitle;

	start_nooutput($file);
	$summer_isstarted = 2;

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . "\n" . '<html>' . "\n" . '<head>
	<title>' . $summer_sitetitle . ' ' . $summer_pagetitle . '</title>
	';

	// always stay in the main directory
	if ($summer_iscmspage)
		echo '<base href="https://' . dirname(dirname($_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . 'a')) . '/">' . "\n\t";

	// set RSS cat filter
	$cat = '';
	if ($summer_iscmspage)
		{
		if (array_search($summer_pageid, $summer_cms_cats) !== false)
			$cat = '?cat=' . $summer_pageid;
		}

	echo '<link href="style.css" rel="stylesheet" type="text/css">
	<link href="summer-cms/rss.php' . $cat . '" rel="alternate" type="application/rss+xml">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<meta name="description" content="' . htmlspecialchars(file_get_contents('data/description.txt')) . '">
	<meta name="keywords" content="' . htmlspecialchars(file_get_contents('data/keywords.txt')) . '">
	<script src="summer-cms/script.js" type="text/javascript"></script>';

	echo "\n</head>\n<!-- Summer Web Dev project (C) 2008 Kristopher Windsor -->\n<body>\n	<div class=\"summer_main\">\n";

	include('data/page-start.txt');
	}

function start_nooutput ($file)
	{
	// set the CWD and page title

	global $summer_iscmspage, $summer_isstarted, $summer_pageid, $summer_pagetitle, $summer_sitetitle;

	if ($summer_isstarted >= 1) return;
	$summer_isstarted = 1;

	$summer_pageid = $file;

	if (basename(getcwd()) == 'summer-cms')
		$summer_iscmspage = true;

	// always stay in the 'summer' directory
	if ($summer_iscmspage)
		chdir('..');
	chdir('summer');

	// set title
	if ($summer_iscmspage)
		{
		if (get_requestedpage($file) == '')
			{
			$title = $file;
			$title[0] = ucwords($title[0]);
			}
		else
			{
			$page_data = get_cms_pagedata($file, get_requestedpage($file));
			$title = ucwords($page_data['title']);
			}
		}
	else
		{
		$title = 'Untitled';
		$pages = explode(';', file_get_contents('data/page-list.txt'));
		if ($summer_iscmspage)
			$file = 'summer-cms/' . $file;
		foreach ($pages as $page_one)
			{
			$page_data = explode('`', $page_one);
			if ($file == $page_data[1])
				$title = $page_data[0];
			}
		}
	$summer_pagetitle = $title;
	$summer_sitetitle = htmlspecialchars(file_get_contents('data/site-title.txt'));
	}

function empower ($required = true)
	{
	global $summer_isempowered, $summer_isstarted;

	if (isset($_COOKIE['admin_name']) && isset($_COOKIE['admin_password']))
		{
		if ($_COOKIE['admin_name'] == file_get_contents('data/admin-name.txt') && $_COOKIE['admin_password'] == file_get_contents('data/admin-password.txt'))
			{
			$summer_isempowered = true;
			return;
			}
		}

	if (!$required)
		return;

	if ($summer_isstarted < 2)
		start('Untitled');

	echo '<div class="summer_section">You need to login before using this page! Click <a href="summer-cms/login.php">here</a> to login.</div> ';
	finish();
	die();
	}

function finish ()
	{
	include('data/page-finish.txt');
	echo "\n	</div>\n\t" . '<div class="summer_dropdown" id="dropdown" onMouseOver="nav_on()" onMouseOut="nav_off()"></div>' . "\n</body>\n</html>";
	die();
	}

function crash ($e)
	{
	global $summer_isstarted;

	if ($summer_isstarted < 2)
		start('Untitled');

	echo '<div class="summer_section">' . $e . '<br><a href="javascript:history.go(-1)">Go back</a></div>';
	finish();
	}

// format

include('formattext.php');

function format_date ($time)
	{
	return date('l, dS F, Y', $time);
	}

function validate_text ($text)
	{
	// validates text such as a title (will be stored to file after validation, then shown with htmlspecialchars()))
	// can't allow ; or `
	// don't allow newlines, tabs, unknown characters
	$a = 'abcdefghijklmnopqrstuvwxyz1234567890!@#$%^&*()-_=+[]{}|~" ?/\'<>,.:';

	for ($i = 0; $i < strlen($text); $i ++)
		{
		if (strpos($a, strtolower($text[$i])) === false)
			$text[$i] = '?';
		}

	return $text;
	}

function validate_filename ($text, $allowext = false)
	{
	// validates a filename
	// can't allow / or .
	// doesn't allow every valid character
	$a = 'abcdefghijklmnopqrstuvwxyz1234567890-_ ';
	$text = strtolower($text);

	if ($allowext)
		{
		$pi = pathinfo($text);
		if (!isset($pi['filename'])) $pi['filename'] = '';
		if (!isset($pi['extension'])) $pi['extension'] = '';
		// recursive
		$text = validate_filename($pi['filename']);
		$ext = validate_filename($pi['extension']);
		if ($text == '' && $ext == '')
			return '';
		return "$text.$ext";
		}

	for ($i = 0; $i < strlen($text); $i ++)
		{
		if (strpos($a, $text[$i]) === false)
			$text[$i] = '-';
		}

	return $text;
	}

// get

function get_cms_pagedata ($cat, $page)
	{
	$data = explode(';', file_get_contents('data/content/' . $cat . '-' . $page . '.txt'));
	while (count($data) < 4)
		$data[] = '';
	return array('title' => $data[0], 'time' => (int) $data[1], 'password' => $data[2], 'hits' => (int) $data[3]);
	}

function get_cms_pagelist ($cat, $timesort = -1)
	{
	$pages = get_filelist("content/$cat", '.txt');
	$pages_sorted = array();

	if (count($pages) == 0)
		return $pages_sorted;

	if ($timesort !== true && $timesort !== false)
		$timesort = ($cat == 'blog' or $cat == 'news');

	if ($timesort)
		{
		foreach ($pages as $page_one)
			{
			$page_data = get_cms_pagedata($cat, $page_one);
			$pages_sorted[$page_data['time']] = $page_one;
			}
		krsort($pages_sorted, SORT_NUMERIC);
		}
	else
		{
		foreach ($pages as $page_one)
			{
			$page_data = get_cms_pagedata($cat, $page_one);
			$pages_sorted[$page_data['title']] = $page_one;
			}
		ksort($pages_sorted, SORT_STRING);
		}

	return $pages_sorted;
	}

function get_filelist ($path, $ext = '')
	{
	if (!is_dir($path)) return array();

	$list = array();

	$dir = opendir($path);
	while(false !== ($file = readdir($dir)))
		{
		if ($file != '.' && $file != '..')
			{
			if (!is_dir($file) && ($ext == '' || substr($file, -strlen($ext)) == $ext))
				$list[] = substr($file, 0, strlen($file) - strlen($ext));
			}
		}
	closedir($dir);

	return $list;
	}

function get_folderlist ($path)
	{
	if (!is_dir($path))
		crash('Bad path for get_filelist: ' . $path);

	$list[] = array();

	$dir = opendir($path);
	while(false !== ($file = readdir($dir)))
		{
		if ($file != '.' && $file != '..')
			{
			if (is_dir($file))
				$list[] = file;
			}
		}
	closedir($dir);

	return $list;
	}

function get_requestedpage ($cat)
	{
	// verifies page exists for the given category
	$page = '';
	if (isset($_GET['page']))
		{
		$page = validate_filename($_GET['page']);
		if (!file_exists('content/' . $cat . '/' . $page . '.txt'))
			$page = '';
		}
	return $page;
	}

// set

function set_cms_pagedata ($cat, $page, $data)
	{
	while (count($data) < 4)
		$data[] = '';
	file_put_contents('data/content/' . $cat . '-' . $page . '.txt', implode(';', $data));
	}

// show

function show_content ($cat, $page, $showtime = false)
	{
	if (!file_exists("content/$cat/$page.txt"))
		{echo '<div class="summer_section error">Error! This content (' . "$cat/$page" . ') does not exist! Please inform the administrator!</div> '; return;}

	$page_data = get_cms_pagedata($cat, $page);

	if ($page_data['password'] == '')
		{
		if ($showtime)
			echo '<div class="summer_time">' . format_date($page_data['time']) . '</div> ';
		echo '<div class="summer_content">' . format_content(file_get_contents("content/$cat/$page.txt")) . '</div> ';
		$page_data['hits'] ++;
		set_cms_pagedata($cat, $page, $page_data);
		return;
		}

	// make a form to let the user enter a password
	echo '<div class="summer_content" id="content;' . "$cat;$page" . '">This content is password protected!' .
		'<a href="javascript:xml_get_protected(\'' . "$cat;$page" . '\')">Click here</a> to load the content.</div> ';
	}

function show_content_minimized ($cat, $page, $showtime = false)
	{
	global $summer_isempowered;

	if (!file_exists("content/$cat/$page.txt"))
		{echo '<div class="summer_content error">Error! This content (' . "$cat/$page" . ') does not exist! Please inform the administrator!</div> '; return;}

	$page_data = get_cms_pagedata($cat, $page);

	// prompt for password unless user is a verified admin
	if ($page_data['password'] != '' && $summer_isempowered === false)
		{
		show_content($cat, $page, $showtime = false);
		return;
		}

	if ($showtime)
		echo '<div class="summer_time">' . format_date($page_data['time']) . '</div> ';

	// make a form to let the user enter a password
	echo '<div class="summer_content" id="content;' . "$cat;$page" . '"><a href="javascript:xml_get_protected_nopass(\'' . "$cat;$page" . '\')">Click here</a> to show this content.</div> ';
	}

function show_nav ()
	{
	global $summer_cms_cats, $summer_isempowered;

	$nav = array();
	$dropdown = array();
	$title = array();

	// part 1: static pages
	$pages = explode(';', file_get_contents('data/page-list.txt'));
	foreach ($pages as $page_one)
		{
		$page_data = explode('`', $page_one);
		$t = '';

		if ($page_data[0] == '' || $page_data[1] == '')
			$nav[] = '';
		else
			{
			$nav[] = '<a href="' . $page_data[1] . '.php">' . $page_data[0] . '</a>';
			if ($page_data[2] != '')
				$t = $page_data[2];
			}

		$title[] = $t;
		}

	// part 2: links to dynamic content
	foreach ($summer_cms_cats as $cat_one)
		{
		$pages = get_cms_pagelist($cat_one);
		if (count($pages) > 0)
			{
			$dd = ''; $i = 0;
			foreach ($pages as $page_one)
				{
				$i ++;
				if ($i < 5)
					{
					$page_data = get_cms_pagedata($cat_one, $page_one);
					if ($page_data['password'] == '')
						$dd .= '<li><a href="summer-cms/' . $cat_one . '.php?page=' . $page_one . '">' . htmlspecialchars($page_data['title']) . '</a></li>';
					else
						$i --;
					}
				}

			// if there are some pages, but they are all password protected, show the link without the dropdown
			if ($i > 0)
				$dropdown[count($nav)] = $dd;

			$nav[] = '<a href="summer-cms/' . $cat_one . '.php">' . ucwords($cat_one) . '</a>';
			$title[] = ''; // title gets in the way of the dropdown
			}
		}

	$nav[] = '<a href="summer-cms/search.php">Search</a>'; $title[] = 'Google search tool';
	$nav[] = '<a href="summer-cms/index.php">Sitemap</a>'; $title[] = 'List of pages on this site';

	empower(false);
	if ($summer_isempowered)
		{$nav[] = '<a href="summer-cms/admin.php">Admin</a>'; $title[] = 'Admin control panel';}

	echo '<ul class="summer_nav">';
	foreach ($nav as $i => $nav_one)
		{
		$t = '';
		if ($title[$i] != '')
			$t = ' title="' . htmlspecialchars($title[$i]) . '"';

		if (isset($dropdown[$i]))
			echo '<li' . $t . ' id="li' . ($i + 1) . '" onMouseOver="nav_show(\'' . ($i + 1) . '\', \'' . htmlspecialchars('<ul>' . $dropdown[$i] . '</ul>') . '\')" onMouseOut="nav_off()">' .
				$nav_one . '</li> ';
		else
			{
			if ($nav_one == '')
				echo '<li class="summer_nav_spacer"></li> ';
			else
				echo '<li' . $t . '>' . $nav_one . '</li> ';
			}
		}

	// super hax
	echo '<li class="summer_nav_spacer"></li><li style="background-color: transparent">
<a href="//www.facebook.com/pages/Windsor-Family-Farm/108250365877006">
<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FWindsor-Family-Farm%2F108250365877006&amp;layout=button_count&amp;show_faces=true&amp;width=116&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:116px; height:21px;" allowTransparency="true"></iframe>
</a></li>';

	echo '</ul>' . "\n";
	}

function show_news ($showtotal, $fullpost = false)
	{
	echo '<div class="summer_news">';

	$pages = get_cms_pagelist('news', true);
	$shown = 0;

	if (count($pages))
		{
		foreach ($pages as $page_one)
			{
			if ($shown < $showtotal)
				{
				$page_data = get_cms_pagedata('news', $page_one);
				$url = 'summer-cms/news.php?page=' . $page_one;

				if ($page_data['password'] == '')
					{
					$shown ++;
					echo '<h3 class="summer_news_heading"><a href="' . $url . '">' . $page_data['title'] . '</a></h3> ';
					echo '<div class="summer_time">' . format_date($page_data['time']) . '</div> ';
					echo '<div class="summer_news_content">'; // cannot be a generic div because it holds div children

					$content = file_get_contents('content/news/' . $page_one . '.txt');
					if ($content > 128 && !$fullpost)
						$content = substr($content, 0, 128);
					echo format_content($content);
					}

				echo '</div> ';
				}
			}
		}

	echo '</div> ';
	}

function show_rss ()
	{
	global $summer_cms_cats, $summer_iscmspage, $summer_pageid;

	$cat = '';

	if ($summer_iscmspage)
		{
		if (array_search($summer_pageid, $summer_cms_cats) !== false)
			$cat = $summer_pageid;
		}

	echo '<a href="summer-cms/rss.php?cat=' . $cat . '"><img src="summer-img/rss.png" class="summer_rss" alt="RSS"></a>';
	}
?>
