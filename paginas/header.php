<?
require_once (dirname(__DIR__)."/php/classes/usuario.class.php");
require_once (dirname(__DIR__)."/php/database/conexao.php");

if (!isset($_SESSION)) session_start();
$logado = false;
if (isset($_SESSION['tipo_usuario']) and $_SESSION['tipo_usuario']>0) {
    $_SESSION['logado']=1;
    $logado = true;
    
    $usuario = unserialize($_SESSION['usuario']);
} else { 
    $_SESSION['tipo_usuario'] = 0;
}

// MENSAGENS ENVIADAS PELA SESSAO
$info_msg = isset($_SESSION['info_msg'])?$_SESSION['info_msg']:"";
$conf_msg = isset($_SESSION['conf_msg'])?$_SESSION['conf_msg']:"";
$erro_msg = isset($_SESSION['erro_msg'])?$_SESSION['erro_msg']:"";
// echo isset($_SESSION['erro_msg'])?$_SESSION['erro_msg']:"NOPS";
unset($_SESSION['info_msg']);
unset($_SESSION['conf_msg']);
unset($_SESSION['erro_msg']);

// PEGAR NOTIFICAÇÕES
if (isset($_SESSION['logado'])) {
    $notificacoes = DBselect("notificacao", "where id_usuario={$usuario->id} and lido=0 order by data DESC limit 10", "COUNT(*) as qtd")[0]['qtd'];
    $logs = DBselect("log_moderador", "where id_usuario={$usuario->id} and lido=0 order by data DESC limit 10", "COUNT(*) as qtd")[0]['qtd'];
    $qtd = $notificacoes+$logs;
}
?>
<head>
    <meta name="google-signin-scope" content="profile email">
    <meta name="google-signin-client_id" content="662723215242-6ber8do472s3g6cgpsskmvab8b23tiv3.apps.googleusercontent.com">
</head>
<header id="topo" class="<? if (isset($menu_style)) echo $menu_style ?>">
    <div id="topo-container">
        <nav class="menu esquerda">
            <a href="/" id="logo">
                <img src="https://<? echo $_SERVER['SERVER_NAME']; ?>/img/logo.png">
            </a>

            <ul>
                <li id="Comics"><a href="/pesquisa/comic">Comics</a></li>
                <li id="Novels"><a href="/pesquisa/novel">Novels</a></li>
            </ul>
        </nav>
        
        <nav class="menu direita">
            <?
            if (!isset($_SESSION['logado'])) {
            ?>
            <ul>
                <li id="link-publicar"><a href="dashboard"><i class="far fa-edit"></i><span>Publicar</span></a></li>
                <li id="link-login"><a href=""><i class="fa fa-sign-in-alt"></i><span>Login</span></a></li>
            </ul>
            <?
			} else {
			?>
            <ul>
            	<? if ($_SESSION['tipo_usuario']>1) {?><li id="adm"><a href="admPainel"><i class="fa fa-user"></i><span>Adm</span></a></li><?}?>
                <li id="publicar"><a href="dashboard"><i class="far fa-edit"></i><span>Publicar</span></a></li>
            	<li id="header-foto-perfil" class='no'>
            		<? if ($usuario->foto_perfil==null) { ?>
                    <i class="icon fa fa-user-circle"></i>
                    <? } else { ?>
                    <img src="https://<? echo $_SERVER['SERVER_NAME']; ?>/servidor/thumbs-usuarios/<? echo $usuario->foto_perfil; ?>">
                    <? } ?>
            		<div id="perfil-painel" class="flutuante">
						<header>
            				<a href="perfil">
            				    <? if ($usuario->foto_perfil==null) { ?>
                                <i class="icon fa fa-user-circle"></i>
                                <? } else { ?>
                                <img src="https://<? echo $_SERVER['SERVER_NAME']; ?>/servidor/thumbs-usuarios/<? echo $usuario->foto_perfil; ?>">
                                <? } ?>
                                <div>
                                    <h4><? echo explode(' ', $usuario->nome)[0]; ?></h4>
                                    <p><? echo $usuario->nickname; ?></p>
                                </div>
            				</a>
						</header>
						
						<ul>
							<li>
								<a href="configuracoes"><i class="fa fa-cog"></i><span>Configurar</span></a>
							</li>
							<li>
								<a href="/perfil/<? echo "{$usuario->nickname}/seguindo" ?>"><i class="fa fa-bookmark"></i><span>Favoritos</span></a>
							</li>
							<li>
								<a href="sair"><i class="fa fa-power-off"></i><span>Sair</span></a>
							</li>
						</ul>
					</div>
				</li>
            	<li id="icon-notificacao" class='no'>
            	    <? if ($qtd>0) { ?> <span class="notificacao-qtd"><? echo $qtd; ?></span> <?}?>
            		<i class="icon fa fa-bell"></i>
            		<div id="notificacoes" class="flutuante">
						<header>
							<h4>Notificações</h4>
							
						</header>
						<ul class="nada scroll">
						    <div class="img-loading"></div>
<!--
							<li class="notificacao novo">
								<a href="#">
									<img src="https://<? echo $_SERVER['SERVER_NAME']; ?>/img/logo.png">
									<div>
										<h4>Novo capitulo de fulano</h4>
										<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam culpa nihil doloribus mollitia amet,</p>
									</div>
									
								</a>
							</li>
							<li class="notificacao log lido">
								<a href="#">
									<h4>Log do Sistema</h4>
									<p>O Moderador Fulano alterou tal coisa em seu perfil!</p>
								</a>
							</li>
-->
						</ul>
					</div>
				</li>
            </ul>
            <? } ?>
            <div id="pesquisar" class="">
                <input type="text" placeholder="Digite algo">
                <i class="icon fa fa-search"></i>
            </div>
        </nav>
        
        <div id="menu-toogle">
            <span id="bar-1"></span>
            <span id="bar-2"></span>
            <span id="bar-3"></span>
        </div>
    </div>
</header>
<?
include(dirname(__DIR__)."/paginas/loginCadastro.php");
?>