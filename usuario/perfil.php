<?
require "../php/database/conexao.php";
require "../php/classes/usuario.class.php";
require "../php/classes/titulo.class.php";

date_default_timezone_set('America/Sao_Paulo');

session_start();

$menu_style = "transparente";

if ($_GET['nickname']=="" and !isset($_SESSION['logado'])) {
    $_SESSION['erro_msg'] = "Faça login ou cadastre-se para acessar sua conta!";
    session_write_close();
    header("location: ");
}

if ($_GET['nickname']!="") {
    $nickname = $_GET['nickname'];
    $usuario = new Usuario();
    

    if (!$usuario->carregar(null, $nickname)) {
        $_SESSION['erro_msg'] = "Usuario não existe!";
        header("Location: /perfil");
        exit;
    }

    $tempUsuario = $usuario->toArray();
}
$seguindo = false;
if ($_GET['seguindo']!="") {
	$seguindo=true;
}
?>
<!DOCTYPE HTML>
<html>

<head>
	<title><? echo isset($nickname)?$nickname:"Perfil"; ?> | ZINNES</title>
	<link rel="icon" type="image/png" href="img/logo.png" />
	<base href="https://<? echo $_SERVER['SERVER_NAME']; ?>/">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/usuario/perfil.css?<? echo time() ?>" media="(min-width: 1000px)">
	<link rel="stylesheet" type="text/css" href="css/geral/comentarios.css" media="(min-width: 1000px)">
	<link rel="stylesheet" type="text/css" href="css/geral/geral.css" media="(min-width: 1000px)">
	<link rel="stylesheet" type="text/css" href="cssmobile/usuario/perfil.css?<? echo time() ?>" media="(max-width: 999px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/geral/geral.css" media="(max-width: 999px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/geral/comentarios.css" media="(max-width: 999px)">
	<link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>
	<? 
    $usuario = new Usuario();
    include("../paginas/header.php");
    $usuario_perfil = new Usuario($usuario->toArray());
    if (isset($_SESSION['logado'])) $id_usuario_1 = $usuario->id;

    $editar = false;
    if ((isset($tempUsuario) and $tempUsuario['id']==$usuario->id) || $_SESSION['tipo_usuario']>1 || $_GET['nickname']=="") {
        $editar = true;
    }
    if ($_GET['nickname']!="" and (!isset($_SESSION['usuario']) or $_GET['nickname']!=$usuario->nickname)) {
        $usuario_perfil = new Usuario($tempUsuario);
    }

    $qtd_gosteis = DBselect("avaliar_titulo", "where id_titulo in (select id from titulo where id_projeto in (select id from projeto where id_usuario = {$usuario_perfil->id}))", "count(id_usuario) as qtd")[0]['qtd'];

    $qtd_seguidores = DBselect("seguir_projeto", "where id_projeto in (select id from projeto where id_usuario={$usuario_perfil->id})", "count(id_usuario) as qtd")[0]['qtd'];

    $titulo = new Titulo();
    $ultimos = $titulo->carregarPortifolio($usuario_perfil->id);
    $gosteis = $titulo->carregarPortifolio($usuario_perfil->id, true);
    ?>

		<header id="perfil-topo">
			<div class="box-window">
				<div id="lado-esquerdo">
				    <? if ($usuario_perfil->foto_perfil=="" or $usuario_perfil->foto_perfil==null) {?>
                    <img src="img/profile-default.png" id="foto-perfil">
                    <?} else {?>
                    <img src="https://<? echo $_SERVER['SERVER_NAME']; ?>/servidor/thumbs-usuarios/<? echo $usuario_perfil->foto_perfil; ?>" id="foto-perfil">
                    <?}
                    
                    if ($editar) {
                       // if (($_SESSION['tipo_usuario']==1 and !isset($nickname)) or $_SESSION['tipo_usuario']>=2) {
                    ?>
                    <div>
				    <a id="editar-perfil" class="botao" href="configuracoes<? echo ($_SESSION['tipo_usuario']>=2 and isset($nickname))?"/".$usuario_perfil->nickname:"" ?>">Editar <i class="fas fa-edit"></i></a>
                  
                    <a id="dashboard" class="botao" href="dashboard<? echo ($_SESSION['tipo_usuario']>=2 and isset($nickname))?"/".$usuario_perfil->nickname:"" ?>">Dashboard <i class="fas fa-columns"></i></a>
                    </div>
                    <?
                       // }
                    }
                        
                    ?>
				</div>

				<div id="dados-perfil">
					<h2><? echo strtoupper($usuario_perfil->nickname).", <span>{$usuario_perfil->nome}</span>"; ?></h2>

					<div class="lado-a-lado">
						<p id="perfil-online">Online em <? echo date("d/m/Y", strtotime($usuario_perfil->ultimo_login)); ?>
						</p>
						<p id="localizacao"><i class="fas fa-map-marker-alt"></i><? echo $usuario_perfil->localizacao; ?></p>
					</div>

					<div id="contadores-perfil" class="lado-a-lado">
						<p><i class="fas fa-users"></i> <? echo number_format($qtd_seguidores, 0, "", "."); ?></p>
						<p><i class="fas fa-heart"></i> <? echo number_format($qtd_gosteis, 0, "", "."); ?></p>
					</div>
					
					<p id="descricao">
						<? echo $usuario_perfil->descricao==""?"Nenhuma descrição!":$usuario_perfil->descricao; ?>
					</p>

					<div id="links">
						<? if ($usuario_perfil->link_twitter!=null) {?><a href="<? echo $usuario_perfil->link_twitter ?>"><i class="fab fa-twitter"></i> <span>Twitter</span></a><?}?>
						<? if ($usuario_perfil->link_facebook!=null) {?><a href="<? echo $usuario_perfil->link_facebook ?>"><i class="fab fa-facebook-f"></i> <span>Facebook</span></a><?}?>
						<? if ($usuario_perfil->link_instagram!=null) {?><a href="<? echo $usuario_perfil->link_instagram ?>"><i class="fab fa-instagram"></i> <span>Instagram</span></a><?}?>
						<? if ($usuario_perfil->link_youtube!=null) {?><a href="<? echo $usuario_perfil->link_youtube ?>"><i class="fab fa-youtube"></i> <span>Youtube</span></a><?}?>
						<? if ($usuario_perfil->link_site!=null) {?><a href="<? echo $usuario_perfil->link_site ?>"><i class="fas fa-link"></i> <span>Meu Site</span></a><?}?>
					</div>
				</div>
			</div>
		</header>

		<div class="box-window" id="main">
            <div id="navegacao">
                <ul>
                    <li id="icon-comentarios" class="<? echo !$seguindo?"selecionado":"" ?>" title="Comentarios"><i class="fas fa-comment"></i><span>Mural</span></li>
                    <li id="icon-projetos" title="Series"><i class="fas fa-book"></i><span>Series</span></li>
                    <li id="icon-titulos" title="Ultimos capítulos adicionados"><i class="fas fa-newspaper"></i><span>Novos</span></li>
                    <li id="icon-favoritos" class="<? echo $seguindo?"selecionado":"" ?>" title="Favoritos"><i class="fas fa-bookmark"></i><span>Favoritos</span></li>
                    <li id="icon-gostei" title="Gostei"><i class="fas fa-heart"></i><span>Gostei</span></li>
                    <!-- <li id="icon-seguidores" title="Seguidores"><i class="fas fa-heart"></i></li> -->
                </ul>
            </div>

			<article id="">
				<div id="comentarios" style="<? echo !$seguindo?"display: block":"display: none" ?>">
					<h3>Mural</h3>
					<? if (isset($_SESSION['logado'])) {?>
					<div id="comentar">
                        <? if ($usuario->foto_perfil=="" or $usuario->foto_perfil==null) {?>
	                    <img src="img/profile-default.png" id="foto-perfil">
	                    <?} else {?>
	                    <img src="/servidor/thumbs-usuarios/<? echo $usuario->foto_perfil; ?>" id="foto-perfil">
	                    <?}?>
                        <form>
                            <input type="hidden" name="id_referencia">
                            <input type="hidden" name="id">
                            <div class="input linha">
                                <textarea name="texto" required placeholder="Escreva algo"></textarea>
                            </div>
                            <p id="referencia"><span></span> <i class="fa fa-times"></i></p>
                            <p id="editar"><span></span> <i class="fa fa-times"></i></p>
                            <button class="botao" disabled><i class="fa fa-paper-plane"></i><span>Publicar</span></button>
                        </form>
					</div>
					<?}?>
					<ul>
						
					</ul>
					
					<div class="img-loading"></div>
				</div>

				<div id="projetos" style='display: none'>
					<h3>Series <span>(0)</span></h3>
					
					<ul>
						<div class="img-loading"></div>
					</ul>
				</div>

				<div id="gostei" style='display: none'>
				    <h3>Gostei <span>(<? echo count($gosteis); ?>)</span></h3>
				    
				    <ul>
				        <? foreach($gosteis as $value) { ?>
						<li class="molde lista-grande gostei">
							<a href="/<? echo ($value['tipo']==1?"lerComic/":"lerNovel/").$value['id'] ?>">
                                <?
								if ($value['thumb_titulo']==null) {
								?>
					            <img src="/img/sem-foto.png">
					            <?
								} else {
					            ?>
					            <img src="/servidor/titulos/thumbs/<? echo $value['thumb_titulo']; ?>">
					            <?
								}
					            ?>
                                <div>
                                    <h4><? echo substr($value['nome'], 0, 25) ?></h4>
                                    <p>da série <b><? echo $value['serie']; ?></b></p>
                                    <p><? echo $value['descricao']; ?></p>
                                </div>
							</a>
						</li>
						<? } ?>
				    </ul>
				</div>
				
				<div id="titulos" style='display: none'>
				    <h3>Últimos capítulos adicionados</h3>
				    
				    <ul>
				        <? foreach($ultimos as $value) { ?>
						<li class="molde lista-grande gostei">
							<a href="/<? echo ($value['tipo']==1?"lerComic/":"lerNovel/").$value['id'] ?>">
                                <?
								if ($value['thumb_titulo']==null) {
								?>
					            <img src="/img/sem-foto.png">
					            <?
								} else {
					            ?>
					            <img src="/servidor/titulos/thumbs/<? echo $value['thumb_titulo']; ?>">
					            <?
								}
					            ?>
                                <div>
                                    <h4><? echo substr($value['nome'], 0, 25) ?></h4>
                                    <p>da série <b><? echo $value['serie']; ?></b></p>
                                    <p><? echo $value['descricao']; ?></p>
                                </div>
							</a>
						</li>
						<? } ?>
				    </ul>
				</div>

				<div id="favoritos"  style="<? echo $seguindo?"display: block":"display: none" ?>">
				    <h3>Seguindo <span>0</span></h3>
				    
				    <ul>
				        <div class="img-loading"></div>
				    </ul>
				</div>
			</article>

			<section id="lateral-direita">
				<div id="patrocinio">
					<h3>Apoie o Autor</h3>
					<? if ($usuario_perfil->link_patreon!=null) {?><a href="<? echo $usuario_perfil->link_patreon; ?>" class="botao patreon" target="_BLANK">Patreon <i class="fa fa-external-link-alt"></i></a><?}?>
					<? if ($usuario_perfil->link_padrim!=null) {?><a href="<? echo $usuario_perfil->link_padrim; ?>" class="botao padrim" target="_BLANK">Padrim <i class="fa fa-external-link-alt"></i></a><?}?>
					<? if ($usuario_perfil->link_apoiase!=null) {?><a href="<? echo $usuario_perfil->link_apoiase; ?>" class="botao apoia-se" target="_BLANK">Apoia-se <i class="fa fa-external-link-alt"></i></a><?}?>
				</div>
				
				<div id="ultimos-titulos">
					<h3>Novos <a href="">Ver todos</a></h3>
					<ul>
					    <? 
					    $count = 1;
					    foreach($ultimos as $value) { 
					    	if ($count>3) break;
				    	?>
						<li class='lista-pequena'>
							<a href="/<? echo ($value['tipo']==1?"lerComic/":"lerNovel/").$value['id'] ?>">
                                <?
								if ($value['thumb_titulo']==null) {
								?>
					            <img src="/img/sem-foto.png">
					            <?
								} else {
					            ?>
					            <img src="/servidor/titulos/thumbs/<? echo $value['thumb_titulo']; ?>">
					            <?
								}
					            ?>
                                <div>
                                    <h4><? echo substr($value['nome'], 0, 21) ?></h4>
                                    <p>da série <b><? echo $value['serie']; ?></b></p>
                                </div>
							</a>
						</li>
						<? 
						$count++;
						} 
						?>
					</ul>
				</div>

				<div id="meus-likes">
					<h3>Gostei <a href="">Ver todos</a></h3>
					<ul>
					    <? 
					    $count = 1;
					    foreach($gosteis as $value) { 
					    	if ($count>3) break;
				    	?>
						<li class='lista-pequena'>
							<a href="/<? echo ($value['tipo']==1?"lerComic/":"lerNovel/").$value['id'] ?>">
                                <?
								if ($value['thumb_titulo']==null) {
								?>
					            <img src="/img/sem-foto.png">
					            <?
								} else {
					            ?>
					            <img src="/servidor/titulos/thumbs/<? echo $value['thumb_titulo']; ?>">
					            <?
								}
					            ?>
                                <div>
                                    <h4><? echo substr($value['nome'], 0, 21) ?></h4>
                                    <p>da série <b><? echo $value['serie']; ?></b></p>
                                </div>
							</a>
						</li>
						<? 
						$count++;
						} 
						?>
					</ul>
				</div>
			</section>
		</div>

		<? include("../paginas/rodape.php") ?>
</body>
<script type="text/javascript">
	var usuario_perfil = <? echo json_encode($usuario_perfil->toArray()); ?>;
	var tipoUsuario = <? echo $_SESSION['tipo_usuario']; ?>;
	var qtd_gosteis = <? echo $qtd_gosteis; ?>;
</script>
<script src="js/usuario/perfil.js?<? echo time(); ?>" async></script>

</html>