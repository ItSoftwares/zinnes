<?
require_once "../database/conexao.php";

$data = date("Y-m-d H:i:s", time() + 50*24*60*60);

DBdelete("notificacao", "where data<'{$data}'");
?>