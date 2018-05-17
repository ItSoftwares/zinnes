<pre>
<?
require "../database/conexao.php";
// require "../util/listarArquivos.php";

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

// var_dump($pastas);

// $diretorio = realpath(dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."titulos".DIRECTORY_SEPARATOR."comics".DIRECTORY_SEPARATOR.'81'.DIRECTORY_SEPARATOR);
// echo $diretorio."<br>";
// rmdir_recursive($diretorio);	

// foreach ($pastas as $k => $value1) {
// 	$diretorio = realpath(dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."titulos".DIRECTORY_SEPARATOR."comics".DIRECTORY_SEPARATOR.$value1.DIRECTORY_SEPARATOR);
// 	echo $diretorio."<br>";
// 	rmdir_recursive($diretorio);	
// }

// DBdelete('avaliar_titulo a1 INNER JOIN avaliar_titulo a2', 'where a1.data < a2.data and a1.id_usuario = a2.id_usuario and a1.id_titulo = a2.id_titulo');
// DBexecute('delete a1 from avaliar_titulo a1 INNER JOIN avaliar_titulo a2', 'where a1.data < a2.data and a1.id_usuario = a2.id_usuario and a1.id_titulo = a2.id_titulo');
// var_dump(DBexecute('rollback'));

$likes = DBselect('comentario_usuario c inner join usuario u on c.para = u.id', '', 'para, nickname');

var_dump($likes);

exit;
?>
</pre>