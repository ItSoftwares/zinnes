<?
require "../php/database/conexao.php";
$busca = $_GET['busca'];

if ($busca=="") {
	echo "<p class='aviso'>Informe o termo a ser pesquisado!</p>";
	exit;
}

$comentarios = DBselect("comentario_titulo c INNER JOIN titulo t ON c.id_titulo = t.id INNER JOIN projeto p ON t.id_projeto = p.id INNER JOIN usuario u ON u.id = p.id_usuario", "where c.texto LIKE '%{$busca}%' order by data DESC limit 50", "c.*, p.tipo, t.nome as titulo, u.nickname");

if (!isset($comentarios)) $comentarios = [];

foreach ($comentarios as $key => $value) {
?>

<li class="comentario">
	<a href="/<? echo ($value['tipo']==1?"lerComic/":"lerNovel/").$value['id_titulo']; ?>">
		<h4><? echo $value['nickname']; ?>, <span><? echo $value['titulo']; ?></span></h4>

		<p><? echo $value['texto']; ?></p>

		<span><? echo date("d M, Y", strtotime($value['data'])); ?></span>
		<div class="clear"></div>
	</a>
</li>

<?
}

if (count($comentarios)==0) echo "<p class='aviso'>Nenhum resultado para '{$busca}'</p>";
?>