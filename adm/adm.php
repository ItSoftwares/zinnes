<?
require "../php/database/conexao.php";
require "../php/util/listarArquivos.php";
require "../php/util/sessao.php";
require "../php/classes/usuario.class.php";

verificarSeSessaoExpirou();

$usuarios = DBselect("usuario", "", "count(id) as qtd")[0]['qtd'];
$series = DBselect("projeto", "", "count(id) as qtd")[0]['qtd'];
$titulos = DBselect("titulo", "", "count(id) as qtd")[0]['qtd'];
$coments = DBselect("comentario_titulo", "", "count(id) as qtd")[0]['qtd'];

$diretorio = realpath(dirname(__DIR__).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."slides");
if ($diretorio!="" and is_dir($diretorio)) $slides = listar($diretorio)['nomes'];
$links = DBselect("slides");
?>
<!DOCTYPE HTML>
<html> 

<head>
	<title>ADM PAINEL | ZINNES</title>
    <link rel="icon" type="image/png" href="img/logo.png" />
	<base href="https://<? echo $_SERVER['SERVER_NAME']; ?>/">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/adm/adm.css" media="(min-width: 1000px)">
	<link rel="stylesheet" type="text/css" href="css/geral/geral.css" media="(min-width: 1000px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/adm/adm.css" media="(max-width: 999px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/geral/geral.css" media="(max-width: 999px)">
	<link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>
	<?
    include("../paginas/header.php");
    $codigo = DBselect("moderador", "where id_usuario={$usuario->id}")[0]['pin'];
    $moderadores = DBselect("usuario", "where id in (select id_usuario from moderador) and id<>{$usuario->id}", "*");
    if ($moderadores==null) $moderadores=[];
    ?>

    <div id="pin" style="<? echo isset($_SESSION['pin'])?'display: none':''; ?>">
    	<h1><? echo $codigo=="aaaa"?"Defina um PIN de acesso <br>Somente números":"PIN" ?></h1>

    	<div id="codigo">
    		<div class="input linha" id="pin_1"><input type="number" value="" step="1" maxlength="1" max="9" min="0" size="1" autofocus="" placeholder="*"></div>
	    	<div class="input linha" id="pin_2"><input type="number" value="" step="1" maxlength="1" max="9" min="0" size="1" placeholder="*"></div>
	    	<div class="input linha" id="pin_3"><input type="number" value="" step="1" maxlength="1" max="9" min="0" size="1" placeholder="*"></div>
	    	<div class="input linha" id="pin_4"><input type="number" value="" step="1" maxlength="1" max="9" min="0" size="1" placeholder="*"></div>
    	</div>

    	<button id="definir-pin" class="botao cantos" style="<? echo $codigo!="aaaa"?'display: none':''; ?>">Definir</button>
    </div>

    <section class="box-window" id="geral">
    	<ul>
    		<li><a href="/pesquisa/autor"><i class="fa fa-user"></i> <span><? echo $usuarios; ?></span> Usuários</a></li>
    		<li><a href="/pesquisa/serie"><i class="fa fa-book"></i> <span><? echo $series; ?></span> Series</a></li>
    		<li><a href="/pesquisa/comic"><i class="fa fa-newspaper"></i> <span><? echo $titulos; ?></span> Capítulos</a></li>
    		<li><a href="admPainel#comentarios"><i class="fa fa-comment"></i> <span><? echo $coments; ?></span> Comentários</a></li>
    	</ul>

    	<div id="boxes">
            <? if ($_SESSION['tipo_usuario']==3) { ?>
            <div id="slides">
                <h3>Slides</h3> 
                <button class="botao canto">Links</button>
                <div class="clear"></div>
                <section>
                    <?
                    $count = 0;
                    foreach ($slides as $key => $img) {
                    ?>
                    <div data-index="<? echo $key; ?>">
                        <i class="fa fa-save salvar" title="Salvar imagem para slide"></i>
                        <i class="fa fa-trash remover" title="Remover slide"></i>
                        <img src="/servidor/slides/<? echo $img; ?>">

                        <label for="slide_<? echo $count+1 ?>">Trocar Imagem</label>
                        <input type="file" name="slide" id="slide_<? echo $count+1 ?>">
                    </div>
                    <?
                    $count++;
                    }
                    ?>
                    <div id="novo" style="<? echo $count==6?"display: none":''; ?>">
                        <i class="fa fa-plus icon"></i>
                        <i class="fa fa-save salvar" title="Salvar imagem para slide"></i>
                        <i class="fa fa-trash remover" title="Remover slide"></i>

                        <label for="slide_<? echo $count+1 ?>">Adicionar Imagem</label>
                        <input type="file" name="slide" id="slide_<? echo $count+1 ?>">
                    </div>
                </section>
            </div>     

            <div id="moderadores">
                <h3>Moderadores</h3>
                <button class="botao canto">Novo</button>
                <div class="clear"></div>

                <ul class="scroll">
                    <?
                    foreach ($moderadores as $key => $mod) {
                        $mod = new Usuario($mod);
                    ?>
                    <li class="lista-grande moderador" data-id="<? echo $mod->id; ?>">
                        <a href="/perfil/<? echo $mod->nickname; ?>" data-id="<? echo $mod->id; ?>">
                            <?
                            if ($mod->foto_perfil!=null) {
                            ?>
                            <img src="servidor/thumbs-usuarios/<? echo $mod->foto_perfil; ?>" alt="">
                            <?
                            } else {
                            ?>
                            <img src="img/profile_default.png" alt="">
                            <?}?>
                             
                            <div>
                                <h4><? echo $mod->nickname; ?></h4>
                                
                                <p class="descricao"><? echo substr($mod->descricao, 0, 50); ?></p>
                            </div>

                            <i class="fa fa-times remover"></i>
                        </a>
                    </li>
                    <?
                    }

                    if (count($moderadores)==0) {
                        echo "<p class='aviso'>Nenhum moderador</p>";
                    }
                    ?>
                </ul>
            </div>
            <?}?>
            <div id="comentarios" class="meio">
                <h3>Comentarios</h3>

                <div class="input linha">
                    <input type="text" name="busca" placeholder="Digite e Enter para buscar">
                    <i class="fa fa-search"></i>
                </div>

                <div class="clear"></div>

                <ul class="scroll">
                    <p class="aviso">Faça uma busca por palavras chaves!</p>
                </ul>
            </div>
            
            <div id="email" class="meio">
                <h3>Enviar Email</h3>

                <form class="campos">
                    <div class="input linha metade">
                        <input type="text" name="usuario" placeholder="Nickname do usuário!" <? echo $_SESSION['tipo_usuario']==2?"required":"" ?> autocomplete="off" />
                        <i class="fa fa-user"></i>
                        <ul id="lista-usuarios">
                            <!-- <li>izac</li>
                            <li>le ninja</li> -->
                        </ul>
                    </div>

                    <div class="input linha metade">
                        <select name="sexo">
                            <option value="0">Sexo (TODOS)</option>
                            <option value="1">Homem</option>
                            <option value="2">Mulher</option>
                        </select>
                    </div>

                    <div class="input linha">
                        <textarea name="mensagem" placeholder="Digite aqui o email"></textarea>
                        <i class="fa fa-envelope"></i>
                    </div>

                    <button class="botao canto">Enviar</button>
                </form>
            </div>
        </div>
    </section>

    <div id="links" class="fundo">
    	<i class="fa fa-times fechar"></i>
        
        <div>
            <h3>Link dos Slides</h3>

            <form>
                <div class="campos">
                    
                </div>

                <button class="botao canto">Salvar</button>
                <div class="clear"></div>
            </form>
        </div>
    </div>

    <section id="visualizar-imagem" class="fundo">
        <i class="fa fa-times fechar"></i>

    	<i class="fa fa-angle-left setas" id="anterior"></i>
    	<img src="">
    	<i class="fa fa-angle-right setas" id="proximo"></i>
    </section>

    <i class="fa fa-key" id="redefinir" title="Redefinir PIN"></i>
    <div id="div_session_write"></div>

	<? include("../paginas/rodape.php") ?>
</body>
<script type="text/javascript">
    var codigoEntrada = '<? echo $codigo; ?>';
    var slides = <? echo json_encode($slides); ?>;
	var links = <? echo json_encode($links); ?>;
</script>
<script src="js/adm/adm.js?<? echo time(); ?>"></script>

</html>