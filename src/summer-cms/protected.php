<?php

include('../summer/summer.php');
start_nooutput('Protected');

if (!isset($_GET['content']))
	crash('The content was not specified!');

$content = explode(';', $_GET['content']);

if (count($content) < 2)
	crash('The content specified is invalid!');

$content[0] = validate_filename($content[0]);
$content[1] = validate_filename($content[1]);
if (!file_exists('content/' . $content[0] . '/' . $content[1] . '.txt'))
	crash('This content does not exist!');

$page_data = get_cms_pagedata($content[0], $content[1]);
if ($page_data['password'] != '')
	{
	empower(false);

	if (!isset($_GET['password']))
		{
		if (!$summer_isempowered)
			crash('The password was not specified!');
		}
	else
		{
		$password = $_GET['password'];
		if ($password != $page_data['password'])
			die('The password is incorrect! <a href="javascript:xml_get_protected(\'' . $content[0] . ';' . $content[1] . '\')">Click here</a> to try again.</div>');
		}
	}

echo format_content(file_get_contents('content/' . $content[0] . '/' . $content[1] . '.txt'));
?>
