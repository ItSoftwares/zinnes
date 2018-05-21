<?php

class serie {
    private $props = [];
    public $valores_atualizar = array();
    
    public function nova() {
        $result = DBselect('projeto', "where nome = '{$this->nome}'", 'nome');
        
        if (count($result)>0) {
            return array('estado'=>2, 'mensagem'=>"Já existe alguma Serie cadastrada com esse nome, tente outro!");
        } else {
            $id = DBcreate("projeto", $this->toArray());
            $serie = DBselect("projeto", "where id={$id}")[0];
            $this->id = $id;
            return array('estado'=>1, 'mensagem'=>"Serie criada com sucesso!", 'serie' => $serie);
        }
    }
    
    public function atualizar($arquivos) {
        if ($this->estaDeclarado('nome')) {
            $nome = DBselect("projeto", "where nome='{$this->nome}' and id<>{$this->id}");
            
            if (count($nome)>0) {
                return array('estado'=>2, 'mensagem'=>"Já existe uma série com esse Nome!");
                exit;
            }
        } else {
            $this->nome = DBselect("projeto", "where id={$this->id}", "nome")[0]['nome'];
        }
        
        if ($arquivos!=null and isset($arquivos['upload-imagem-projeto']) and is_uploaded_file($arquivos['upload-imagem-projeto']['tmp_name'])) {
            $this->thumb_projeto = $this->mudarFoto($arquivos['upload-imagem-projeto'], $this->larguraImagem, $this->alturaImagem);
            $this->unsetAtributo('alturaImagem');
            $this->unsetAtributo('larguraImagem');
        } else if ($arquivos!=null and isset($arquivos['upload-banner-projeto']) and is_uploaded_file($arquivos['upload-banner-projeto']['tmp_name'])) {
            $this->banner_projeto = $this->mudarBanner($arquivos['upload-banner-projeto'], $this->larguraImagem, $this->alturaImagem);
            $this->unsetAtributo('alturaImagem');
            $this->unsetAtributo('larguraImagem');
        }
        
        $temp = $this->id;
        DBupdate("projeto", $this->valores_atualizar, "where id={$temp}");
        
        $this->valores_atualizar = array();
        
        return array('estado'=>1, 'mensagem'=>"Informações atualizadas com sucesso!", 'atualizado' => $this->toArray());
    }
    
