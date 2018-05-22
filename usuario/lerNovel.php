<?
require "../php/database/conexao.php";
require "../php/classes/usuario.class.php";
require "../php/classes/serie.class.php";
require "../php/classes/titulo.class.php";

date_default_timezone_set('America/Sao_Paulo');

if ($_GET['id']!="") {
    $id = $_GET['id'];
    
    $titulo = new Titulo();
    $teste = $titulo->carregar($id);
    
    if (!$teste) {
        $_SESSION['erro_msg'] = "Esse capítulo não existe!";
        header("Location: /perfil");
        exit;
    }

    $titulo->visualizacao();
    
    $serie = new Serie();
    $serie->carregar($titulo->id_projeto);
    
    if ($serie->tipo==1) {
        $_SESSION['erro_msg'] = "Função inválida!";
        header("Location: /serie/{$serie->id}");
        exit;
    }
    
    $autor = new Usuario();
    $autor->carregar($serie->id_usuario);
} else {
    $_SESSION['erro_msg'] = "Busca inválida!";
    header("Location: /perfil");
    exit;
}

$capitulos = DBselect("titulo t CROSS JOIN (SELECT @cnt := 0) n", "where rascunho = 0 and id_projeto={$serie->id} and id>=".($titulo->id-2)." ORDER BY id ASC", "id, thumb_titulo, nome, (@cnt := @cnt + 1) AS numero");

$temp = [];
foreach($capitulos as $cap) {
    $temp[$cap['id']] = $cap;
}
$capitulos = $temp;

// foreach ($_COOKIE as $key => $value) {
//     setcookie($key, null, -1, '');
// }

// unset($_COOKIE);
// echo "<pre>";var_dump($_COOKIE); exit;
?>
<!DOCTYPE HTML>
<html>

