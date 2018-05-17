<?
session_start();

$get = $_GET;

foreach ($get as $key => $value) {
	$_SESSION[$key] = $value;
}
?>