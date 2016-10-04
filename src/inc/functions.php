<?php

// (C) 2008 Kristopher Windsor

$page_title = "";
$contact = "<a href=\"contact.php\">contact us</a>";
$email = "<a href=\"mailto:kimwindsor@gmail.com\">Windsor Email</a>";
$pictures = '';

function pic ($filename, $large = false)
	{
	global $pictures;

	if ($large)
		$style = 'picture_large';
	else
		$style = 'picture';

	$pictures .= "<a href=\"img/$filename.jpg\"><img class=\"$style\" src=\"img/$filename.jpg\" alt=\"\"></a><br>\n";
	}
?>