    public function excluir() {
        $this->fromArray(DBselect("projeto", "where id={$this->id}"));

        $titulos = DBselect("titulo", "where id_projeto={$this->id}", "id, nome, thumb_titulo");

        $titulos = count($titulos)>0?$titulos:[];

        foreach ($titulos as $key => $value) {
            $titulo = new Titulo($value);

            $titulo->excluir($this->tipo, true);
        }

        $dirname = realpath("../..".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'servidor'.DIRECTORY_SEPARATOR.'projetos'.DIRECTORY_SEPARATOR;

        if ($this->thumb_projeto!=null || $this->thumb_projeto!="") unlink($dirname."thumbs".DIRECTORY_SEPARATOR.$this->thumb_projeto);
        if ($this->banner_projeto!=null || $this->banner_projeto!="") unlink($dirname."banners".DIRECTORY_SEPARATOR.$this->banner_projeto);

        DBdelete("titulo", "where id_projeto = {$this->id}");
        DBdelete("projeto", "where id={$this->id}");
        DBdelete("seguir_projeto", "where id_projeto={$this->id}");

        return array('estado'=>1, 'mensagem'=>"Serie excluida!"); 
    } 

    public function carregar($id) {
        $result = DBselect('projeto p INNER JOIN usuario u ON p.id_usuario = u.id', "where p.id='{$id}'", "p.*, u.nickname as autor, 
        (select nome from genero where id=p.id_genero) genero, 
        
        (select COUNT(id_usuario) from seguir_projeto where id_projeto=p.id) seguidores, 

        (select COUNT(id_titulo) from avaliar_titulo where id_titulo in (select id from titulo where id_projeto=p.id)) gosteis,
        
        (select COUNT(id) from titulo t where t.id_projeto = p.id and rascunho=0) capitulos,

        (select SUM(visualizacoes) from titulo where id_projeto=p.id) visualizacoes");
        
        if (count($result)>0) {
            $this->fromArray($result[0]);
            return true;
        }
        
        return false;
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
           // echo 'Message could not be sent.<pre>';
           // echo $mail->ErrorInfo;
            return 2;
        } else {
            $mail->ClearAllRecipients();
            
            return 1;
        }
    }
    
    public function mudarFoto($imagem, $w, $h) {
        $antiga = DBselect("projeto", "where id={$this->id}", "thumb_projeto");
        if (count($antiga)>0) $antiga = $antiga[0]['thumb_projeto'];
        
        $dirname = realpath("../..".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'servidor'.DIRECTORY_SEPARATOR.'projetos'.DIRECTORY_SEPARATOR.'thumbs'.DIRECTORY_SEPARATOR;
        $save = $this->id.time().".png";
        
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
        
        $img2 = imagecrop($img, ['x' => 0, 'y' => 0, 'width' => $w, 'height' => $w]);

        imagejpeg($img2, $dirname.$save, 50);
        imagedestroy($img2);
        
        if ($antiga!=null || $antiga!="") unlink($dirname.$antiga);
        
        return $save;
    }
    
    public function mudarBanner($imagem, $w, $h) {
        $antiga = DBselect("projeto", "where id={$this->id}", "banner_projeto");
        if (count($antiga)>0) $antiga = $antiga[0]['banner_projeto'];
        
        $dirname = realpath("../..".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'servidor'.DIRECTORY_SEPARATOR.'projetos'.DIRECTORY_SEPARATOR.'banners'.DIRECTORY_SEPARATOR;
        $save = $this->id.time().".png";
        
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
        
        $h = 325*$w/1350;
        
        $img2 = imagecrop($img, ['x' => 0, 'y' => 0, 'width' => $w, 'height' => $h]);

        imagejpeg($img2, $dirname.$save, 50);
        imagedestroy($img2);
        
        if ($antiga!=null || $antiga!="") unlink($dirname.$antiga);
        
        return $save;
    }
    
    public function seguir() {
        if (!is_numeric($this->id_usuario) or $this->id_usuario==0) {
            return array('estado'=>2, 'mensagem'=>"Faça login ou cadastre-se antes de seguir algum projeto!");
        }

        DBcreate("seguir_projeto", $this->toArray());
            
        return array('estado'=>1, 'mensagem'=>"Agora você está seguindo essa serie. <br><br>Quando o autor lançar novos capítulos você será notificado!");
    }
    
    public function deixarDeSeguir() {
        DBdelete("seguir_projeto", "where id_usuario={$this->id_usuario} and id_projeto={$this->id_projeto}");
            
        return array('estado'=>1, 'mensagem'=>"Você deixou de acompanhar esta serie.");
    }

    public function pesquisar($pesquisa, $limite, $tipo) {
        $pesquisa = explode(" ", $pesquisa);
        $query = "where (";
        foreach ($pesquisa as $key => $value) {
            $query .= "p.nome LIKE '%{$value}%' or p.descricao LIKE '%{$value}%' or p.tags LIKE '%{$value}%'";
            if ($key<count($pesquisa)-1) $query .= "or ";
        }
        $query .= ")";
        $result = [];
        $query .= $tipo==0?"":" and p.tipo={$tipo}";
        $result = DBselect("projeto p INNER JOIN usuario u ON p.id_usuario = u.id", "{$query} order by nome ASC limit {$limite}, 20", "p.*, u.nickname, (select SUM(visualizacoes) from titulo t where t.id_projeto = p.id) visualizacoes, (select COUNT(id) from titulo where id_projeto = p.id) capitulos");
        // $result = DBselect("projeto p INNER JOIN usuario u ON p.id_usuario = u.id", "{$query} and capitulos > 0 order by nome ASC limit {$limite}, 20", "p.*, u.nickname, (select SUM(visualizacoes) from titulo t where t.id_projeto = p.id) visualizacoes, (select COUNT(id) from titulo where id_projeto = p.id) capitulos");
        $qtd = DBselect("projeto p", "{$query}", "COUNT(p.id) as qtd")[0]['qtd'];

        return array("quantidade"=>$qtd, "resultados"=>$result);
    }

    public function carregarPortifolio($id, $sigo = false) {
        if (!$sigo) {
            $result = DBselect('projeto p', "where p.id_usuario='{$id}'", "p.*, 
            (select nome from genero where id=p.id_genero) genero, 
            
            (select COUNT(id_usuario) from seguir_projeto where id_projeto=p.id) seguidores, 

            (select COUNT(id_titulo) from avaliar_titulo where id_titulo in (select id from titulo where id_projeto=p.id)) gosteis,
            
            (select COUNT(id) from titulo t where t.id_projeto = p.id) capitulos,

            (select SUM(visualizacoes) from titulo where id_projeto=p.id) visualizacoes");
        } else {
            $result = DBselect('projeto p INNER JOIN usuario u ON p.id_usuario = u.id', "where p.id in (select id_projeto from seguir_projeto where id_usuario={$id})", "p.*, u.nickname as autor,  
            (select nome from genero where id=p.id_genero) genero, 
            
            (select COUNT(id_usuario) from seguir_projeto where id_projeto=p.id) seguidores, 

            (select COUNT(id_titulo) from avaliar_titulo where id_titulo in (select id from titulo where id_projeto=p.id)) gosteis,
            
            (select COUNT(id) from titulo t where t.id_projeto = p.id) capitulos,

            (select SUM(visualizacoes) from titulo where id_projeto=p.id) visualizacoes");
        }
        
        
        if (count($result)>0) {
            return $result;
        }
        
        return [];
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