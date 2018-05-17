<?
require "../php/database/conexao.php";
require "../php/classes/usuario.class.php";

session_start();

$time = $_GET['time'];
$senha = $_GET['hash'];
$id = $_GET['id'];

$dif = time()-($time+60*60);

if (time()>$time+60*60) {
	$_SESSION['info_msg'] = "Link expirou, solicite outro!";
	// header("Location: /"); exit;
}

$usuario = new Usuario();

if (!$usuario->carregar($id)) { 
	$_SESSION['info_msg'] = "Link inválido!";
	// echo "Link inválido!";
	header("Location: /"); exit;
}

if ($senha!=$usuario->senha) {
	$_SESSION['info_msg'] = "Link inválido!";
	// echo "Link inválido 2!";
	header("Location: /"); exit;
}

$menu_style = "transparente";

if ($_SESSION['tipo_usuario']>0) {
	$_SESSION['info_msg'] = "Sai da sua conta para poder recuperar a senha da conta solicitada!";
	header("Location: /"); exit;
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>ZINNES - Recuperar Senha</title>
		<base href="https://<? echo $_SERVER['SERVER_NAME']; ?>/">
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="img/logo.png" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/geral/recuperarSenha.css" media="(min-width: 1000px)">
        <link rel="stylesheet" type="text/css" href="css/geral/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" type="text/css" href="cssmobile/geral/recuperarSenha.css" media="(max-width: 999px)">
        <link rel="stylesheet" type="text/css" href="cssmobile/geral/geral.css" media="(max-width: 999px)">
        <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	</head>

	<body>
		<? include("../paginas/header.php") ?>

		<section id="recuperar">
			<? if ($usuario->foto_perfil=="" or $usuario->foto_perfil==null) {?>
            <img src="img/profile-default.png" id="foto-perfil">
            <?} else {?>
            <img src="servidor/thumbs-usuarios/<? echo $usuario->foto_perfil; ?>" id="foto-perfil">
            <?}?>
			
			<p>Digite sua nova senha abaixo!</p>

			<form class="">
				<div class="input normal">
					<input type="text" name="senha" placeholder="Nova Senha" autocomplete="off" minlength="8" autofocus />
				</div>

				<div class="input normal">
					<input type="text" id="repetir-senha-trocar" placeholder="Repita a nova senha" autocomplete="off" />
				</div>
				
				<p id="minutos" title="Tempo para link expirar"><? echo "59:59"; ?></p>

				<button class="botao cantos">Mudar Senha</button>
			</form>
		</section>
		
		<? include("../paginas/rodape.php") ?>
	</body>

	<script>
		var time = Math.abs(<? echo $dif; ?>);
		var usuario = <? echo json_encode($usuario->toArray()); ?>;
	</script>
	<script src="js/geral/recuperarSenha.js?<? echo time(); ?>" async></script>
</html>