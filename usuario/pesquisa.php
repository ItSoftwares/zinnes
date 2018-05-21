<?
require "../php/database/conexao.php";
require "../php/classes/serie.class.php";
require "../php/classes/usuario.class.php";
require "../php/classes/titulo.class.php";

session_start();

if ($_GET['query']=="") {
    $pesquisa = "";
} else $pesquisa = $_GET['query'];

if ($_GET['tipo']!="") {
    $tipo = $_GET['tipo'];
}
else {
    $tipo = "serie";
}

$pagina = $_GET['pagina']!=""?$_GET['pagina']:1;

if ($tipo=="serie") {
    $serie = new Serie();
    $resultados = $serie->pesquisar($pesquisa, ($pagina-1)*20, 0);
} else if ($tipo=="comic" or $tipo=="novel") {
    $serie = new Serie();
    $resultados = $serie->pesquisar($pesquisa, ($pagina-1)*20, $tipo=="comic"?1:2);
} else if ($tipo=="autor") {
    $usuario = new Usuario();
    $resultados = $usuario->pesquisar($pesquisa, ($pagina-1)*20);
} else {
    $_SESSION['erro_msg'] = "Tipo de conteúdo inválido!";
    header("Location: /pesquisa/serie/{$pagina}/{$pesquisa}");
    exit;
}

$quantidade = $resultados['quantidade'];
$resultados = $resultados['resultados'];   
$resultados = $resultados==null?[]:$resultados;   

if ($pagina>1 and count($resultados)==0) {
    $_SESSION['erro_msg'] = "Página inválida!";
    header("Location: /pesquisa/{$tipo}/{$pagina}/{$pesquisa}");
    exit;
} 

$generos = DBselect("genero");

$temp = [];
foreach($generos as $g) {
    $temp[$g['id']] = $g['nome'];
}

$generos = $temp;

