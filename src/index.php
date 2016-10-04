<?php

include('summer/summer.php');
include('inc/functions.php');

pic("home/wff");
pic("home/flag_mural");
pic("home/kid_w_chick");
pic("home/barn-rainbow");
pic("home/suess");
pic("home/goodbye-cow");
pic("home/roosters");
pic("home/pigs");

start('index');

if (isset($_GET['notfound']))
echo '<script>alert("Page not found; here is the home page instead.")</script>';

show_content('main', 'home-main');

show_news(3, true);

finish();

?>

