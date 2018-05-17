<?
require "../database/conexao.php";
require "../classes/usuario.class.php";
require "../util/listarArquivos.php";
require "../vendor/autoload.php";

if (isset($_POST['funcao'])) {
    if (!isset($_SESSION)) session_start();
    $dados = $_POST;
    $arquivos = $_FILES;
    $funcao = $dados['funcao']; unset($dados['funcao']);
    $id_usuario = isset($dados['id_usuario'])?$dados['id_usuario']:0;
    $usuario_sessao = isset($_SESSION['usuario'])?unserialize($_SESSION['usuario']):0;
    $usuario_moderador = isset($_SESSION['usuario_moderador'])?unserialize($_SESSION['usuario_moderador']):0;
    
    if ($funcao=="cadastro") {
        $usuario = new Usuario($dados);
        $result = $usuario->cadastrar();
        
        echo json_encode($result);
        exit;
    } 
    else if ($funcao=="login") {
        $usuario = new Usuario($dados);
        $result = $usuario->login();
        
        echo json_encode($result);
        exit;
    } 
    else if ($funcao=="facebook") {
        $usuario = new Usuario($dados);
        $result = $usuario->apiFacebook();
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="google") {
        $usuario = new Usuario($dados);
        $result = $usuario->apiGoogle();
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="atualizar") {
        $temp = unserialize($_SESSION['perfil_editado']);
        $user = unserialize($_SESSION['usuario']);
        
        foreach($dados as $key => $value) {
            if ($value==$temp->$key and $key!="id") {
                unset($dados[$key]);
            }
        }
        
        $dados['foto_perfil'] = $temp->foto_perfil;
        
        $usuario = new Usuario($dados);
        $result = $usuario->atualizar($arquivos);
        
        foreach($result['atualizado'] as $key => $value) {
            $temp->$key = $value;
        }
        
        $_SESSION['perfil_editado'] = serialize($temp);
        
        if ($user->id==$temp->id) {
            $_SESSION['usuario'] = serialize($temp);
        } else if (is_object($usuario_moderador)) {
            $usuario->novoLogModerador(array(
                'descricao' => "O moderador <b>{$usuario_moderador->nickname}</b> atualizou informações em seu perfil!",
                'id_moderador' => $user->id,
                'id_usuario' => $temp->id
            ));
        }
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="confirmar-conta") {
        $url = "www.zinnes.com.br/confirmarConta?id={$usuario_sessao->id}&senha={$usuario_sessao->senha}&funcao=confirmar-conta";

        $result = $usuario_sessao->mensagem("Muito bem, sua conta está quase completa. Para validar sua conta acesse o link abaixo diretamente ou copie e cole em um navegador! <br><br> <a href='{$url}' target='_BLANK'>Clique aqui</a> <br><br> {$url}");
        
        if ($result==2) {
            $result = [];
            $result['estado']=1;
            $result['mensagem'] = "Acabamos de enviar um email para <b>{$usuario_sessao->email}</b>, aguarde-o e acesso o link para confirmar sua conta!";
        }

        echo json_encode($result);
        exit;
    }
    else if ($funcao=="pegarNotificacoes") {
        $usuario = unserialize($_SESSION['usuario']);
        
        $result = $usuario->pegarNotificacoes($dados['ultimaData']);
        
        $result['estado'] = 1;
        
        echo json_encode($result);
    }
    else if ($funcao=="atualizarPin") {
        $usuario = new Usuario($dados);
        $result = $usuario->atualizarPin();
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="moderador") {
        $usuario = new Usuario($dados);
        $result = $usuario->moderador();

        $resultado = $result['moderador']?"Moderador":"Usuario";
        $usuario->novoLogModerador(array(
            'descricao' => "O Administrador <b>{$usuario->nickname}</b> trocou suas diretrizes para {$resultado}!</b>!",
            'id_moderador' => $usuario_moderador->id,
            'id_usuario' => $dados['id']
        ));
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="removerSlide") {
        $usuario = new Usuario($dados);
        $result = $usuario->removerSlide();
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="adicionarSlide") {
        // var_dump($dados); exit;
        $usuario = new Usuario($dados);
        $result = $usuario->adicionarSlide($arquivos['slide']);
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="atualizarLinkSlide") {
        $usuario = new Usuario($dados);
        $result = $usuario->atualizarLinkSlide();

        echo json_encode($result);
        exit;
    }
    else if ($funcao=="emailModerador") {
        $usuario = new Usuario($dados);
        $result = $usuario->emailModerador();

        echo json_encode($result);
        exit;
    }
    else if ($funcao=="comentar") {
        $usuario = new Usuario($dados);
        
        $result = $usuario->comentar();
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="atualizar-comentario") {
        $nickname = $dados['nickname']; unset($dados['nickname']);
        $de = $dados['de']; unset($dados['de']);
        // var_dump($dados); exit;
        $usuario = new Usuario($dados);
        
        $result = $usuario->atualizarComentario();

        if (is_object($usuario_moderador) and $usuario_moderador->id!=$id_usuario) {
            $usuario_sessao->novoLogModerador(array(
                'descricao' => "O moderador <b>{$usuario_moderador->usuario_sessao}</b> alterou o comentario <b>#{$usuario->id}</b> no perfil do usuario <b>{$nickname}</b>!",
                'id_moderador' => $usuario_sessao->id,
                'id_usuario' =>$de
            ));
        }
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="apagar-comentario") {
        $usuario = new Usuario($dados);
        
        $result = $usuario->apagarComentario();

        if (is_object($usuario_moderador) and $usuario_moderador->id!=$usuario->de) {
            $usuario_sessao->novoLogModerador(array(
                'descricao' => "O moderador <b>{$usuario_sessao->nickname}</b> excluiu seu comentario no mural do <b>#{$usuario->para}</b>: '{$usuario->comentario}'!",
                'id_moderador' => $usuario_sessao->id,
                'id_usuario' => $usuario->de
            ));
        }
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="pegar-comentarios") {
        $usuario = new Usuario($dados);
        
        $result = $usuario->pegarComentarios();
        
        echo json_encode($result);
        exit;
    } 
    else if ($funcao=="pesquisar") {
        $usuario = new Usuario();
        $result = $usuario->pesquisar($dados['nickname']);
        
        echo json_encode($result);
        exit;
    } 
    else if ($funcao=="recuperarSenha") {
        $usuario = new Usuario($dados);
        $result = $usuario->recuperarSenha();
        
        echo json_encode($result);
        exit;
    } 
} 
else if (isset($_GET)) {
    // var_dump($_GET);
    $dados = $_GET;
    $funcao = $dados['funcao']; unset($dados['funcao']);
    if (!isset($_SESSION)) session_start();
    $usuario_sessao = isset($_SESSION['usuario'])?unserialize($_SESSION['usuario']):0;

    if ($funcao=="confirmar-conta") {
        $usuario = new Usuario($dados);
        $result = $usuario->confirmarConta();

        if (isset($result['estado']) and $result['estado']==1) {
            if ($usuario_sessao!=0) {
                $usuario_sessao->confirmado = 1;
                $_SESSION['usuario'] = serialize($usuario_sessao);

                $_SESSION['conf_msg'] = $result['mensagem'];

            }
            header("location: /dashboard");
        } else echo $result;
    }
}
else {
    echo json_encode(array(
        'estado' => 2,
        'mensagem' => "Post inexistente"
    ));
    exit;
}
?>