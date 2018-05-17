<?
require "../database/conexao.php";
require "../classes/usuario.class.php";
require "../classes/serie.class.php";
require "../classes/titulo.class.php";
require "../util/listarArquivos.php";
require "../vendor/autoload.php";

if (isset($_POST)) {
    if (!isset($_SESSION)) session_start();
    $dados = $_POST;
    // var_dump($dados); exit;
    $arquivos = $_FILES;
    $funcao = $dados['funcao']; unset($dados['funcao']);
    $tipo = isset($dados['tipo'])?$dados['tipo']:0; unset($dados['tipo']);
    $usuario = isset($_SESSION['usuario'])?unserialize($_SESSION['usuario']):0;
    $usuario_moderador = isset($_SESSION['usuario_moderador'])?unserialize($_SESSION['usuario_moderador']):0;
    
    if (isset($dados['ordem'])) $dados['ordem'] = json_decode($dados['ordem'], true);
    if (isset($dados['imagens'])) $dados['imagens'] = json_decode($dados['imagens'], true);

    if ($funcao=="novo") {
        $id_usuario = $dados['id_usuario']; unset($dados['id_usuario']);
        $serie = $dados['serie']; unset($dados['serie']);
        $titulo = new Titulo($dados);
        $result = $titulo->novo($arquivos);

        if (is_object($usuario_moderador) and $usuario_moderador->id!=$id_usuario) {
            $usuario->novoLogModerador(array(
                'descricao' => "O moderador <b>{$usuario->nickname}</b> criou um capitulo em sua serie: <b>{$serie}</b>!",
                'id_moderador' => $usuario_moderador->id,
                'id_usuario' => $id_usuario,
                'id_titulo' =>$titulo->id
            ));
        }
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="atualizar") {
        $serie = $dados['serie']; unset($dados['serie']);
        $id_usuario = $dados['id_usuario']; unset($dados['id_usuario']);
        // var_dump($dados); exit;
        $titulo = new Titulo($dados);
        $result = $titulo->atualizar($arquivos);

        if (is_object($usuario_moderador) and $usuario_moderador->id!=$id_usuario) {
            $usuario->novoLogModerador(array(
                'descricao' => "O moderador <b>{$usuario->nickname}</b> atualizou o capítulo <b>{$titulo->nome}</b> da serie <b>{$serie}</b>!",
                'id_moderador' => $usuario_moderador->id,
                'id_titulo' =>$titulo->id
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
    else if ($funcao=="publicar") {
        $id_usuario = $dados['id_usuario']; unset($dados['id_usuario']);
        $serie = $dados['serie']; unset($dados['serie']);
        
        $titulo = new Titulo($dados);
        $titulo->rascunho = 0;
        $result = $titulo->atualizar($arquivos);
        $nomes = $result['nomes'];
        if ($result['estado']==1) {
            $result = $titulo->publicar();
            $result['nomes'] = $nomes;
        }
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="apagarImagem") {
        $titulo = new Titulo($dados);
        $result = $titulo->apagarImagemComic();
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="gostei") {
        $titulo = new Titulo($dados);
        
        $result = $titulo->gostei();
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="comentar") {
        // var_dump($dados); exit;
        $titulo = new Titulo($dados);
        $result = $titulo->comentar();
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="atualizar-comentario") {
        $nome = $dados['nome']; unset($dados['nome']);
        $id_usuario = $dados['id_usuario']; unset($dados['id_usuario']);
        $titulo = new Titulo($dados);
        
        $result = $titulo->atualizarComentario();

        if (is_object($usuario_moderador) and $usuario_moderador->id!=$id_usuario) {
            $usuario->novoLogModerador(array(
                'descricao' => "O moderador <b>{$usuario->nickname}</b> alterou o comentario <b>#{$titulo->id}</b> do capítulo <b>{$nome}</b>!",
                'id_moderador' => $usuario_moderador->id,
                'id_usuario' => $id_usuario,
                'id_titulo' =>$titulo->id_titulo
            ));
        }
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="apagar-comentario") {
        $nome = $dados['nome']; unset($dados['nome']);
        $id_usuario = $dados['id_usuario']; unset($dados['id_usuario']);
        $titulo = new Titulo($dados);
        
        $result = $titulo->apagarComentario();

        if (is_object($usuario_moderador) and $usuario_moderador->id!=$id_usuario) {
            $usuario->novoLogModerador(array(
                'descricao' => "O moderador <b>{$usuario->nickname}</b> excluiu o comentario <b>#{$titulo->id}</b> do capítulo <b>{$nome}</b>: '{$titulo->texto}'!",
                'id_moderador' => $usuario_moderador->id,
                'id_usuario' => $id_usuario,
                'id_titulo' =>$titulo->id_titulo
            ));
        }
        
        echo json_encode($result);
        exit;
    }
    else if ($funcao=="pegar-comentarios") {
        $titulo = new Titulo($dados);
        
        $result = $titulo->pegarComentarios();
        
        echo json_encode($result);
        exit;
    } 
    else if ($funcao=="excluir") {
        $titulo = new Titulo($dados);
        $result = $titulo->excluir($tipo);
        $id_usuario = $dados['id_usuario']; unset($dados['id_usuario']);

        if (is_object($usuario_moderador) and $usuario_moderador->id!=$id_usuario) {
            $usuario->novoLogModerador(array(
                'descricao' => "O moderador <b>{$usuario->nickname}</b> excluiu o capitulo <b>{$titulo->nome}!</b>!",
                'id_moderador' => $usuario_moderador->id,
                'id_usuario' => $id_usuario,
                'id_projeto' => $titulo->serie
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