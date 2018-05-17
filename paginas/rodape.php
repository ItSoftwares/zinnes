<footer>
	<p>
		<span>E-mail: zinnes2018@zinnes.com.br</span>

		<span><? echo date("d/m/Y, H:i");  ?></span>
	</p>

	<ul>
		<li><a href="/pesquisa/comic">Comics</a></li>
		<li><a href="/pesquisa/novel">Novels</a></li>
		<li><a href="/dashboard">Publicar</a></li>
<!--		<li><a href="#">Login</a></li>-->
		<!-- <li><a href="#">Contato</a></li> -->
	</ul>

	<div>
		<div id="redes-sociais">
			<a href="http://fb.me/ZinnesHQ"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
			<a href="http://twitter.com/ZinnesHQ"><i class="fab fa-twitter" aria-hidden="true"></i></a>
			<a href="http://instagram.com/zinneshq/"><i class="fab fa-instagram" aria-hidden="true"></i></a>
		</div>

		<a href="https://www.instagram.com/itsoftwares/">© Plataforma desenvolvido por ItSoftwares.
<!--        <img src="img/itsoftwares.png">-->
        </a>
	</div>
</footer>
<? 
$temp = dirname(__DIR__)."/html/modals.html";
// echo $temp;
include($temp); ?>
<script type="text/javascript">
	var logado = <? echo $logado?1:0; ?>;
	var tipo_usuario = <? echo $_SESSION['tipo_usuario']; ?>;
    <? if (isset($_SESSION['logado'])) {?> var usuario = <? echo json_encode($usuario->toArray()); }?>;
    <? if (isset($qtd)) {?> var qtdNotificacoesNaoLidas = <? echo $qtd; }?>;

    if (tipo_usuario>=2) {
    	<? if (isset($_SESSION['logado'])) {?> var usuario_moderador = <? echo json_encode($usuario->toArray()); }?>;
    }
</script>
<script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
<script src="https://<? echo $_SERVER['SERVER_NAME'] ?>/js/geral/headerRodape.js?<? echo time() ?>"></script>
<script src="https://<? echo $_SERVER['SERVER_NAME'] ?>/js/geral/facebook.js?<? echo time() ?>"></script>
<script src="https://<? echo $_SERVER['SERVER_NAME'] ?>/js/geral/google.js?<? echo time(); ?>"></script>
<script type="text/javascript">
    <? if ($info_msg!="") {?> chamarPopupInfo("<? echo $info_msg; ?>"); //console.log("<? echo $info_msg ?>"); <?}?>;
    <? if ($conf_msg!="") {?> chamarPopupConf("<? echo $conf_msg; ?>"); //console.log("<? echo $conf_msg ?>"); <?}?>;
    <? if ($erro_msg!="") {?> chamarPopupErro("<? echo $erro_msg; ?>"); //console.log("<? echo $erro_msg ?>"); <?}?>;
</script>