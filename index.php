<?
require "php/database/conexao.php";
require "php/classes/titulo.class.php";
require "php/classes/serie.class.php";
require "php/classes/usuario.class.php";
require "php/util/listarArquivos.php";
// exit;
$diretorio = realpath("servidor".DIRECTORY_SEPARATOR."slides");
if ($diretorio!="" and is_dir($diretorio)) $slides = listar($diretorio)['nomes'];
$links_slides = DBselect("slides");

$ultimos = DBselect("titulo t INNER JOIN projeto p ON t.id_projeto = p.id INNER JOIN genero g ON g.id = p.id_genero", "where rascunho=0 order by data DESC limit 8", "t.*, p.tipo, p.thumb_projeto, (select COUNT(id_usuario) from avaliar_titulo where id_titulo = t.id) gosteis, p.descricao as projeto_descricao, g.nome as genero");

// $vistos = DBselect("titulo t INNER JOIN projeto p ON t.id_projeto = p.id", "where rascunho=0 order by visualizacoes DESC limit 7", "t.*, p.tipo, p.thumb_projeto, p.descricao as projeto_descricao");
$vistos = DBselect("(select * from titulo where rascunho=0 order by visualizacoes DESC limit 20) t INNER JOIN projeto p ON t.id_projeto = p.id", "order by RAND() limit 7", "t.*, p.tipo, p.thumb_projeto, p.descricao as projeto_descricao");

$series_por_genero = DBselect("projeto p INNER JOIN genero g ON p.id_genero = g.id", "group by id_genero order by visualizacoes DESC limit 8", "p.*, (select SUM(visualizacoes) from titulo where titulo.id_projeto = p.id) visualizacoes, g.nome as genero");

$top = DBselect("projeto p INNER JOIN genero g ON p.id_genero = g.id", "order by seguidores DESC limit 8", "(select COUNT(id_usuario) from seguir_projeto where id_projeto = p.id) seguidores, p.*, g.nome as genero");

$publishers = DBselect("usuario u", "order by seguidores DESC, projetos DESC limit 8", "u.nickname, u.foto_perfil, u.id, (select COUNT(id_usuario) from seguir_projeto s where id_projeto in (select id from projeto where id_usuario=u.id)) seguidores, (select COUNT(id) from projeto where id_usuario = u.id) projetos");

