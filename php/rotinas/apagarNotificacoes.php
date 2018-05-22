<?
require_once "../database/conexao.php";

$data = date("Y-m-d H:i:s", time() + 100*24*60*60);

DBdelete("notificacao", "where data<'{$data}'");
DBdelete("notificacao_comentario", "where data<'{$data}'");
?>