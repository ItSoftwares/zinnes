<pre>
<?
error_reporting(E_ALL);
require "../database/conexao.php";

// $array = [
// 	['nome'=>'Artes Marciais'],
// 	['nome'=>'Aventura'],
// 	['nome'=>'Bender'],
// 	['nome'=>'Doujinshi'],
// 	['nome'=>'Ecchi'],
// 	['nome'=>'Esporte'],
// 	['nome'=>'Fantasia'],
// 	['nome'=>'Ficção'],
// 	['nome'=>'Gastronomia'],
// 	['nome'=>'Gender'],
// 	['nome'=>'Harem'],
// 	['nome'=>'Historico'],
// 	['nome'=>'Horror'],
// 	['nome'=>'Historico'],
// 	['nome'=>'Horror'],
// 	['nome'=>'Isekai'],
// 	['nome'=>'Josei'],
// 	['nome'=>'Magia'],
// 	['nome'=>'Mecha'],
// 	['nome'=>'Manhua'],
// 	['nome'=>'Manhwa'],
// 	['nome'=>'Medicina'],
// 	['nome'=>'Militar'],
// 	['nome'=>'Misterio'],
// 	['nome'=>'Musical'],
// 	['nome'=>'One-Shot'],
// 	['nome'=>'Psicológico'],
// 	['nome'=>'Romance'],
// 	['nome'=>'Sci-fi'],
// 	['nome'=>'Seinen'],
// 	['nome'=>'Shoujo'],
// 	['nome'=>'Shoujo-Ai'],
// 	['nome'=>'Shounen'],
// 	['nome'=>'Shounen Ai'],
// 	['nome'=>'Sobrenatural'],
// 	['nome'=>'Super poderes'],
// 	['nome'=>'suspense'],
// 	['nome'=>'Tragédia'],
// 	['nome'=>'Vida Escolar'],
// 	['nome'=>'Webtoon'],
// 	['nome'=>'Yaoi'],
// 	['nome'=>'Yuri'],
// 	['nome'=>'Zumbi']
// ];

DBcreateVarios('genero', $array);

// require "../classes/usuario.class.php";
// require "../classes/serie.class.php";
// require "../classes/titulo.class.php";
// require "../vendor/autoload.php";

// $result = DBselect('avaliar_titulo', 'group by id_usuario, id_titulo having count(*) > 1', 'id_titulo, id_usuario, count(*)');
// var_dump($result); exit;

// $usuario = new usuario();

// $usuario->usuario = 'izac le ninja';
// $usuario->sexo = 0;

// var_dump($usuario->toArray());

// $result = $usuario->emailModerador();

// var_dump($result);

// require "../util/listarArquivos.php";

// $pastas = listar(realpath(dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."titulos".DIRECTORY_SEPARATOR."comics".DIRECTORY_SEPARATOR))['nomes'];

// $nomes = $pastas;

// $titulos = DBselect('titulo t', 'order by t.id DESC', 't.id, id_projeto');
// $projetos = DBselect('projeto p', 'order by p.id DESC', 'p.id');

// var_dump($titulos);
// $temp = [];
// foreach ($projetos as $key => $value) {
// 	array_push($temp, intval($value['id']));
// }
// $projetos = $temp;

// $temp = [];
// foreach ($titulos as $key => $value) {
// 	if (!in_array($value['id_projeto'], $projetos)) array_push($temp, $value['id_projeto']);
// }

// var_dump($temp); exit;

// $serie = new Serie();
// $serie->id = 26;
// $serie->tipo = 1;

// $serie->excluir();

// exit; ////////////////////////////////////////////

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

// var_dump($pastas); exit;

// $diretorio = realpath(dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."titulos".DIRECTORY_SEPARATOR."comics".DIRECTORY_SEPARATOR.'104'.DIRECTORY_SEPARATOR);
// echo $diretorio."<br>";
// rmdir_recursive($diretorio);	

// foreach ($pastas as $k => $value1) {
// 	$diretorio = realpath(dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."titulos".DIRECTORY_SEPARATOR."comics".DIRECTORY_SEPARATOR.$value1.DIRECTORY_SEPARATOR);
// 	echo $diretorio."<br>";
// 	rmdir_recursive($diretorio);	
// }

exit;
?>
</pre>