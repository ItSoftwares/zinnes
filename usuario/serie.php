<?
//echo $_GET['id']; exit;
require "../php/database/conexao.php";
require "../php/classes/serie.class.php";
require "../php/classes/usuario.class.php";

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

if ($_GET['id']!="") {
    $id = $_GET['id'];
    $serie = DBselect('projeto p INNER JOIN usuario u ON p.id_usuario = u.id', "where p.id='{$id}'", "p.*, u.nickname as autor, 
    (select nome from genero where id=p.id_genero) genero, 
    
    (select COUNT(id_titulo) from avaliar_titulo where id_titulo in (select id from titulo where id_projeto=p.id)) gosteis,
    
    (select SUM(visualizacoes) from titulo where id_projeto=p.id) visualizacoes");

    if (!isset($serie)) {
        $_SESSION['erro_msg'] = "Serie não existe!";
        header("Location: /perfil");
        exit;
    }
    
    $serie = new Serie($serie[0]);
    
    if ($serie->visualizacoes==null) $serie->visualizacoes = 0;
} else {
    $_SESSION['erro_msg'] = "Serie não existe!";
    header("Location: /perfil");
    exit;
}

$menu_style = "transparente";
?>
<!DOCTYPE HTML>
<html>

<head>
    <title><? echo $serie->nome; ?> | ZINNES</title>
    <link rel="icon" type="image/png" href="img/logo.png" />
    <base href="https://<? echo $_SERVER['SERVER_NAME']; ?>/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/usuario/serie.css" media="(min-width: 1000px)">
    <link rel="stylesheet" type="text/css" href="css/geral/geral.css" media="(min-width: 1000px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/usuario/serie.css" media="(max-width: 999px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/geral/geral.css" media="(max-width: 999px)">
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    
    <style>
        <? if ($serie->banner_projeto==null) {?>
        header#serie-topo {
            background-image: url("img/banner-teste.jpg");
            background-size: 30%;
        }
        <?} else {?>
        header#serie-topo {
            background-image: url("servidor/projetos/banners/<? echo $serie->banner_projeto; ?>");
            background-size: cover;
        }
        <?}?>
    </style>
</head>

