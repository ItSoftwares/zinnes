<head>
    <meta name="google-signin-client_id" content="516384381165-naa5srog01g0gcq3gpkkj2hd16tffd79.apps.googleusercontent.com">
</head>
<script src="https://apis.google.com/js/platform.js" async defer></script>
<section id="container-login">
    <i class="fechar fas fa-times"></i>
   
    <div id="painel">
        <div id="esquerda">
            <div id="fundo"></div> <img src="img/logo.png" id="logo-icon">
            <h3>Bem-vindo</h3>
            <p>Venha e junte-se a nós</p>
            <ul>
                <li>Simples para publicar</li>
                <li>Rápido feedback</li>
                <li>Compartilhe com o mundo</li>
            </ul>
        </div>
        <div id="direita">
            <div id="atalhos">
                <label id="label-login" class="marcado">Login</label> <span>/</span>
                <label id="label-cadastro">Cadastro</label>
            </div>
            <div id="formularios">
                <form id="login" class="show">
                    <div class="input linha">
                        <input type="email" name="email" placeholder="Seu Email" required> <i class="fa fa-envelope"></i> </div>
                    <div class="input linha">
                        <input type="password" name="senha" placeholder="Senha" required> <i class="fa fa-key"></i> </div>
                    <button class="botao">Logar</button>
                </form>
                <form id="cadastro" class="">
                    <div class="input linha">
                        <input type="text" name="nome" placeholder="Seu Nome" required> <i class="fa fa-user"></i> </div>
                    <div class="input linha">
                        <input type="text" name="nickname" placeholder="Nickname" required> <i class="fa fa-tag"></i> </div>
                    <div class="input linha">
                        <input type="email" name="email" placeholder="Email" required> <i class="fa fa-envelope"></i> </div>
                    <div class="input linha">
                        <input type="password" name="senha" placeholder="Senha" required minlength="8"> <i class="fa fa-key"></i> </div>
                    <div class="input linha">
                        <input type="password" id="repetir-senha" placeholder="Repita a Senha" required minlength="8"> <i class="fa fa-key"></i> </div>
                    <button class="botao">Cadastrar</button>
                </form>
            </div>
            <div id="login-redes">
                <p>Ou logue usando:</p>
                <ul>
                    <li id="login-facebook"><i class="fab fa-facebook-f"></i></li>
                    <li id="login-google" class="" data-onsuccess="onSignIn"><i class="fab fa-google"></i></li>
                </ul>
            </div>
        </div>
    </div>
    <div id="esqueci">Esqueceu a <b>Senha?</b></div>
</section>
<script src="js/geral/loginCadastro.js?<? echo time(); ?>" async></script>