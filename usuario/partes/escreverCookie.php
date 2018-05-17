<?
$get = $_GET;

foreach ($get as $key => $value) {
	setcookie($key, $value, time()+3600*24*365);

	var_dump($_GET);
}
?>