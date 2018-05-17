<?php
	session_start();
	session_unset();

	session_destroy();
    $_SESSION['info_msg'] = "Volte logo para nos visitar!";
	header("Location: /");
?>