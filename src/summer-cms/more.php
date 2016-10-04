<?php

include('../summer/summer.php');

start('more');

$page = get_requestedpage('more');

if ($page == '')
	{
	echo '<div class="summer_content"><ul class="summer_cms_pagelist">';
	// show page list
	$pages_sorted = get_cms_pagelist('more', false);
	if (count($pages_sorted))
		{
		foreach ($pages_sorted as $page_one)
			{
			$page_data = get_cms_pagedata('more', $page_one);
			echo '<li>';
			if ($page_data['password'] != '')
				echo '<img src="summer-img/lock.png"> ';
			echo '<a href="summer-cms/more.php?page=' . $page_one . '">' . htmlspecialchars($page_data['title']) . '</a> (' . format_date($page_data['time']) . ')</li>';
			}
		}
	else
		echo '<li>No content is available yet.</li>';
	echo '</ul></div>';
	}
else
	show_content('more', $page, true); // show specific page

finish();
?>
