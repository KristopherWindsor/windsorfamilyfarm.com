<?php

include('../summer/summer.php');

start_nooutput('RSS');

if (isset($_GET['cat']))
	{
	$cat = $_GET['cat'];
	if (!array_search($cat, $summer_cms_cats))
		$cat = '';
	}
else
	$cat = '';

if ($cat == '')
	{
	// make a list of all the pages in all the cats
	$pages = array();
	foreach ($summer_cms_cats as $cat_one)
		{
		$pages_onecat = get_cms_pagelist($cat_one, true);
		foreach ($pages_onecat as $c => $p_one)
			$pages_onecat[$c] = $cat_one . '/' . $pages_onecat[$c];
		$pages += $pages_onecat;
		}
	krsort($pages);
	}
else
	{
	// get a list of the pages in a single cat
	$pages = get_cms_pagelist($cat, true);
	foreach ($pages as $c => $p_one)
		$pages[$c] = $cat . '/' . $pages[$c];
	}

if ($cat == '')
	$link_page = 'index.php';
else
	$link_page = 'summer-cms/' . $cat . '.php';

$title = htmlspecialchars(file_get_contents('data/site-title.txt'));
if ($cat != '')
	$title .= ' ' . ucwords($cat);

header('Content-type: application/rss+xml');

echo '<?xml version="1.0" encoding="ISO-8859-1" ?>' . "\n";
echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
echo '<channel>' . "\n";
echo '<title>' . $title . ' RSS</title>' . "\n";
echo '<link>http://' . dirname(dirname($_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . 'a')) . '/' . $link_page . '</link>' . "\n";
echo '<description>' . htmlspecialchars(file_get_contents('data/description.txt')) . "</description>\n<generator>The Summer Web Dev Suite RSS Feed Generator (C) Kristopher Windsor</generator>\n";
echo '<atom:link href="http://' . $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']) . '" rel="self" type="application/rss+xml" />' . "\n\n";

foreach ($pages as $one)
	{
	echo '<item>' . "\n";

	$one_data = explode('/', $one); // ie. blog/helloworld
	$cat = $one_data[0];
	$file = $one_data[1];

	$page_data = get_cms_pagedata($cat, $file);
	$title = htmlspecialchars($page_data['title']);
	$link = 'http://' . dirname($_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . 'a') .  "/$cat.php?page=$file";
	$description = htmlspecialchars(file_get_contents("content/$cat/$file.txt"));
	$category = ucwords($cat);
	$pubdate = date("r", $page_data['time']);

	echo "	<title>$title</title>\n";
	echo "	<link>$link</link>\n";
	echo "	<description>$description</description>\n";
	echo "	<pubDate>$pubdate</pubDate>\n";
	echo "	<category>$category</category>\n";
	echo "	<guid>$link</guid>\n";

	echo '</item>' . "\n\n";
	}

echo "</channel>\n</rss>";

?>
