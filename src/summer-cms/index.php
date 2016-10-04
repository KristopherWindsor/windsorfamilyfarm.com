<?php // page is similar to the Nav, but also contains more (hidden) links

include('../summer/summer.php');

start('sitemap');
echo '<div class="summer_section"><ul class="summer_pagelist">';

// main links
$pages = explode(';', file_get_contents('data/page-list.txt'));
echo '<li>Main<ul class="summer_cms_pagelist">';
foreach ($pages as $page_one)
	{
	$page_data = explode('`', $page_one);
	echo '<li><a href="' . $page_data[1] . '.php">' . $page_data[0] . '</a></li>';
	}
echo '</ul></li>';

// cms links
foreach ($summer_cms_cats as $cat_one)
	{
	$pages = get_cms_pagelist($cat_one, ($cat_one == 'blog' || $cat_one == 'news'));

	if (count($pages))
		{
		echo '<li><a href="summer-cms/' . $cat_one . '.php">' . ucwords($cat_one) . '</a><ul class="summer_cms_pagelist">';
		foreach ($pages as $page_one)
			{
			$page_data = get_cms_pagedata($cat_one, $page_one);
			echo '<li>';
			if ($page_data['password'] != '')
				echo '<img src="summer-img/lock.png"> ';
			echo '<a href="summer-cms/' . $cat_one . '.php?page=' . $page_one . '">' . htmlspecialchars($page_data['title']) . '</a></li>';
			}
		echo '</ul></li>';
		}
	}
?>

<li>Extras<ul class="summer_cms_pagelist">
	<li><a href="summer-cms/rss.php">RSS</a></li>
	<li><a href="summer-cms/search.php">Search</a></li>
	<li><a href="summer-cms/index.php">Sitemap</a></li>
</ul></li>

<li>Control Panel<ul class="summer_cms_pagelist">
	<li><img src="summer-img/lock.png"> <a href="summer-cms/admin.php">Admin</a></li>
	<li><img src="summer-img/lock.png"> <a href="summer-cms/login.php">Login</a></li>
</ul></li>

<?php echo '</ul></div>'; finish(); ?>
