<?php

// (C) 2008 Kristopher Windsor
// originally from iCMS
// the $mode option has been removed

function format_oneline ($t)
	{
	// used to prevent url and img titles from breaking HTML

	$t = str_replace("\n", '', $t);
	$t = str_replace("\r", '', $t);
	$t = htmlspecialchars($t);

	return $t;
	}

function format_iscombomaterial ($mode)
	{
	// enum value == array index + 1
	$enum_code = 1;
	$enum_email = 2;
	$enum_heading = 3;
	$enum_html = 4;
	$enum_img = 5;
	$enum_url = 6;

	$enum_text = 7;
	$enum_combo = 8; // text, heading, img, and url

	return ($mode == $enum_email || $mode == $enum_heading || $mode == $enum_img || $mode == $enum_url || $mode == $enum_text || $mode == $enum_combo);
	}

function format_content ($text)
	{
	$tags = array('code', 'email', 'h', 'html', 'img', 'url');

	// enum value == array index + 1
	$enum_code = 1;
	$enum_email = 2;
	$enum_heading = 3;
	$enum_html = 4;
	$enum_img = 5;
	$enum_url = 6;

	$enum_text = 7;
	$enum_combo = 8; // text, heading, img, and url

	$maxlength = 10000;
	if (strlen($text) > $maxlength)
		$text = substr($text, 0, $maxlength);

	$content = array();
	$content_type = array();

	// split tags (new method)
	do
		{
		$loc = strlen($text); // position in text
		$i = false; // tag found

		foreach ($tags as $c => $tag_one)
			{
			if (strpos($text, "[$tag_one]") !== false && strpos($text, "[/$tag_one]", strpos($text, "[$tag_one]")) !== false)
				{

				if (strpos($text, "[$tag_one]") < $loc)
					{
					$loc = strpos($text, "[$tag_one]");
					$i = $c;
					}
				}
			}

		if ($loc == 0)
			{
			$thetag = $tags[$i]; // $i is an index
			// there is a tag at the beginning of $text => split it
			$openspot = strpos($text, "[$thetag]");
			$closespot = strpos($text, "[/$thetag]", $openspot);
			$content[] = substr($text, $openspot + strlen($thetag) + 2, $closespot - $openspot - strlen($thetag) - 2);
			$content_type[] = $i + 1; // converts array index to enum value
			$text = substr($text, $closespot + strlen($thetag) + 3);
			}
		else
			{
			// there is some text at the beginning of $text that is not in tags
			$content[] = substr($text, 0, $loc);
			$content_type[] = $enum_text;
			$text = substr($text, $loc);
			}
		} while ($text != '');

	// format each part
	foreach ($content as $counter => $somecontent)
		{
		switch ($content_type[$counter])
			{
			case $enum_code:
				$content[$counter] = format_content_code($content[$counter]); break;
			case $enum_email:
				$content[$counter] = format_content_email($content[$counter]); break;
			case $enum_heading:
				$content[$counter] = format_content_heading($content[$counter]); break;
			case $enum_html:
				$content[$counter] = format_content_html($content[$counter]); break;
			case $enum_img:
				$content[$counter] = format_content_img($content[$counter]); break;
			case $enum_text:
				$content[$counter] = format_content_text($content[$counter]); break;
			case $enum_url:
				$content[$counter] = format_content_url($content[$counter]); break;
			}
		}

	// merge text, url, and img before applying per-line formats (lists)
	$counter_previous = false;
	foreach ($content as $counter => $somecontent)
		{
		if ($counter_previous !== false)
			{
			if (format_iscombomaterial($content_type[$counter_previous]) && format_iscombomaterial($content_type[$counter]))
				{
				$content[$counter] = $content[$counter_previous] . $content[$counter];
				$content_type[$counter] = $enum_combo;
				unset($content[$counter_previous]);
				unset($content_type[$counter_previous]);
				}
			else
				{
				if (format_iscombomaterial($content_type[$counter_previous]))
					$content_type[$counter_previous] = $enum_combo;
				}
			}
		$counter_previous = $counter;
		}
	// last element is not yet converted to combo, unless it merged with previous element
	if (format_iscombomaterial($content_type[$counter_previous]))
		$content_type[$counter_previous] = $enum_combo;

	// format text, img, url, and heading combo (lists)
	foreach ($content as $counter => $somecontent)
		{
		if ($content_type[$counter] != $enum_combo)
			continue;

		$content[$counter] = format_content_combo($content[$counter]);
		}

	// merge combo, html, and code
	return implode('', $content);
	}

function format_content_code ($text)
	{
	$text = htmlspecialchars($text);
	$text = str_replace("\n", "<br>", $text);
	$text = str_replace("\r", '', $text);

	return '<textarea class="form_textarea" rows="16" cols="80" readonly>' . $text . '</textarea>';
	}

