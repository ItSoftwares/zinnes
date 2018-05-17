<?
require_once "../database/conexao.php";
require_once "../classes/titulo.class.php";
// exit;

$data = date("Y-m-d", time());

$titulos = DBselect("titulo", "where rascunho=1 and data_lancamento LIKE '%{$data}%'", "id, id_projeto");

foreach ($titulos as $key => $value) {
	$titulo = new Titulo($value);
	$titulo->publicar();
}

DBupdate("titulo", array('rascunho' => 0), "where rascunho=1 and data_lancamento LIKE '%{$data}%'");

$hoje = date("d/m/Y");
if (count($titulos)>0) {
	DBcreate("log_moderador", array('id_moderador'=>1, 'id_usuario'=>1, 'descricao'=>"Hoje {$hoje} foram publicado ".count($titulos)." capítulos automaticamente!"));
}
?>