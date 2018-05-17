<?
require "../database/conexao.php";
require "../classes/usuario.class.php";
require "../classes/serie.class.php";
require "../classes/titulo.class.php";
require "../vendor/autoload.php";

if (isset($_POST)) {
    if (!isset($_SESSION)) session_start();
    $dados = $_POST;
    $arquivos = $_FILES;
    $funcao = $dados['funcao']; unset($dados['funcao']);
    $usuario = new Usuario(unserialize($_SESSION['usuario']));
    $usuario_moderador = isset($_SESSION['usuario_moderador'])?unserialize($_SESSION['usuario_moderador']):0;

    if ($funcao=="nova") {
        $serie = new Serie($dados);
        $result = $serie->nova();

        if (is_object($usuario_moderador) and $usuario_moderador->id!=$serie->id_usuario) {
            $usuario->novoLogModerador(array(
                'descricao' => "O moderador <b>{$usuario->nickname}</b> adicionou uma serie em sua conta!",
                'id_moderador' => $usuario_moderador->id,
                'id_usuario' => $serie->id_usuario,
                'id_projeto' =>$serie->id
            ));
        }
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="atualizar") {
        $id_usuario = $dados['id_usuario']; unset($dados['id_usuario']);
        $serie = new Serie($dados);
        $result = $serie->atualizar($arquivos);

        if (is_object($usuario_moderador) and $usuario_moderador->id!=$id_usuario) {
            $usuario->novoLogModerador(array(
                'descricao' => "O moderador <b>{$usuario->nickname}</b> alterou informações de sua serie <b>{$serie->nome}!</b>!",
                'id_moderador' => $usuario_moderador->id,
                'id_usuario' => $id_usuario,
                'id_projeto' =>$serie->id
            ));
        }
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="seguir") {
       // var_dump($dados); exit;
        $seguir = $dados['seguir']; unset($dados['seguir']);
        $serie = new Serie($dados);
        
        if ($seguir == 1) {
            $result = $serie->deixarDeSeguir();
        } else {
            $result = $serie->seguir();
        }
        
        echo json_encode($result);
        exit;
    } 
    else if ($funcao=="excluir") {
        $serie = new Serie($dados);
        $result = $serie->excluir();

        if (is_object($usuario_moderador) and $usuario_moderador->id!=$serie->id_usuario) {
            $usuario->novoLogModerador(array(
                'descricao' => "O moderador <b>{$usuario->nickname}</b> excluiu a serie <b>{$serie->nome}!</b>!",
                'id_moderador' => $usuario_moderador->id,
                'id_usuario' => $serie->id_usuario
            ));
        }
        
        echo json_encode($result);
        exit;
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