<?php

class Titulo {

    private $props = []; 
    public $valores_atualizar = array();
    
    public function novo($arquivos) {
        $result = DBselect('titulo', "where nome='{$this->nome}' and id_projeto={$this->id_projeto}", 'nome');
        // var_dump($result); exit;
        if (count($result)>0) {
            return array('estado'=>2, 'mensagem'=>"Já existe algum Capítulo criado com esse nome, tente outro!");
            exit;
        } else {
            $this->data = date('Y-m-d H:i:s', time());
            $this->data_ultima_atualizacao = date('Y-m-d H:i:s', time());
            
            if ($this->estaDeclarado("imagens")) {
                $imagens = $this->imagens;
                $this->unsetAtributo("imagens");
            }
            if ($this->estaDeclarado("ordem")) {
                $ordem = $this->ordem;
                $this->unsetAtributo("ordem");
            }

            if ($arquivos!=null and isset($arquivos['thumb_titulo']) and is_uploaded_file($arquivos['thumb_titulo']['tmp_name'])) {
                $this->thumb_titulo = $this->mudarFoto($arquivos['thumb_titulo'], $this->larguraImagem, $this->alturaImagem);
                $this->unsetAtributo('alturaImagem');
                $this->unsetAtributo('larguraImagem');
            }
            
            $this->id = DBcreate("titulo", $this->toArray());
            
            if (isset($imagens)) $this->imagens = $imagens;
            if (isset($ordem)) $this->ordem = $ordem;
            
            $this->atualizar($arquivos);
            
            return array('estado'=>1, 'mensagem'=>"Capítulo salvo com sucesso!", 'titulo' => $this->toArray());
            exit;
        }
    }
    