function format_content_combo ($text)
	{
	if ($text == '')
		return '';

	$tl = explode('<br>', $text);
	$listdepth = 0;

	foreach ($tl as $counter => $aline)
		{
		$listdepth_previous = $listdepth;
		$pos = strpos($aline, ' ');
		if ($pos !== false && $pos < 5)
			{
			if (substr($aline, 0, $pos) == str_repeat('-', $pos))
				$listdepth = $pos;
			else
				$listdepth = 0;
			}
		else
			$listdepth = 0;

		if ($listdepth < $listdepth_previous)
			{
			$listdepth > 0 ? $listcode = '</ul></li>': $listcode = '</ul>';
			$tl[$counter - 1] .= str_repeat($listcode, $listdepth_previous - $listdepth);
			}

		if ($listdepth == 0)
			{
			if ($tl[$counter] == '')
				$tl[$counter] = '<br>';
			else
				{
				if (substr($tl[$counter], 0, 4) != '<h3>')
					$tl[$counter] = '<div>' . $tl[$counter] . '</div>';
				}
			}
		else
			$tl[$counter] = '<li>' . substr($tl[$counter], $listdepth + 1) . '</li>';

		if ($listdepth > $listdepth_previous)
			{
			$listdepth > 1 ? $listcode = '<li class="nestedlist"><ul>': $listcode = '<ul>';
			$tl[$counter] = str_repeat($listcode, $listdepth - $listdepth_previous) . $tl[$counter];
			}
		}

	$text = implode('', $tl);

	// close lists
	if ($listdepth > 1)
		$text .= str_repeat('</ul></li>', $listdepth - 1); // hopes the "-1" cures all
	if ($listdepth > 0)
		$text .= '</ul>';

	// remove extra breaks

	return $text;
	}

function format_content_email ($text)
	{
	$seppos = strpos($text, '|');
	if ($seppos === false)
		{$mail = $text; $title = 'email';}
	else
		{$mail = substr($text, $seppos + 1); $title = substr($text, 0, $seppos);}

	$mail = format_oneline($mail);
	$mail_data = explode('@', $mail);

	if (strlen($mail) < 8 || count($mail_data) != 2 || strlen($title) == 0)
		return 'invalid email address';

	return '<a href="javascript:showmail(\'' . $mail_data[0] . '\', \'' . $mail_data[1] . '\')">' . htmlspecialchars($title) . '</a>';
	}

function format_content_heading ($text)
	{
	$text = format_content_text($text);
	return "<h3>$text</h3>";
	}

function format_content_html ($text)
	{
	return $text; // no changes
	}

function format_content_img ($text)
	{
	$seppos = strpos($text, '|');
	if ($seppos === false)
		{$link = $text; $title = $text;}
	else
		{$link = substr($text, $seppos + 1, strlen($text) - $seppos - 1); $title = substr($text, 0, $seppos);}

	$link = htmlspecialchars($link);
	$title = format_oneline($title);

	if (strlen($link) == 0)
		return 'invalid image URL';

	return '<img class="page_image" src="' . $link . '" alt="' . $title . '">';
	}

function format_content_text ($text)
	{
	$text = htmlspecialchars($text);
	$text = str_replace("\n", '<br>', $text);
	$text = str_replace("\r", '', $text);

	$smilies = explode('	', file_get_contents('data/smilies.txt'));
	foreach ($smilies as $counter => $smileyset)
		{
		$smileydata = explode(' ', $smileyset);
		foreach ($smileydata as $smiley)
			{
			$text = str_ireplace($smiley, '<img class="smiley" src="summer-img/smilies/' . $counter . '.gif" alt="' . $smiley . '">', $text);
			}
		}

	while (strpos($text, "[b]") !== false && strpos($text, "[/b]", strpos($text, "[b]")) !== false)
		{
		$openspot = strpos($text, "[b]");
		$closespot = strpos($text, "[/b]", $openspot);

		$firstpart = substr($text, 0, $openspot);
		$midpart = substr($text, $openspot + 3, $closespot - $openspot - 3);
		$lastpart = substr($text, $closespot + 4, strlen($text) - $closespot - 4);

		$text = $firstpart . "<b>" . $midpart . "</b>" . $lastpart;
		}

	while (strpos($text, "[i]") !== false && strpos($text, "[/i]", strpos($text, "[i]")) !== false)
		{
		$openspot = strpos($text, "[i]");
		$closespot = strpos($text, "[/i]", $openspot);

		$firstpart = substr($text, 0, $openspot);
		$midpart = substr($text, $openspot + 3, $closespot - $openspot - 3);
		$lastpart = substr($text, $closespot + 4, strlen($text) - $closespot - 4);

		$text = $firstpart . "<i>" . $midpart . "</i>" . $lastpart;
		}

	return $text;
	}

function format_content_url ($text)
	{
	$seppos = strpos($text, '|');
	if ($seppos === false)
		{$link = $text; $title = $text;}
	else
		{$link = substr($text, $seppos + 1, strlen($text) - $seppos - 1); $title = substr($text, 0, $seppos);}

	$link = htmlspecialchars($link);
	if (strpos(strtolower($link), 'javascript:') === 0)
		$link = '';

	if (substr($title, 0, 5) == '[img]' && substr($title, -6, 6) == '[/img]')
		$title = format_content_img(substr($title, 5, strlen($title) - 11));
	else
		$title = format_oneline($title);

	if (strlen($link) == 0 || strlen($title) == 0)
		return 'invalid link';

	return '<a href="' . $link . '">' . $title . '</a>';
	}

?>
