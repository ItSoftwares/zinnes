<?
//echo $_GET['nome'];
//$menu_style = "transparente";
require "../php/database/conexao.php";
require "../php/classes/usuario.class.php";
require "../php/util/sessao.php";
verificarSeSessaoExpirou();

if ($_GET['nickname']!="") {
    if ($_SESSION['tipo_usuario']<2) {
        $_SESSION['erro_msg'] = "Você não tem autorização para acessar essa página!";
        session_write_close();
        header("location: dashboard");
    }
    $nickname = $_GET['nickname'];
    $usuario = new Usuario();
    $usuario->carregar(null, $nickname);

    if (!$usuario->carregar(null, $nickname)) {
        $_SESSION['erro_msg'] = "Usuario não existe!";
        header("Location: dashboard");
        exit;
    }

    $tempUsuario = $usuario->toArray();
}
// echo "<pre>"; var_dump($_SESSION); exit;
$generos = DBselect("genero", "order by nome ASC");
?>
<!DOCTYPE HTML>
<html>

<head>
    <title><? echo isset($nickname)?$nickname:"Series"; ?> | ZINNES</title>
    <link rel="icon" type="image/png" href="img/logo.png" />
    <base href="https://<? echo $_SERVER['SERVER_NAME']; ?>/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/usuario/projetos.css" media="(min-width: 1000px)">
    <link rel="stylesheet" type="text/css" href="css/geral/geral.css" media="(min-width: 1000px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/usuario/projetos.css" media="(max-width: 999px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/geral/geral.css" media="(max-width: 999px)">
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body> 
    <? 
    include("../paginas/header.php");
    $usuario_oficial = new Usuario($usuario->toArray());

    if ($_GET['nickname']!="" and (!isset($_SESSION['usuario']) or $_GET['nickname']!=$usuario->nickname)) {
        $usuario = new Usuario($tempUsuario);
    }
     
    $series = DBselect("projeto p", "where id_usuario = {$usuario->id} order by nome ASC", "p.*,
    (select COUNT(id_usuario) from seguir_projeto where id_projeto=p.id) seguidores, 
    
    (select COUNT(id_titulo) from avaliar_titulo where id_titulo in (select id from titulo where id_projeto=p.id)) gosteis,
    
    (select COUNT(id) from titulo t where t.id_projeto = p.id) capitulos,
    
    (select SUM(visualizacoes) from titulo where id_projeto=p.id) visualizacoes");
    
//    echo count($series);
    ?>
    
    <div class="box-window" id="series">
        <a href="perfil<? echo $_SESSION['tipo_usuario']>=2?"/".$usuario->nickname:"" ?>" class="botao linha"><i class="fa fa-angle-left"></i> Ver perfil</a>
        
        <h2>Series</h2>
        <header id="cabecalho">
            <div class="input normal">
                <select id="tipo">
                    <option value="0">Tudo</option>
                    <option value="1">Comic</option>
                    <option value="2">Novel</option>
                </select>
            </div>
            
            <ul class="grupo-opcoes">
                <li class="principal"><i class="fa fa-plus"></i> Nova Serie</li>
                <div class="opcoes">
                    <li class="opcao" id="nova-comic" data-tipo="comic"><a href="#"><i class="fa fa-th"></i> Comic</a></li>
                    <li class="opcao" id="nova-novel" data-tipo="novel"><a href="#"><i class="fa fa-file-alt"></i> Novel</a></li>
                </div>
            </ul>
        </header>
        
        <article>
            <p class="aviso">Nenhuma serie criada</p>
            <ul>
                <? if ($usuario->confirmado==0 and !isset($nickname)) {?>

                <button class="botao" id="enviar-confirmacao">ENVIAR CONFIRMAÇÃO</button>

                <? } else if (isset($nickname)) {?>

                <p class="aviso">Este usuário não confirmou a conta ainda!</p>

                <? } ?>
            </ul>
        </article>
    </div>
    
    <section id="form-serie" class="fundo">
        <i class="fa fa-times fechar"></i>
        
        <div>
            <h3>Editar Serie</h3>
            
            <form>
                <div class="campos">
                    <div class="input linha">
                        <label>Nome da Serie</label>
                        <input type="text" name="nome" placeholder="Nome da serie" required>
                    </div>
                    <div class="input linha metade">
                        <label>Tipo</label>
                        <select name="tipo">
                            <option value="0">Tipo</option>
                            <option value="1">Comic</option>
                            <option value="2">Novel</option>
                        </select>
                    </div>
                    <div class="input linha metade">
                        <label>Gênero</label>
                        <select name="id_genero">
                            <option value="0">Gênero</option>
                            <? foreach ($generos as $g) {?>
                            <option value="<? echo $g['id']; ?>"><? echo $g['nome']; ?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="input linha">
                        <label>Descrição</label>
                        <textarea name="descricao" placeholder="Escreva algo sobre sua serie" required maxlength="300"></textarea>
                    </div>
                    <div class="input linha">
                        <label>Tags</label>
                        <input type="text" id="tags" placeholder="Tags, pressione Enter para adicionar" maxlength="15">
                        <input type="hidden" name="tags">
                    </div>
                    <ul id="lista-tags">
                    </ul>
                    
                    <input type="hidden" name="id_usuario" value="<? echo $usuario->id; ?>">
                </div>
                
                <button class="botao canto">Salvar <i class="fa fa-save"></i></button>
            </form>
        </div>
    </section>
    
    <? include("../paginas/rodape.php") ?>
</body>
<script>
    var series = <? echo json_encode($series); ?>;
</script>
<script src="js/usuario/dashboard.js?<? echo time(); ?>" async></script>

</html>