<?php

// here are some functions new to PHP 5

function file_put_contents ($file, $d)
	{
	$f = fopen($file, 'w');
	fputs($f, $d);
	fclose($f);
	}




?>
