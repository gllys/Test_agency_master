<?php
session_start();
if(!isset($_SESSION['test'])) {
	echo 1;
	$_SESSION['test'] = date('Y-m-d H:i:s');
}
echo '<pre>';
print_r($_SESSION);
print_r($_COOKIE);
echo session_id();
echo '<br>';
echo session_name();