<body>
    <? 
    $usuario = new Usuario();
    include("../paginas/header.php") ;
    $editar = false;
    $seguir = 0;
    // SE ESTIVER LOGADO
    if ($logado) {
        if ($serie->id_usuario==$usuario->id || $_SESSION['tipo_usuario']>1) {
            $editar = true;
        }
        
        if ($serie->id_usuario==$usuario->id) $seguir = 2;
        else $seguir = DBselect("seguir_projeto", "where id_usuario={$usuario->id} and id_projeto={$serie->id}");
    }
    
    // CAPITULOS    
    $tudo = ($usuario->id==$serie->id_usuario or $editar)?"":"and rascunho=0";
    
    $capitulos = DBselect("titulo t", "where t.id_projeto={$serie->id} {$tudo} order by data DESC", "t.*, (select COUNT(id_usuario) from avaliar_titulo where id_titulo = t.id ) gosteis");
    
    if (count($capitulos)>0) {
        if ($capitulos[0]['id']==null) $capitulos = [];
        else $primeiro = $capitulos[count($capitulos)-1];
    } else $capitulos = [];
    ?>
    
    <header id="serie-topo" class="<? echo $serie->banner_projeto==null?"sem-banner":""; ?>">
        <div id="banner">
   <!-- 1350px x 325px    -->
        </div>
        
        <div class="box-window" id="titulo">
            <span id="genero"><? echo $serie->genero; ?></span>
            <h1><? echo $serie->nome; ?></h1>
            <a id="autor" href="perfil/<? echo $serie->autor; ?>"><? echo $serie->autor; ?> <i class="fa fa-user"></i></a>
            
            <? if ($editar) {?>
            <span id="icon-mudar-banner">
                <i class="fa fa-image"></i>
            </span>
            <?}?>
            
            <div id="links">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<? echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>" target="_BLANK"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com/share?url=<? echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>" target="_BLANK"><i class="fab fa-twitter"></i></a>
                <a href="https://plus.google.com/share?url=<? echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>" target="_BLANK"><i class="fab fa-google"></i></a>
                <a href="<? echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>" id="copiar"><i class="fa fa-copy"></i></a>
                
                <? if ($seguir==0 || count($seguir)==0) {?>
                <button class="botao" id="seguir"><span>Seguir</span><i class="fas fa-plus"></i></button>
                <?} else if ($seguir==2) {
                ?>
                <button class="botao autor"><span>Autor</span><i class="fas fa-check"></i></button>
                <?} else {?>
                <button class="botao seguindo" id="seguir"><span>Seguindo</span><i class="fas fa-check"></i></button>
                <?}?>
            </div>
        </div>
    </header>
    
    <div class="box-window" id="series">
        <div id="descricao">
            <div id="imagem-projeto">
                <? if ($serie->thumb_projeto==null) {?>
                <i class="fa fa-eye-slash"></i>
                <?} else {?>
                <img src="servidor/projetos/thumbs/<? echo $serie->thumb_projeto; ?>" alt="">
                <?}?>
                
                <? if ($editar) {?>
                <label id="icon-mudar-imagem-projeto" for="upload-imagem-projeto">
                    <i class="fa fa-image"></i>
                    Mudar<br>200x200
                </label>
                <?}?>
                <form>
                    <input type="file" id="upload-imagem-projeto" name="upload-imagem-projeto">
                </form>
            </div>
            
            <button id="salvar" class="botao">Salvar <i class="fa fa-save"></i></button>
           
            <div id="likes" class="lado-a-lado">
                <p><i class="fa fa-heart"></i>
                <? echo number_format($serie->gosteis, 0, "", "."); ?></p>
                <span class="dot"></span>
                <p><i class="fa fa-eye"></i> <? echo number_format($serie->visualizacoes, 0, "", "."); ?></p>
            </div>
            
            <h4 id="tipo"><? echo $serie->tipo==1?"Comic":"Novel"; ?></h4>
            
            <p><? echo $serie->descricao; ?></p>
            
            <?
            $novo = ($serie->tipo==1?"comic":"novel")."/".$serie->id;
            if (isset($primeiro)) $primeiro = ($serie->tipo==1?"lerComic/":"lerNovel/").$primeiro['id'];
            
            if ($editar) {
            ?>
            
            <a href="<? echo $novo; ?>" class="botao" id="novo">Novo Capítulo <i class="fa fa-plus"></i></a>
            <?}?>
            <? if (isset($primeiro)) {?><a href="<? echo $primeiro; ?>" class="botao">Primeiro Capítulo <i class="fa fa-angle-right"></i></a><? } ?>
        </div>
        
        <div id="capitulos">
            <ul>
                <? 
                $contador = count($capitulos);
                $pagina = 1;
                foreach($capitulos as $key => $cap) { 
                    $tipo = ($serie->tipo==1?"lerComic":"lerNovel");
                    $data = strtotime($cap['data']);
                    $dia = strftime("%d", $data);
                    $mes = strftime("%B", $data);
                    $ano = strftime("%Y", $data);
                    $data = $dia." ".ucfirst(substr($mes, 0, 3)).", {$ano}";
                ?>
                <li class="capitulo" data-pagina="<? echo ceil($pagina/10); ?>" data-id="<? echo $key; ?>" style="display: none;">
                    <a href="<? echo $tipo."/".$cap['id']; ?>">
                        <header>
                             <? if ($cap['thumb_titulo']!=null) {?>
                             <img src="servidor/titulos/thumbs/<? echo $cap['thumb_titulo']; ?>" alt="">
                             <?} else if ($serie->thumb_projeto!=null) {?>
                             <img src="servidor/projetos/thumbs/<? echo $serie->thumb_projeto; ?>" alt="">
                             <?} else {?>
                             <i class="fa fa-eye-slash"></i>
                             <?}?>
                            
                            <h3><? echo $cap['nome'].($cap['rascunho']==1?"<span>Rascunho</span>":""); ?></h3>
                        </header>
                        
                        <div class="capitulo-info">
                            <span class="data"><? echo $data; ?></span>
                            <span class="gosteis"><i class="fa fa-heart"></i> <? echo number_format($cap['gosteis'], 0, "", "."); ?></span>
                            <span class="numero"><? echo "#".$contador; ?></span>
                            <? if ($editar) {?>
                            <span class="editar">
                                <i class="fa fa-edit" title="Editar Capítulo"></i>
                            </span>
                            <span class="excluir">
                                <i class="fa fa-trash" title="Excluir Capítulo"></i>
                            </span>
                            <?}?>
                        </div>
                    </a>
                </li>
                <?
                $contador--;
                } 
                ?>
            </ul>
            
            <div id="paginas">
                <? 
                $atual = "atual";
                for($i=1; $i<=ceil(count($capitulos)/10); $i++) { ?>
                <span class="<? echo $atual; ?>" data-pagina='<? echo $i ?>'><? echo $i; ?></span>
                <? 
                $atual="";
                } ?>
            </div>
        </div>
    </div>

    <div id="upload-banner" class="fundo">
        <i class="fechar fas fa-times"></i>
       
        <div>
            <h3>Mudar Banner da serie</h3>
            <form>
                <div id="upload-preview">
                    <img src="">
                    <span id="area"><img src=""><p>Somente essa parte irá aparecer na tela!</p></span>
                </div>

                

                <div id="upload-label">
                    <label for="input-imagem-banner">
                        <i class="fa fa-cloud-upload-alt"></i>
                        Clique aqui para fazer upload da imagem!
                    </label>
                    <input type="file" name="upload-banner-projeto" id="input-imagem-banner" accept="image/*">
                </div>
                
                <p class="aviso">Use imagens com a resolução de 1350 x 325.</p>

                <button class="botao canto" disabled>Atualizar</button>
            </form>
        </div>
    </div>
    
    <? include("../paginas/rodape.php") ?>
</body>
<script>
    var serie = <? echo json_encode($serie->toArray()); ?>;
    var capitulos = <? echo json_encode($capitulos); ?>;
    var seguir = <? echo json_encode($seguir); ?>;
    var ultimaPagina = <? echo ceil(count($capitulos)/10); ?>;
    var editar = <? echo $editar?1:0; ?>;
</script>
<script src="js/usuario/serie.js?<? echo time(); ?>" async></script>

</html>