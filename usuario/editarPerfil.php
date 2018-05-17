<?

//$menu_style = "transparente";
require "../php/database/conexao.php";
require "../php/classes/usuario.class.php";
require "../php/util/sessao.php";
verificarSeSessaoExpirou();

if ($_GET['nickname']!="") {
    if ($_SESSION['tipo_usuario']<2) {
        $_SESSION['erro_msg'] = "Você não tem autorização para acessar essa página!";
        session_write_close();
        header("location: configuracoes");
    }
    $nickname = $_GET['nickname'];
    $usuario = new Usuario();
    $usuario->carregar(null, $nickname);

    if (!$usuario->carregar(null, $nickname)) {
        $_SESSION['erro_msg'] = "Usuario não existe!";
        header("Location: configuracoes");
        exit;
    }

    $tempUsuario = $usuario->toArray();
}
?>
<!DOCTYPE HTML>
<html>

<head>
    <title><? echo isset($nickname)?$nickname:"Configurações"; ?> | ZINNES</title>
    <link rel="icon" type="image/png" href="img/logo.png" />
    <base href="https://<? echo $_SERVER['SERVER_NAME']; ?>/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/usuario/editarPerfil.css" media="(min-width: 1000px)">
    <link rel="stylesheet" type="text/css" href="css/geral/geral.css" media="(min-width: 1000px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/usuario/editarPerfil.css" media="(max-width: 999px)">
    <link rel="stylesheet" type="text/css" href="cssmobile/geral/geral.css" media="(max-width: 999px)">
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>
    <? 
    include("../paginas/header.php");
    if ($_GET['nickname']!="" and (!isset($_SESSION['usuario']) or $_GET['nickname']!=$usuario->nickname)) {
        $usuario = new Usuario($tempUsuario);
    }
    $_SESSION['perfil_editado'] = serialize($usuario);
    ?>
    <div class="box-window">
        <a href="perfil<? echo $_SESSION['tipo_usuario']>=2?"/".$usuario->nickname:"" ?>" class="botao linha"><i class="fa fa-angle-left"></i> Ver perfil</a>
       
        <section id="editar">
            <h2>Editar Perfil</h2>
            <form>
                <aside>
                    <div id="foto-perfil">
                        <label for="input-imagem-perfil">Mudar<br>200x200</label>
                        <input type="file" name="imagem-perfil" id="input-imagem-perfil" accept="image/*">
                        <? if ($usuario->foto_perfil=="" or $usuario->foto_perfil==null) {?>
                        <img src="img/profile-default.png">
                        <?} else {?>
                        <img src="https://<? echo $_SERVER['SERVER_NAME']; ?>/servidor/thumbs-usuarios/<? echo $usuario->foto_perfil; ?>">
                        <?}?>
                    </div>
                    <?
                    if ($_SESSION['tipo_usuario']>2 and isset($nickname)) {
                        if ($usuario->estado_conta<2) {
                            ?>
                    <button class="botao" id="bloquear" type="button">Bloquear <i class="fa fa-lock"></i></button>
                            <?
                        } else {
                            if ($usuario->estado_conta==2) {?>
                    <p>Conta bloqueada até: <? echo date('d/m/Y', strtotime($usuario->dias_bloqueio)); ?></p>
                            <?
                            } else if ($usuario->estado_conta==3) {
                            ?>
                    <p>Bloqueado permanentemente!</p>
                            <? 
                            }
                            ?>
                    <button class="botao desbloquear" id="bloquear" type="button">Desbloquear <i class="fa fa-unlock"></i></button>
                            <?
                        }
                    ?>
                    
                    <?}?>
                </aside>
                
                <div id="campos">
                    <h3>Pessoal</h3>
                    <div class="campos">
                        <div class="input linha">
                            <input type="text" name="nome" placeholder="Nome" value="<? echo $usuario->nome; ?>" required>
                            <i class="fa fa-user"></i>
                        </div>
                        <div class="input linha">
                            <input type="text" name="nickname" placeholder="Nickname, todos no site verão este nome" value="<? echo $usuario->nickname; ?>" required>
                            <i class="fa fa-tag"></i>
                        </div>
                        <div class="input linha">
                            <textarea name="descricao" placeholder="Sobre mim" maxlength="500"><? echo $usuario->descricao; ?></textarea>
                            <i class="fab fa-elementor"></i>
                        </div>
                        <div class="input linha">
                            <input type="text" name="localizacao" placeholder="Localização (Pais, estado etc.)" value="<? echo $usuario->localizacao; ?>" required>
                            <i class="fa fa-map-marker-alt"></i>
                        </div>
                        <div class="input linha metade">
                            <input type="text" name="aniversario" placeholder="Aniversario" value="<? echo $usuario->aniversario; ?>" data-mask="00/00/0000">
                            <i class="fa fa-birthday-cake"></i>
                        </div>
                        <div class="input linha metade">
                            <select name="sexo">
                                <option value="0">Sexo</option>
                                <option value="1" <? echo $usuario->sexo==1?"selected":"" ?>>Homem</option>
                                <option value="2" <? echo $usuario->sexo==2?"selected":"" ?>>Mulher</option>
                            </select>
                            <!-- <i class="fa fa-birthday-cake"></i> -->
                        </div>
                    </div>
                    
                    <h3>Conta</h3>
                    <div class="campos">
                        <div class="input linha">
                            <input type="email" name="email" placeholder="Email de login" value="<? echo $usuario->email; ?>" required>
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div class="input linha metade">
                            <input type="password" name="senha" placeholder="Mudar senha" <? echo ($usuario->facebook!=null || $usuario->google!=null)?"data-api=1":""; ?>>
                            <i class="fa fa-key"></i>
                        </div>
                        <div class="input linha metade">
                            <input type="password" name="" id="repetir" placeholder="Repita a senha" <? echo ($usuario->facebook!=null || $usuario->google!=null)?"data-api=1":""; ?>>
                            <i class="fa fa-key"></i>
                        </div>
                    </div>
                    
                    <h3>Links</h3>
                    <div class="campos">
                        <div class="input linha metade">
                            <input type="url" name="link_twitter" placeholder="Twitter - https://www.example.com" value="<? echo $usuario->link_twitter; ?>">
                            <i class="fab fa-twitter"></i>
                        </div>
                        <div class="input linha metade">
                            <input type="url" name="link_facebook" placeholder="Facebook - https://www.example.com" value="<? echo $usuario->link_facebook; ?>">
                            <i class="fab fa-facebook-f"></i>
                        </div>
                        <div class="input linha metade">
                            <input type="url" name="link_instagram" placeholder="Instagram - https://www.example.com" value="<? echo $usuario->link_instagram; ?>">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <div class="input linha metade">
                            <input type="url" name="link_youtube" placeholder="Youtube - https://www.example.com" value="<? echo $usuario->link_youtube; ?>">
                            <i class="fab fa-youtube"></i>
                        </div>
                        <div class="input linha">
                            <input type="url" name="link_site" placeholder="Site - https://www.example.com" value="<? echo $usuario->link_site; ?>">
                            <i class="fa fa-link"></i>
                        </div>
                    </div>
                    
                    <h3>Patrocínio</h3>
                    <div class="campos">
                        <div class="input linha">
                            <input type="url" name="link_patreon" placeholder="Patreon - https://www.example.com"  value="<? echo $usuario->link_patreon; ?>">
                            <i class="fa fa-link"></i>
                        </div>
                        <div class="input linha">
                            <input type="url" name="link_padrim" placeholder="Padrim - https://www.example.com"  value="<? echo $usuario->link_padrim; ?>">
                            <i class="fa fa-link"></i>
                        </div>
                        <div class="input linha">
                            <input type="url" name="link_apoiase" placeholder="Apoia-se - https://www.example.com"  value="<? echo $usuario->link_apoiase; ?>">
                            <i class="fa fa-link"></i>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="id" value="<? echo $usuario->id; ?>">
                <button id="salvar" class="botao">Salvar <i class="fa fa-save"></i></button>
            </form>
        </section>
    </div>
    <? include("../paginas/rodape.php") ?>
</body>
<script>
    var perfil = <? echo json_encode($usuario->toArray()); ?>;
</script>
<script src="js/usuario/editarPerfil.js?<? echo time(); ?>" async></script>
<script src="js/geral/jquery.mask.js?<? echo time(); ?>" async></script>
</html>