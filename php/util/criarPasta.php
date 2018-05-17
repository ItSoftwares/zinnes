<pre>
<?
require "../database/conexao.php";
require "../util/listarArquivos.php";

// $pastas = listar(realpath(dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."titulos".DIRECTORY_SEPARATOR."comics".DIRECTORY_SEPARATOR))['nomes'];

// $nomes = $pastas;

// $titulos = DBselect('titulo', '', 'id');

// $temp = [];
// foreach ($titulos as $key => $value) {
// 	array_push($temp, intval($value['id']));
// }
// $titulos = $temp;

// $temp = [];
// foreach ($pastas as $key => $value) {
// 	if (!in_array($key, $titulos)) array_push($temp, $key);
// }

// $pastas = $temp;

// // var_dump($nomes);

// foreach ($pastas as $k => $value1) {
// 	$diretorio = realpath(dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."titulos".DIRECTORY_SEPARATOR."comics".DIRECTORY_SEPARATOR.$value1.DIRECTORY_SEPARATOR);
// 	echo $diretorio."<br>";
// 	rmdir($diretorio);	
// }

$vistos = DBselect("(select * from titulo where rascunho=0 order by visualizacoes DESC limit 7) t INNER JOIN projeto p ON t.id_projeto = p.id", "order by RAND()", "t.visualizacoes");

var_dump($vistos);

exit;
?>
</pre>