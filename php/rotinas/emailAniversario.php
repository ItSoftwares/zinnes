<?
require_once "../database/conexao.php";
require_once "../classes/usuario.class.php";
require_once "../vendor/autoload.php";

$data = date("d/m");

$usuarios = DBselect("usuario", "where aniversario like '%{$data}%'", "email, nome");

if (count($usuarios)>0) {
	// var_dump($usuarios); exit;

	$mod = new Usuario();

	$mod->mensagem = "Não esquecemos do seu dia, feliz aniversário da equipe ZINNES!";

	$mod->emailModerador($usuarios);
}
?>