// $publishers = array_merge($publishers, $publishers);
$menu_style = "transparente";
?>
<!DOCTYPE HTML>
<html> 
    <head>
        <title>ZINNES - Home</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="img/logo.png" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Zinnes - Este nome faz referência às “fanzines”, que são publicações independentes produzidas por entusiastas de determinadas subculturas, como um gênero literário ou musical, para o prazer de outros que compartilham dos mesmos interesses, visando a criação de redes de pessoas interessadas nos assuntos abordados. Essa plataforma tem o mesmo objetivo: ser o elo entre os amantes das HQs e das novels, popularizando obras autorais e enriquecendo estes gêneros literários. Seja bem-vindo ao mundo da ficção!" />
        <meta name="keywords" content="zinnes, Zinnes, ZINNES, zines, Zines, ZINES, quadrinhos, autoral, histórias, hqs, hq, Desenhista, quadrinista, ilustrador, editora, revista, história, cartum, cartunista, hq, quadrinhos, gibi, séries, novels, romances, literatura, publisher, autores, autorais, caricatura, fotonovelas, mangás, cartoon, comic, comics, desenho, gravuras, imagem, ilustração, ficção, ficcionista, kawaii, entretenimento, plataforma, escritor, fã, histórias em quadrinhos, arte dos quadrinhos,"/>
        <meta name="robots" content="index, follow">
        <link rel="stylesheet" type="text/css" href="css/index.css" media="(min-width: 1000px)">
        <link rel="stylesheet" type="text/css" href="css/geral/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" type="text/css" href="cssmobile/index.css" media="(max-width: 999px)">
        <link rel="stylesheet" type="text/css" href="cssmobile/geral/geral.css" media="(max-width: 999px)">
        <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    </head>
    
    <body>
        <? include("paginas/header.php") ?>

        <div id="slides">
            <i class="fa fa-angle-double-left seta esquerda"></i>
            <i class="fa fa-angle-double-right seta direita"></i>
            <ul> 
                <? 
                foreach ($slides as $key => $value) {
                ?>
                <li data-slide='<? echo $key+1; ?>'>
                    <a href="<? echo $links_slides[$key]['link'] ?>" target="_BLANK">
                        <img src="/servidor/slides/<? echo $value ?>">
                        <div class="sombra">
                        	<img src="/servidor/slides/<? echo $value ?>">
                        </div>
                    </a>
                </li>
                <?}?>
            </ul>

            <div id="slide-control">
                <? 
                $cont = 1;
                foreach ($slides as $value) {
                    echo "<span data-slide='{$cont}'></span>";
                    $cont++;
                } ?>
            </div>
        </div>

        <div id="zinnes">
            <!-- <img src="img/logo.png" alt=""> -->
            <h1>Bem-vindo à ZINNES. A sua plataforma de publicação de obras autorais</h1>
        </div>

        <div id="main">
            <section id="ultimos" class="box-window horizontal fade">
                <h2>Mais recentes</h2>

                <ul>
                    <? foreach ($ultimos as $i => $value) {
                    $titulo = new Titulo($value);
                    ?>
                    <li <? echo ($i==0 or $i==7)?"class='maior'":"" ?>>
                        <a href="/<? echo ($titulo->tipo==1?"lerComic":"lerNovel")."/{$titulo->id}" ?>">
                            <? if ($titulo->thumb_titulo!=null) {?>
                            <img src="servidor/titulos/thumbs/<? echo $titulo->thumb_titulo ?>">
                            <? } else if ($titulo->thumb_projeto!=null) {?>
                            <img src="servidor/projetos/thumbs/<? echo $titulo->thumb_projeto ?>">
                            <? } else { ?>
                            <img src="img/sem-foto.png">
                            <? } ?>
                            <div class="label">
                                <? 
                                $nome = ($i==0 or $i==7)?$titulo->nome:(substr($titulo->nome, 0, 20).(strlen($titulo->nome)>20?"...":"")); 
                                $descricao = ($titulo->descricao=="" or $titulo->descricao==null)?$titulo->projeto_descricao:$titulo->descricao;
                                ?>
                                <h3 class="titulo"><? echo $nome ?></h3>
                                <p class="descricao"><? echo substr($descricao, 0, 150)."..." ?></p>
                                <p class="genero contador"><i class="fa fa-tag"></i> <span><? echo $titulo->genero ?></span></p>
                            </div>
                        </a>
                    </li>
                    <?
                    } ?>
                </ul>
            </section>

            <hr>

            <section id="mais-vistos" class="box-window horizontal fade">
                <h2>Mais Vistos</h2>

                <ul>
                    <? foreach ($vistos as $i => $value) {
                    $titulo = new Titulo($value);
                    ?>
                    <li <? echo ($i==0)?"class='grande'":"" ?>>
                        <a href="/<? echo ($titulo->tipo==1?"lerComic":"lerNovel")."/{$titulo->id}" ?>">
                            <? if ($titulo->thumb_titulo!=null) {?>
                            <img src="servidor/titulos/thumbs/<? echo $titulo->thumb_titulo ?>">
                            <? } else if ($titulo->thumb_projeto!=null) {?>
                            <img src="servidor/projetos/thumbs/<? echo $titulo->thumb_projeto ?>">
                            <? } else { ?>
                            <img src="img/sem-foto.png">
                            <? } ?>
                            <div class="label">
                                <? 
                                $nome = ($i==0)?$titulo->nome:(substr($titulo->nome, 0, 20).(strlen($titulo->nome)>20?"...":""));
                                $descricao = ($titulo->descricao=="" or $titulo->descricao==null)?$titulo->projeto_descricao:$titulo->descricao; 
                                $descricao = ($i!=0)?substr($descricao, 0, 150)."...":$descricao; 
                                ?>
                                <h3 class="titulo"><? echo $nome ?></h3>
                                <p class="descricao"><? echo $descricao ?></p>
                                <p class="genero contador"><i class="fa fa-eye"></i> <span><? echo number_format($titulo->visualizacoes, 0, "", "."); ?></span></p>
                            </div>
                        </a>
                    </li>
                    <?
                    } ?>
                </ul>
            </section>

            <section id="genero" class="box-window">
                <h2>Melhores do Gênero</h2>

                <ul>
                    <? foreach ($series_por_genero as $key => $value) {
                    $serie = new Serie($value);
                    ?>
                    <li>
                        <a href="/serie/<? echo $serie->id ?>">
                            <? if ($serie->thumb_projeto!=null) {?>
                            <img src="servidor/projetos/thumbs/<? echo $serie->thumb_projeto ?>">
                            <? } else { ?>
                            <img src="img/sem-foto.png">
                            <? } ?>
                            <div class="label">
                                <h3 class="titulo"><? echo $serie->nome ?></h3>
                                <p class="descricao"><? echo substr($serie->descricao, 0, 100)."..." ?></p>
                            </div>
                            <p class="genero"><i class="fa fa-tag"></i> <? echo $serie->genero ?></p>
                        </a>
                    </li>
                    <?
                    } ?>
                </ul>
            </section>

            <hr>

            <section id="top" class="box-window horizontal fade">
                <h2>TOP 10 <span>Mais Seguidos</span></h2>

                <ul>
                    <? foreach ($top as $i => $value) {
                    $serie = new Serie($value);
                    ?>
                    <li <? echo ($i==3 or $i==4)?"class='maior'":"" ?>>
                        <a href="/serie/<? echo $serie->id ?>">
                            <? if ($serie->thumb_projeto!=null) {?>
                            <img src="servidor/projetos/thumbs/<? echo $serie->thumb_projeto ?>">
                            <? } else { ?>
                            <img src="img/sem-foto.png">
                            <? } ?>
                            <div class="label">
                                <? $nome = ($i==3 or $i==4)?$serie->nome:(substr($serie->nome, 0, 20).(strlen($serie->nome)>20?"...":"")); ?>
                                <h3 class="titulo"><? echo $nome ?></h3>
                                <p class="descricao"><? echo substr($serie->descricao, 0, 150)."..."; ?></p>
                                <p class="genero contador"><i class="fa fa-tag"></i> <? echo $serie->genero ?></p>
                            </div>
                        </a>
                    </li>
                    <?
                    } ?>
                </ul>
            </section>

            <hr>

            <section id="publishers" class="box-window horizontal">
                <h2>Publishers em Alta</h2>

                <div class="flex">
                    
                    <ul>
                        <? foreach ($publishers as $i => $value) {
                            $usuario = new Usuario($value);
                            if ($usuario->projetos==0) continue;
                        ?>
                        <li>
                            <a href="/perfil/<? echo $usuario->nickname ?>">
                                <? if ($usuario->foto_perfil!=null) {?>
                                <img src="servidor/thumbs-usuarios/<? echo $usuario->foto_perfil ?>">
                                <? } else { ?>
                                <img src="img/profile-default.png" alt="">
                                <? } ?>
                                <div class="label">
                                    <h3 class="titulo"><? echo $usuario->nickname ?></h3>
                                    <p class="genero contador"><i class="fa fa-users"></i> <span><? echo $usuario->seguidores ?> Seg.</span></p>
                                </div>
                            </a>
                        </li>
                        <?
                        } ?>
                    </ul>

                    <div>
                        <!-- <h3>ZINNES</h3> -->
                        <p>Você também pode ser um publisher. É muito fácil. Comece criando uma conta em nossa plataforma.</p>

                        <button class="botao">CRIAR CONTA</button>
                    </div>
                </div>
            </section>
        </div>

        <? include("paginas/rodape.php") ?>
        <div id="subir"><i class="fa fa-chevron-up" aria-hidden="true"></i></div>
    </body>

    <script type="text/javascript">
        var slides = <? echo json_encode($slides); ?>;    
        var genero = <? echo json_encode($series_por_genero); ?>;    
        var ultimos = <? echo json_encode($ultimos); ?>;    
        var publishers = <? echo json_encode($publishers); ?>;    
    </script>
    <script src="js/index.js?<? echo time(); ?>"></script>
    <!-- <script src="https://www.cnpj.net.br/js/index.js?<? echo time(); ?>"></script> -->
</html>