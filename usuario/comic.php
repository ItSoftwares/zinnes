<?
//echo $_GET['serie'];
//echo $_GET['id'];
//var_dump($_GET);
//exit;
require "../php/database/conexao.php";
require "../php/classes/serie.class.php";
require "../php/classes/titulo.class.php";
require "../php/util/sessao.php";
require "../php/util/listarArquivos.php";
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
    
    if ($serie->tipo==2) {
        $_SESSION['erro_msg'] = "Função inválida!";
        header("Location: /serie/{$serie->id}");
        exit;
    }
} else {
    $_SESSION['erro_msg'] = "Escolha uma serie para criar um novo capítulo!";
    header("Location: /dashboard");
    exit;
}

$arquivos = array('informacoes'=>[]);

if ($_GET['id']!="") {
    $comic = $_GET['id'];
    $comic = DBselect('titulo', "where id='{$comic}'");

    if (!isset($comic)) {
        $_SESSION['erro_msg'] = "Esse Capítulo não existe!";
        header("Location: /serie/".$serie->id);
        exit;
    }
    
    $comic = new Titulo($comic[0]);
    
    $diretorio = realpath(dirname(__DIR__).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."titulos".DIRECTORY_SEPARATOR."comics".DIRECTORY_SEPARATOR.$comic->id);
    
    if ($diretorio!="" and is_dir($diretorio)) $arquivos = listar($diretorio);
}

if (!isset($comic)) $comic = new Titulo();
?>
<!DOCTYPE HTML>
<html>

<head>
    <title><? echo $comic->nome==""?"Gerenciar Comic":$comic->nome; ?> | ZINNES</title>
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
    
    <div class="box-window" id="comic">
        <a href="serie/<? echo $serie->id; ?>" class="botao linha"><i class="fa fa-angle-left"></i> Volta para serie</a>
        
        <header id="cabecalho">
            <h2>Serie: <span><? echo $serie->nome; ?></span></h2>
            <!-- <p>Ultima alteração feita em 25 de Fev, 2018 às 15:39</p> -->
            
            <div>
                <button class="botao linha salvar" disabled>Salvar<i class="fa fa-archive"></i></button>
                <button class="botao linha agendar" disabled>Agendar<i class="fa fa-calendar-alt"></i></button>
                <button class="botao publicar-agora">Publicar<i class="fa fa-check"></i></button>
            </div>
        </header>
        
        <article>
            <div class="input linha">
                <input type="text" name="titulo" id="titulo" placeholder="Título" required maxlength="100" value="<? echo $comic->nome; ?>">
            </div>
            
            <div id="arquivos">
                <header>
                    <span class="numero">#</span>
                    <span class="nome">Nome</span>
                    <!-- <span class="tamanho">Tamanho</span> -->
                    <span class="estado">Estado</span>
                    <span class="acao"></span>
                </header>
                
                <ul id="arquivos-conteudo">
                   <? //for($i=0; $i<10; $i++) {?>
                    <!-- 
                    <li class="arquivo">
                        <div class="barra-progresso"></div>
                        <span class="numero">1</span>
                        <span class="nome">Arquivo.png</span>
                        <span class="tamanho">256kb</span>
                        <span class="estado">Enviada</span>
                        <span class="acao"><i class="fa fa-trash"></i></span>
                    </li> -->

                    <? //} ?>
                    <li class="sem-arquivos">
                        <p class="aviso aviso-1"><b>Sem arquivos.</b> Clique no botão abaixo para adicionar</p>
                        <p class="aviso aviso-2"><b>Tamanho máximo</b>: 940x4000 e 2Mb por imagem</p>
                    </li>
                </ul>
                
                <footer>
                    <label class="botao canto" for="upload-paginas-comic">Carregar arquivos<i class="fa fa-cloud-upload-alt"></i></label>
                    <input type="file" id="upload-paginas-comic" name="upload-paginas-comic" style="display: none" multiple>
                    <!-- <div id="paginas"><b>0</b>/ 40</div> -->
                </footer>
            </div>
        </article>
    </div>
    
    <div id="info-capitulo">
        <div class="box-window">
            <h2>Info do capítulo</h2>
            
            <div>
                <form id="enviar-comic">
                    <input type="hidden" name="nome" value="<? echo $comic->nome?>">

                    <div class="upload-thumb">
                        <div class="upload-thumb-label">
                            <?
                            if ($comic->thumb_titulo==null) {
                            ?>
                            <i class="fa fa-eye-slash"></i>
                            <?} else {?>
                            <img src="servidor/titulos/thumbs/<? echo $comic->thumb_titulo; ?>">
                            <?}?>
                            <label for="upload-thumbnail">
                                Enviar Imagem
                                <br>
                                <i class="fa fa-cloud-upload-alt"></i>
                            </label>
                            <input type="file" id="upload-thumbnail" name="thumb_titulo">
                        </div>

                        <!-- <div> -->
                            <p>Escolha uma imagem PNG ou JPG, de 200x200, de até 2MB, para usar como thumbnail do capítulo. (Imagem Opcional)</p>
                        <!-- </div> -->
                    </div>
                    <span class="separator"></span>
                    <div class="input normal metade">
                        <label>Descrição (opcional)</label>
                        <textarea name="descricao" maxlength="300" placeholder="Escreva um pouco sobre este novo capítulo"><? echo $comic->descricao; ?></textarea>
                    </div>
                </form>
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

    <section id="visualizar-imagem" class="fundo">
        <i class="fa fa-times fechar"></i>

        <!-- <i class="fa fa-angle-left setas" id="anterior"></i> -->
        <img src="">
        <!-- <i class="fa fa-angle-right setas" id="proximo"></i> -->
    </section>
    
    <? 
    include("../paginas/rodape.php");
    $comic = $comic->estaDeclarado("id")?$comic->toArray():[];
    ?>
</body>
<script>
    var arquivos = <? echo json_encode($arquivos); ?>;
    var comic = <? echo json_encode($comic); ?>;
    var serie = <? echo json_encode($serie->toArray()); ?>;
</script>
<script src="js/usuario/comic.js?<? echo time(); ?>" async></script>
<script src="js/geral/calendario.js?<? echo time(); ?>" async></script>

</html>