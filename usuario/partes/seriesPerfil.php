<?
require "../../php/database/conexao.php";
// require "../php/classes/usuario.class.php";
require "../../php/classes/serie.class.php";

$id = $_GET['id'];
$sigo = isset($_GET['sigo'])?true:false;

$serie = new Serie();
$series = $serie->carregarPortifolio($id, $sigo);

if ($sigo) {
	foreach($series as $value) { ?>
	<li class="molde lista-grande favorito">
		<a href="/serie/<? echo $value['id']; ?>">
	        <img src="https://<? echo $_SERVER['SERVER_NAME']; ?>/img/projeto.jpg">
	        <div>
	            <h4><? echo substr($value['nome'], 0, 20); ?></h4>
	            <p>do autor <b><? echo $value['autor']; ?></b></p>
	            <div class="lado-a-lado">
	                <p><i class="fa fa-users"></i><b><? echo number_format($value['seguidores'], 0, "", "."); ?></b></p>
	                
	                <p><i class="fa fa-heart"></i><b><? echo number_format($value['gosteis'], 0, "", "."); ?></b></p>
	            </div>
	        </div>
		</a>
	</li>
	<? }
} else {
	foreach($series as $value) { ?>
	<li class="molde lista-grande projeto">
		<a href="/serie/<? echo $value['id'] ?>">
			<?
			if ($value['thumb_projeto']==null) {
			?>
            <img src="/img/sem-foto.png">
            <?
			} else {
            ?>
            <img src="/servidor/projetos/thumbs/<? echo $value['thumb_projeto']; ?>">
            <?
			}
            ?>
            <div>
                <h4><? echo substr($value['nome'], 0, 20) ?></h4>
                <p><b>#<? echo $value['capitulos'] ?></b> Capítulos</p>
                <div class="lado-a-lado">
                    <p><i class="fa fa-users"></i><b><? echo number_format($value['seguidores'], 0, "", "."); ?></b></p>
	                
	                <p><i class="fa fa-heart"></i><b><? echo number_format($value['gosteis'], 0, "", "."); ?></b></p>
                </div>
            </div>
		</a>
	</li>
	<? }
}
?>

<script type="text/javascript">
	var sigo = <? echo $sigo?1:0; ?>;
	var qtd = <? echo count($series); ?>;

	if (sigo==1) $("#favoritos h3 span").text("("+qtd+")");
	else $("#projetos h3 span").text("("+qtd+")");

	console.log(qtd);
</script>