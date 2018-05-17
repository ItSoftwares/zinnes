<?
//echo $_GET['serie'];
//echo $_GET['id'];
//var_dump($_GET);
//exit;
require "../php/database/conexao.php";
require "../php/classes/serie.class.php";
require "../php/classes/titulo.class.php";
require "../php/util/sessao.php";
verificarSeSessaoExpirou();

date_default_timezone_set('America/Sao_Paulo');

if ($_GET['serie']!="") {
    $id = $_GET['serie'];
    $serie = new Serie();

    if (!$serie->carregar($id)) {
        $_SESSION['erro_msg'] = "Serie não existe!";
        header("Location: /dashboard");
        exit;
    }
    
    if ($serie->tipo==1) {
        $_SESSION['erro_msg'] = "Função inválida!";
        header("Location: /serie/{$serie->id}");
        exit;
    }
} else {
    $_SESSION['erro_msg'] = "Escolha uma serie para criar um novo capítulo!";
    header("Location: /dashboard");
    exit;
}

if ($_GET['id']!="") {
    $novel = $_GET['id'];
    $novel = DBselect('titulo', "where id='{$novel}'");

    if (!isset($novel)) {
        $_SESSION['erro_msg'] = "Esse Capítulo não existe!";
        header("Location: /serie/".$serie->id);
        exit;
    }
} else {
    $_SESSION['info_msg'] = "Limite máximo de caracteres: 35 Mil!";
}

if (isset($novel)) $novel = new Titulo($novel[0]);
else $novel = new Titulo();
?>
<!DOCTYPE HTML>
<html>

<head>
    <title><? echo $novel->nome==""?"Nova Novel":$novel->nome; ?> | ZINNES</title>
    <link rel="icon" type="image/png" href="img/logo.png" />
    <base href="https://<? echo $_SERVER['SERVER_NAME']; ?>/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/usuario/novelComic.css" media="(min-width: 1000px)">
    <link rel="stylesheet" type="text/css" href="css/geral/geral.css" media="(min-width: 1000px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/usuario/novelComic.css" media="(max-width: 999px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/geral/geral.css" media="(max-width: 999px)">
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>
    <? include("../paginas/header.php") ?>
    
    <div class="box-window" id="novel">
        <a href="serie/<? echo $serie->id; ?>" class="botao linha"><i class="fa fa-angle-left"></i> Voltar pra serie</a>
        
        <header id="cabecalho">
            <h2>Serie: <span><? echo $serie->nome; ?></span></h2>
            
            <div>
                <button class="botao linha salvar" disabled>Salvar<i class="fa fa-archive"></i></button>
                <button class="botao linha agendar" disabled>Agendar<i class="fa fa-calendar-alt"></i></button>
                <button class="botao publicar-agora" disabled>Publicar<i class="fa fa-check"></i></button>
            </div>
        </header>
        
        <article>
            <div class="input linha">
                <input type="text" name="titulo" id="titulo" placeholder="Título" required maxlength="100" value="<? echo $novel->nome ?>">
            </div>
            
            <div contenteditable="true" id="texto" placeholder="Escreva sua história aqui..."><? echo $novel->texto ?></div>
            
            <div id="ui-novel" class="">
                <button id="negrito" disabled class="botao"><b>N</b></button>
                <button id="italico" style="font-style: italic" disabled class="botao">I</button>
                <button id="sublinhado" style="text-decoration: underline;" disabled class="botao">S</button>
                
                <i class="fa fa-font"></i>
            </div>
        </article>
        
        <footer class="rodape-capitulo">
            <p id="caracteres"><b>0</b> / 35,000</p>
<!--            <p id="ultima-atualizacao">Ultima alteração feita em 25 de Fev, 2018 às 15:39</p>-->
            <p id="ultima-atualizacao"></p>
        </footer>
    </div>
    
    <div id="info-capitulo">
        <div class="box-window">
            <h2>Info do capítulo</h2>
            
            <div>
                <form id="enviar-novel">
                    <input type="hidden" name="texto">
                    <input type="hidden" name="nome" value="<? echo $novel->nome?>">

                    <div class="upload-thumb">
                        <div class="upload-thumb-label">
                            <?
                            if ($novel->thumb_titulo==null) {
                            ?>
                            <i class="fa fa-eye-slash"></i>
                            <?} else {?>
                            <img src="servidor/titulos/thumbs/<? echo $novel->thumb_titulo; ?>">
                            <?}?>
                            <label for="upload-thumbnail">
                                Enviar Imagem
                                <br>
                                <i class="fa fa-cloud-upload-alt"></i>
                            </label>
                            <input type="file" id="upload-thumbnail" name="thumb_titulo">
                        </div>

        <!--                <div>-->
                            <p>Escolha uma imagem PNG ou JPG, de 200x200, de até 2MB, para usar como thumbnail do capítulo. (Imagem Opcional)</p>
        <!--                </div>-->
                    </div>
                    <span class="separator"></span>
                    <div class="input normal metade">
                        <label>Descrição (opcional)</label>
                        <textarea name="descricao" maxlength="300" placeholder="Escreva um pouco sobre este novo capítulo"><? echo $novel->descricao; ?></textarea>
                    </div>
                </form>
            </div>
            
            <div id="info-botoes">
                <button class="botao linha salvar" disabled>Salvar<i class="fa fa-archive"></i></button>
                <button class="botao linha agendar" disabled>Agendar<i class="fa fa-calendar-alt"></i></button>
                <button class="botao publicar-agora" disabled>Publicar<i class="fa fa-check"></i></button>
            </div>
        </div>
    </div>
    
    <div id="agendar-box" class="fundo">
        <i class="fechar fa fa-times"></i>
        
        <div>
            <h3>Agendar</h3>
            
            <div id="calendario">
                <header>
                    <i class="fa fa-angle-left" id="voltar-mes"></i>
                    <i class="fa fa-angle-right" id="avancar-mes"></i>
                    <div id="mes">Agosto /</div>
                    <div id="ano">2017</div>
                </header>
                <div id="nome-semana"> <span>Dom</span> <span>Seg</span> <span>Ter</span> <span>Qua</span> <span>Qui</span> <span>Sex</span> <span>Sab</span> </div>
                <article>
                    <!-- gerar dias -->
                    <? for ($i=0; $i<6; $i++) { ?>
                        <div class="semana"> <span>0</span> <span>0</span> <span>0</span> <span>0</span> <span>0</span> <span>0</span> <span>0</span> </div>
                        <? } ?>
                </article>
            </div>
            <span class="dia-escolhido">Escolha um dia</span>
            <button class="botao canto">Agendar<i class="fa fa-calendar-alt"></i></button>
        </div>
    </div>
    
    <? 
    include("../paginas/rodape.php");
    $novel = $novel->estaDeclarado("id")?$novel->toArray():[];
    ?>
</body>
<script>
    var serie = <? echo json_encode($serie->toArray()); ?>;
    var novel = <? echo json_encode($novel); ?>;
</script>
<script src="js/usuario/novel.js?<? echo time(); ?>" async></script>
<script src="js/geral/calendario.js?<? echo time(); ?>" async></script>

</html>