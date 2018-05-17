<pre>
<?
require "../database/conexao.php";
require "../util/listarArquivos.php";

$pastas = listar(realpath(dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."titulos".DIRECTORY_SEPARATOR."comics".DIRECTORY_SEPARATOR))['nomes'];

$nomes = $pastas;

$titulos = DBselect('titulo', '', 'id');

$temp = [];
foreach ($titulos as $key => $value) {
	array_push($temp, intval($value['id']));
}
$titulos = $temp;

$temp = [];
foreach ($pastas as $key => $value) {
	if (!in_array($key, $titulos)) array_push($temp, $key);
}

$pastas = $temp;

var_dump($pastas);

$diretorio = realpath(dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."titulos".DIRECTORY_SEPARATOR."comics".DIRECTORY_SEPARATOR.'81'.DIRECTORY_SEPARATOR);
echo $diretorio."<br>";
rmdir_recursive($diretorio);	

// foreach ($pastas as $k => $value1) {
// 	$diretorio = realpath(dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."titulos".DIRECTORY_SEPARATOR."comics".DIRECTORY_SEPARATOR.$value1.DIRECTORY_SEPARATOR);
// 	echo $diretorio."<br>";
// 	rmdir_recursive($diretorio);	
// }

exit;
?>
</pre>