<head>
    <title><? echo $titulo->nome ?> | ZINNES</title>
    <link rel="icon" type="image/png" href="img/logo.png" />
    <base href="https://<? echo $_SERVER['SERVER_NAME']; ?>/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/usuario/leitor.css" media="(min-width: 1000px)">
    <link rel="stylesheet" type="text/css" href="css/geral/comentarios.css" media="(min-width: 1000px)">
    <link rel="stylesheet" type="text/css" href="css/geral/geral.css" media="(min-width: 1000px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/usuario/leitor.css" media="(max-width: 999px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/geral/geral.css" media="(max-width: 999px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/geral/comentarios.css" media="(max-width: 999px)">
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>
    <?
    $usuario = new Usuario();
    include("../paginas/header.php") ;
    $editar = 0;
    $gostei = 0;
    $seguir = 0;
    // SE ESTIVER LOGADO
    if ($logado) {
        if ($serie->id_usuario==$usuario->id || $_SESSION['tipo_usuario']>1) {
            $editar = true;
        }
        $gostei = DBselect("avaliar_titulo", "where id_titulo={$titulo->id} and id_usuario={$usuario->id}");
        if (count($gostei)==0) $gostei=0;
        if ($serie->id_usuario==$usuario->id) $seguir = 2;
        else $seguir = DBselect("seguir_projeto", "where id_usuario={$usuario->id} and id_projeto={$serie->id}");
    }
    $anterior = null;
    $proximo = null;
    ?>
    <section id="tudo" class="
    <? 
    echo (isset($_COOKIE['lateral']) and $_COOKIE['lateral']==0)?"":"aberto";
    echo (isset($_COOKIE['escuro']) and $_COOKIE['escuro']==1)?"escuro":""; 
    ?>
    ">
        <aside class="scroll">
            <a id="imagem-projeto" href="<? echo "serie/".$serie->id ?>">
                <? if ($titulo->thumb_titulo!=null) {?>
                <img src="servidor/titulos/thumbs/<? echo $titulo->thumb_titulo; ?>" alt="">
                <?} else if ($serie->thumb_projeto!=null) {?>
                <img src="servidor/projetos/thumbs/<? echo $serie->thumb_projeto; ?>" alt="">
                <?} else {?>
                <img src="img/sem-foto.png">
                <?}?>
                <!-- <img src="img/projeto.jpg" id="imagem-projeto"> -->
                <h2><? echo $serie->nome; ?></h2>
            </a>
            
            <? if ($seguir==0) {?>
            <button class="botao" id="seguir">Seguir <i class="fas fa-plus"></i></button>
            <?} else if ($seguir==2) {
            ?>
            <button class="botao autor" id="seguir">Autor <i class="fas fa-check"></i></button>
            <?} else {?>
            <button class="botao seguindo" id="seguir">Seguindo <i class="fas fa-check"></i></button>
            <?}?>

            <!-- <button class="botao" id="seguir">Seguir <i class="fa fa-plus"></i></button> -->
            
            <div id="likes" class="lado-a-lado">
                <p><i class="fa fa-heart"></i>
                <span><? echo $serie->gosteis; ?></span></p>
                <span class="dot"></span>
                <p><i class="fa fa-users"></i> <? echo $serie->seguidores; ?></p>
            </div>
            
            <a id="autor" href="perfil/<? echo $autor->nickname ?>">
                <? if ($autor->foto_perfil!=null) {?>
                <img src="servidor/thumbs-usuarios/<? echo $autor->foto_perfil; ?>">
                <?} else {?>
                <img src="img/profile-default.png">
                <?}?>
                <h3><? echo $autor->nickname; ?></h3>
            </a>

            <ul id="lista">
                <h3>Capítulos <a href="serie/<? echo $serie->id ?>">Ver todos</a></h3>
                
                <div>
                    <? 
                    foreach($capitulos as $cap) {
                        if ($cap['id']<$titulo->id-2 or $cap['id']>$titulo->id+2) continue;
                        if ($cap['id']==$titulo->id) $titulo->numero = $cap['numero'];
                        
                        if ($cap['id']<$titulo->id) $anterior = $cap;
                        if ($cap['id']>$titulo->id and $proximo==null) $proximo = $cap;
                    ?>
                    <li class="lista-pequena">
                        <a href="lerNovel/<? echo $cap['id']; ?>">
                            <? if ($cap['thumb_titulo']!=null) {?>
                            <img src="servidor/titulos/thumbs/<? echo $cap['thumb_titulo']; ?>" alt="">
                            <?} else if ($serie->thumb_projeto!=null) {?>
                            <img src="servidor/projetos/thumbs/<? echo $serie->thumb_projeto; ?>" alt="">
                            <?} else {?>
                            <img src="img/sem-foto.png">
                            <?}?>
                            <h4><? echo $cap['nome']; ?></h4>
                        </a>
                    </li>
                    <? } ?>
                </div>
            </ul>
        </aside>

        <section id="main">
            <i class="fa fa-angle-right aside-menu"></i>
            <article>
                <h4>Capítulos <span>(<? echo $titulo->numero." de ".$serie->capitulos ?>)</span> <span class="right"><? echo date("d M, Y", strtotime($titulo->data)) ?></span></h4>

                <h1><? echo $titulo->nome ?></h1>

                <div id="texto">
                    
                    <? echo $titulo->texto; ?>
                    
                </div>
                
                <footer id="acoes">
                    <button id="gostei" class="botao borda <? echo $gostei!=0?"marcado":"" ?>">
                        <span><? echo $titulo->gosteis; ?></span> <i class="fa fa-heart"></i>
                    </button>

                    <div>
                        <p>Compartilhar:</p>

                        <a href="https://www.facebook.com/sharer/sharer.php?u=<? echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>" target="_BLANK"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com/home?status=<? echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>" target="_BLANK"><i class="fab fa-twitter"></i></a>
                        <a href="https://plus.google.com/share?url=<? echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>" target="_BLANK"><i class="fab fa-google"></i></a>
                        <a href="<? echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>" id="copiar"><i class="fa fa-copy"></i></a>
                    </div>
                </footer>

                <div id="menu-atalhos">
                    <? if ($anterior!=null) {?>
                    <a id="anterior" href="lerNovel/<? echo $anterior['id']; ?>">
                        <p><? echo $anterior['nome']; ?></p>
                        <i class="fa fa-angle-up"></i>
                    </a>
                    <?
                    }
                    if ($proximo!=null) {?>
                    <a id="proximo" href="lerNovel/<? echo $proximo['id']; ?>">
                        <p><? echo $proximo['nome']; ?></p>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <?}?>
                </div>
                
                <div id="comentarios">
                    <? if($usuario->estaDeclarado("id")) {?>
					<div id="comentar">
                        <? if ($usuario->foto_perfil!=null) {?>
                        <img src="servidor/thumbs-usuarios/<? echo $usuario->foto_perfil; ?>">
                        <?} else {?>
                        <img src="img/profile-default.png">
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
            </article>
            
            <i id="luz" class="far fa-lightbulb"></i>
        </section>
    </section>

    <div id="div_cookie_write"></div>
    
    <? include("../paginas/rodape.php") ?>
</body>
<script>
    var titulo = <? echo json_encode($titulo->toArray()); ?>;
    var serie = <? echo json_encode($serie->toArray()); ?>;
    var autor = <? echo json_encode($autor->toArray()); ?>;
    var capitulos = <? echo json_encode($capitulos); ?>;
    var usuario = <? echo json_encode($usuario->toArray()); ?>;
    var editar = <? echo $editar; ?>;
    var tipoUsuario = <? echo $_SESSION['tipo_usuario']; ?>;
    var servidor = '<? echo $_SERVER['SERVER_NAME']; ?>';
</script>
<script src="js/usuario/leitor.js?<? echo time(); ?>" async></script>

</html>