    public function atualizar($arquivos = null) {
        if ($this->estaDeclarado('nome')) {
            $nome = DBselect("titulo", "where nome='{$this->nome}' and id<>{$this->id} and id_projeto={$this->id_projeto}");
            
            if (count($nome)>0) {
                return array('estado'=>2, 'mensagem'=>"Já existe algum Capítulo criado com esse nome, tente outro!");
                exit;
            }
        }
        
        if ($this->estaDeclarado('data_lancamento')) {
            $this->data_lancamento = date("Y-m-d H:i:s", $this->data_lancamento);
        }
        
        if ($arquivos!=null and isset($arquivos['thumb_titulo']) and is_uploaded_file($arquivos['thumb_titulo']['tmp_name'])) {
            $this->thumb_titulo = $this->mudarFoto($arquivos['thumb_titulo'], $this->larguraImagem, $this->alturaImagem);
            $this->unsetAtributo('alturaImagem');
            $this->unsetAtributo('larguraImagem');
        }
        $nomes = [];
        if ($this->estaDeclarado('imagens')) {
            $nomes = $this->uploadImagensComic();
            
            $this->unsetAtributo("imagens");
        }
        
        if ($this->estaDeclarado('ordem')) {
            $dirname = realpath("../..".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'servidor'.DIRECTORY_SEPARATOR.'titulos'.DIRECTORY_SEPARATOR.'comics'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
            foreach($this->ordem as $numero => $nome) {
                rename($dirname.$nome, $dirname."{$numero}-".time().".jpg");
                $nomes[$numero] = "{$numero}-".time().".jpg";
            }
            
            $this->unsetAtributo("ordem");
        }
        
        $this->data_ultima_atualizacao = date("Y-m-d H:i:s", time());
        
        $temp = $this->id;
        DBupdate("titulo", $this->valores_atualizar, "where id={$temp}");
        
        $this->valores_atualizar = array();
        
        return array('estado'=>1, 'mensagem'=>"Informações atualizadas com sucesso!", 'titulo' => $this->toArray(), 'nomes' => $nomes);
    }

    public function excluir($tipo, $geral = false) {
        if (!$this->estaDeclarado("thumb_titulo")) {
            $carregar = DBselect("titulo", "where id={$this->id}");
            // var_dump($carregar); exit;
            $this->fromArray($carregar[0]);
        }

        $dirname = realpath("../..".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'servidor'.DIRECTORY_SEPARATOR.'titulos'.DIRECTORY_SEPARATOR;
        
        if ($this->thumb_titulo!=null || $this->thumb_titulo!="") unlink($dirname."thumbs".DIRECTORY_SEPARATOR.$this->thumb_titulo);

        if ($tipo==1) {
            $arquivos = listar($dirname);

            if (count($arquivos['nomes'])>0) {
                foreach ($$arquivos['nomes'] as $key => $value) {
                    unlink($dirname."comics".DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.$this->$value);
                }
                rmdir($dirname."comics".DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
            }
        }

        if ($geral==false) {
            DBdelete("titulo", "where id={$this->id}");
        }

        DBdelete("avaliar_titulo", "where id_titulo={$this->id}");
        DBdelete("comentario_titulo", "where id_titulo={$this->id}");
        DBdelete("notificacao", "where id_titulo={$this->id}");

        return array('estado'=>1, 'mensagem'=>"Capítulo excluido!"); 
    }
    
    public function publicar() {
        $seguidores = DBselect("seguir_projeto","where id_projeto={$this->id_projeto} order by data ASC");
        
        if (count($seguidores)>0) {
            $notificacoes = array();
            $notificacao = array();
            $notificacao['id_titulo'] = $this->id;

            foreach($seguidores as $seg) {
                $notificacao['id_usuario'] = $seg['id_usuario'];
                array_push($notificacoes, $notificacao);
            }

            DBcreateVarios("notificacao", $notificacoes);
        }
        
        return array("estado"=>1, "mensagem"=> "Este título acaba de ser publicado, todos os seguidores serão alertados!");
    }
    
    public function carregar($id) {
        $result = DBselect("titulo t", "where id = {$id}", "t.*, (select COUNT(id_usuario) from avaliar_titulo where id_titulo = t.id ) gosteis");
        
        if (count($result)>0) {
            $this->fromArray($result[0]);
            return true;
        }
        
        return false;
    }

    public function visualizacao() {
        DBupdate("titulo", array("visualizacoes"=>"visualizacoes+1"), "where id={$this->id}");
    }

    public function carregarPortifolio($id, $sigo = false) {
        if (!$sigo) {
            $result = DBselect('titulo t INNER JOIN projeto p ON t.id_projeto = p.id', "where id_projeto in (select id from projeto where id_usuario={$id}) and rascunho=0 order by data DESC limit 10", "t.*, p.nome as serie, p.tipo");
        } else {
            $result = DBselect('titulo t INNER JOIN avaliar_titulo a ON t.id = a.id_titulo INNER JOIN projeto p ON t.id_projeto = p.id', "where id_projeto in (select id from projeto where id_usuario={$id}) and rascunho=0 order by data DESC limit 50", "t.*, p.nome as serie, p.tipo");
        }
        
        
        if (count($result)>0) {
            return $result;
        }
        
        return [];
    }

    public function pesquisar($pesquisa, $limite, $tipo) {
        $pesquisa = explode(" ", $pesquisa);
        $query = "where (";
        foreach ($pesquisa as $key => $value) {
            $query .= "t.nome LIKE '%{$value}%' or t.descricao LIKE '%{$value}%' ";
            if ($key<count($pesquisa)-1) $query .= "or ";
        }
        $query .= ")";
        $result = [];
        $result = DBselect("titulo t INNER JOIN projeto p ON t.id_projeto = p.id INNER JOIN usuario u ON p.id_usuario = u.id", "{$query} and p.tipo={$tipo} order by t.visualizacoes DESC limit {$limite}, 20", "t.*, p.id_genero, u.nickname, p.thumb_projeto, p.tipo, p.nome as serie");
        $qtd = DBselect("titulo t INNER JOIN projeto p ON t.id_projeto = p.id", "{$query} and p.tipo={$tipo}", "COUNT(t.id) as qtd")[0]['qtd'];

        return array("quantidade"=>$qtd, "resultados"=>$result);
    }
    
    public function mensagem($texto_mensagem, $titulo = "Mensagem Plataforma") {
        $mensagem = file_get_contents(dirname(__DIR__, 2)."/html/emailGeral.html");
        // echo dirname(__DIR__, 2)."/html/emailGeral.html"; exit;
        $mensagem = str_replace("--TITULO--", $titulo, $mensagem);
        $mensagem = str_replace("--MENSAGEM--", $texto_mensagem, $mensagem);

        $mail = new PHPMailer;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->addAddress($this->email, $this->nome);

        $mail->SMTPDebug = 0;                            // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'mx1.hostinger.com.br';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'suporte@zinnes.com.br';                 // SMTP username
        $mail->Password = 'teste123';                           // SMTP password
        //$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                  // TCP port to connect to

        $mail->setFrom('suporte@zinnes.com.br', 'ZINNES');

        $mail->DEBUG = 0;
        $mail->Subject = $titulo.' - ZINNES';
        $mail->isHTML(true);
        $mail->Body = $mensagem;
        $mail->CharSet = 'UTF-8';

        if (!$mail->send()) {
            return 2;
        } else {
            $mail->ClearAllRecipients();
            
            return 1;
        }
    }

    public function mudarFoto($imagem, $w, $h) {
        error_reporting(E_ERROR | E_PARSE);
        // var_dump($imagem); exit;
        $antiga = null;
        if ($this->estaDeclarado("id")) $antiga = DBselect("titulo", "where id={$this->id}", "thumb_titulo");
        if (count($antiga)>0) $antiga = $antiga[0]['thumb_titulo'];
        
        $dirname = realpath("../..".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'servidor'.DIRECTORY_SEPARATOR.'titulos'.DIRECTORY_SEPARATOR.'thumbs'.DIRECTORY_SEPARATOR;
        $save = $this->id.time().".jpg";
        
        switch (pathinfo($imagem['name'], PATHINFO_EXTENSION))
        {
            case 'jpeg':
            case 'jpg':
                $img = imagecreatefromjpeg($imagem['tmp_name']);
            break;
            case 'png':
                $img = imagecreatefrompng($imagem['tmp_name']);
            break;
            default:
                die('Invalid image type');
        }
        
        // echo get_resource_type($img); exit;

        $img2 = imagecrop($img, ['x' => 0, 'y' => 0, 'width' => $w, 'height' => $w]);

        if ($img2 !== FALSE) {
            imagejpeg($img2, $dirname.$save, 90);
            imagedestroy($img2);
            // echo "deu certo";
        } else {
            return array('estado'=>2, 'mensagem'=>"Erro ao cortar imagem!");
            exit;
        }
        // exit;
        if ($antiga!=null || $antiga!="") unlink($dirname.$antiga);
        
        return $save;
    }
    
    public function uploadImagensComic() {
        $nomes = [];
        foreach($this->imagens as $numero => $imagem) {
            $dirname = realpath("../..".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'servidor'.DIRECTORY_SEPARATOR.'titulos'.DIRECTORY_SEPARATOR.'comics'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
            $save = $numero."-".time();
            
            if (!is_dir($dirname) && !mkdir($dirname)) {
                echo "Erro ao criar pasta";
            }
            
            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagem['fileData']));
            $img = imagecreatefromstring($data);
            imagejpeg($img, $dirname.$save.".jpg", 90);
            
            $nomes[$numero] = $save;
        }
        
        return $nomes;
    }
    
    public function apagarImagemComic() {
        $dirname = realpath("../..".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'servidor'.DIRECTORY_SEPARATOR.'titulos'.DIRECTORY_SEPARATOR.'comics'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
        
        unlink($dirname.$this->apagar);
        
        foreach($this->ordem as $numero => $nome) {
            if ($numero<$this->num) continue;
            rename($dirname.$nome, $dirname."{$numero}-".time().".jpg");
            $nomes[$numero] = "{$numero}-".time().".jpg";
        }
        
        return array('estado'=>1, 'nomes'=>$nomes);
    }
    
    public function gostei() {
        if ($this->gostei==0) {
            $this->unsetAtributo("gostei");
            DBcreate("avaliar_titulo", $this->toArray());
            return array('estado'=>1, 'mensagem'=>"Avaliação confirmada!");
        } else {
            DBdelete("avaliar_titulo", "where id_usuario={$this->id_usuario} and id_titulo={$this->id_titulo}");
            return array('estado'=>1, 'mensagem'=>"Avaliação removida!");
        }
    }
    
    public function comentar() {
        $id = DBcreate("comentario_titulo", $this->toArray());
        $this->id = $id;
        $this->data = time();
        return array('estado'=>1, 'mensagem'=>"Comentário postado!", 'comentario' => $this->toArray());
    }
    
    public function atualizarComentario() {
        $temp = $this->id;
        DBupdate("comentario_titulo", array('texto'=>$this->texto), "where id={$temp}");
        
        return array('estado'=>1, 'mensagem'=>"Comentário editado!", 'comentario' => $this->toArray());
    }
    
    public function apagarComentario() {
        DBdelete("comentario_titulo", "where id={$this->id} or id_referencia={$this->id}");
        
        return array('estado'=>1, 'mensagem'=>"Comentário apagado!");
    }
    
    public function pegarComentarios() {
        $ultimo = $this->ultimoId!=0?" and c.id<{$this->ultimoId}":"";
        
        $comentarios1 = DBselect("comentario_titulo c INNER JOIN usuario u ON c.id_usuario = u.id", "where id_titulo={$this->id}{$ultimo} and id_referencia IS NULL order by data DESC limit 10", "c.*, u.foto_perfil, u.nickname");
        
        $comentarios2 = [];
        if ($this->ultimoId==0) $comentarios2 = DBselect("comentario_titulo c INNER JOIN usuario u ON c.id_usuario = u.id", "where id_titulo={$this->id}{$ultimo} and id_referencia IS NOT NULL order by data DESC", "c.*, u.foto_perfil, u.nickname");
        
        return array(
            "estado"=>1,
            "comentariosNivel1"=>$comentarios1,
            "comentariosNivel2"=>$comentarios2
        );
    }
    
    public function toArray() {
        return $this->props;
    }
    
    public function fromArray($post) {
        foreach($post as $key => $value) {
            $this->props[$key] = $value;
            $this->valores_atualizar[$key] = $value;
        }
    }
    
    public function unsetAtributo($chave) {
        unset($this->props[$chave]);
        unset($this->valores_atualizar[$chave]);
    }
    
    public function estaDeclarado($chave) {
        if (isset($this->props[$chave])) return true;
        else return false;
    }
    
    // Gets e Sets
    public function __get($name) {
        if (isset($this->props[$name])) {
            return $this->props[$name];
        } else {
            return false;
        }
    }

    public function __set($name, $value) {
        $this->props[$name] = $value;
        $this->valores_atualizar[$name] = $value;
    }
    
    public function __wakeup(){
        foreach (get_object_vars($this) as $k => $v) {
            $this->{$k} = $v;
        }
    }
    
    public function __construct($dados=null) {
        if ($dados!=null) {
            $this->fromArray($dados);
        }
    }
}

?>