$moderador = 0;
// Verificar se esta escolhendo moderador
if (isset($_SESSION['moderador'])) {
    $moderador = 1;
} 
?>
<!DOCTYPE HTML>
<html> 
    <head>
        <title>Pesquisa | ZINNES</title>
        <link rel="icon" type="image/png" href="img/logo.png" />
        <base href="https://<? echo $_SERVER['SERVER_NAME']; ?>/">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/usuario/pesquisa.css" media="(min-width: 1000px)">
        <link rel="stylesheet" type="text/css" href="css/geral/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" type="text/css" href="cssmobile/usuario/pesquisa.css" media="(max-width: 999px)">
        <link rel="stylesheet" type="text/css" href="cssmobile/geral/geral.css" media="(max-width: 999px)">
        <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    </head>
    
    <body>
        <? include("../paginas/header.php") ?>
        
        <div class="box-window">
            <a href="" class="botao linha"><i class="fa fa-angle-left"></i> Inicio</a>
       
            <section id="pesquisa">
                <h2>Resultados (<? echo $quantidade ?>)</h2>
                
                <div class="input normal">
                    <input type="text" placeholder="Digite sua pesquisa" value="<? echo $pesquisa ?>">
                    <i class="fa fa-search"></i>
                </div>
                
                <ul id="abas">
                    <li class="<? echo $tipo=="serie"?"selecionado":""; ?> serie"><a href="">Series</a></li>
                    <li class="<? echo $tipo=="comic"?"selecionado":""; ?> comic"><a href="">Comic</a></li>
                    <li class="<? echo $tipo=="novel"?"selecionado":""; ?> novel"><a href="">Novel</a></li>
                    <li class="<? echo $tipo=="autor"?"selecionado":""; ?> usuario"><a href="">Autor</a></li>
                </ul>
                
                <ul id="resultados">
                    <?
                    // RESULTADOS POR TITULO
                    if (false) {
                        foreach ($resultados as $key => $result) {
                            $serie = new Serie($result);
                    ?>
                    <li class="lista-grande">
                        <a href="/<? echo "serie/".$serie->id; ?>">
                            <?
                            if ($serie->thumb_projeto!=null) {
                            ?>
                            <img src="servidor/projetos/thumbs/<? echo $serie->thumb_projeto; ?>" alt="">
                            <?} else {
                            ?>
                            <img src="img/sem-foto.png" alt="">
                            <?}?>
                             
                            <div>
                                <h4><? echo $serie->nome; ?></h4>
                                
                                <p>Serie <b><? echo $serie->serie; ?></b>, autor <b><? echo $serie->nickname; ?></b></p>
                                
                                <p class="descricao"><? echo substr($serie->descricao, 0, 50); ?></p>
                                
                                <p class="genero"><i class="fa fa-tag"></i> <? echo $generos[$serie->id_genero]; ?></p>
                            </div>
                        </a>
                    </li>
                    <?
                        }
                    } 
                    // RESULTADOS PARA SERIE
                    else if ($tipo=="novel" or $tipo=="comic" or $tipo=="serie") {
                        foreach ($resultados as $key => $result) {
                            $serie = new Serie($result);
                    ?>
                    <li class="lista-grande">
                        <a href="/serie/<? echo $serie->id; ?>">
                            <?
                            if ($serie->thumb_projeto!=null) {
                            ?>
                            <img src="servidor/projetos/thumbs/<? echo $serie->thumb_projeto; ?>" alt="">
                            <?} else {
                            ?>
                            <img src="img/sem-foto.png" alt="">
                            <?}?>
                             
                            <div>
                                <h4><? echo $serie->nome; ?></h4>
                                
                                <p>Autor <b><? echo $serie->nickname; ?></b></p>
                                
                                <p class="descricao"><? echo substr($serie->descricao, 0, 50); ?></p>
                                
                                <p class="genero"><i class="fa fa-tag"></i> <? echo $generos[$serie->id_genero]; ?></p>
                            </div>
                        </a>
                    </li>
                    <?
                        }
                    }
                    // RESULTADOS POR USUARIO
                    else {
                        foreach ($resultados as $key => $result) {
                            $usuario = new $usuario($result);
                    ?>
                    <li class="lista-grande usuario">
                        <a href="/perfil/<? echo $usuario->nickname; ?>" data-id="<? echo $usuario->id; ?>">
                            <?
                            if ($usuario->foto_perfil!=null) {
                            ?>
                            <img src="servidor/thumbs-usuarios/<? echo $usuario->foto_perfil; ?>" alt="">
                            <?
                            } else {
                            ?>
                            <img src="img/sem-foto.png" alt="">
                            <?}?>
                             
                            <div>
                                <h4><? echo $usuario->nickname; ?></h4>
                                
                                <p class="descricao"><? echo substr($usuario->descricao, 0, 50); ?></p>
                            </div>
                        </a>
                    </li>
                    <?
                        }
                    }

                    if (count($resultados)==0) {
                        echo "<p class='aviso'>Nenhum resultado encontrado para \"{$pesquisa}\"</p>";
                    }
                    ?>
                </ul>
                
                <div id="paginas">
                    <button class="botao canto" disabled id="anterior">Voltar</button>
                    
                    <button class="botao canto" disabled id="proxima">Próxima</button>
                </div>
            </section>
        </div>
        
<!--        <div class="popup popup-info">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</div>-->
<!--        <div class="popup popup-erro">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</div>-->
<!--        <div class="popup popup-conf">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</div>-->
        
        <? include("../paginas/rodape.php") ?>
    </body>
    <script type="text/javascript">
        var tipo = '<? echo $tipo; ?>';
        var pesquisa = '<? echo $pesquisa; ?>';
        var qtd = <? echo $quantidade; ?>;
        var pagina = '<? echo $pagina; ?>';
        var moderador = '<? echo $moderador; ?>';
        var resultados = <? echo json_encode($resultados); ?>;
    </script>
    <script src="js/usuario/pesquisa.js?<? echo time(); ?>" async></script>
</html>