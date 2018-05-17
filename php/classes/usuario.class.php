<?
date_default_timezone_set('America/Sao_Paulo');

class Usuario {
    private $props = []; 
    public $valores_atualizar = array();
    
    public function cadastrar() {
        $senha_digitada = $this->senha;
        
        $result = DBselect('usuario', "where email = '{$this->email}' or nickname = '{$this->nickname}'", 'email, nickname');
        
        if (count($result)>0) {
            $result = $result[0];
            if ($this->email==$result['email']) return array('estado'=>2, 'mensagem'=>"Já existe algum usuário cadastrado com esse email!");
            else return array('estado'=>2, 'mensagem'=>"Já existe algum usuário cadastrado com esse Nick!");
        } else {
            $qtd = DBselect("moderador", "", "count(*) as qtd")[0]['qtd'];
                    
            $this->hash = time();
            $this->senha = md5($this->senha.$this->hash);
            
            $dados = array_filter($this->toArray());
            unset($dados['repetirSenha']);
            
            $this->id = DBcreate('usuario', $dados);
            $retorno = "Conta criada com sucesso. Para publicar Series confirme sua conta em <b>Publicar</b>!";

            if ($qtd==0) {
                DBcreate("moderador", array(
                    'administradorGeral' => 1,
                    'id_usuario' => $this->id
                ));
                
                $retorno = "Conta Administrador geral criada com sucesso!";
            }
            
            // ENVIAR EMAIL DE CONFIRMAÇÃO DE CADASTRO
            $mensagem = "<p>{$this->nome} ({$this->nickname})</p>";
            $mensagem .= "<p>VOCÊ ESTÁ NO SITE DE HQs E NOVELS MAIS DIVERTIDO DA INTERNET.</p>";
            $mensagem .= "<p>NOSSA MISSÃO? ENTRETER VOCÊ QUE AMA ESTA ARTE, VALORIZANDO OS ARTISTAS DOS QUADRINHOS COM UMA PLATAFORMA DEDICADA A ELES.</p>";
            $mensagem .= "<p>SABOREIE OS TÍTULOS SEM MODERAÇÃO.</p>";
            $mensagem .= "<p>VENHA SEMPRE!</p>";
            
            $result = $this->mensagem($mensagem, "Bem-vindo a nossa plataforma");
            
            return array('estado'=>1, 'mensagem'=>$retorno);
        }
    }
    
    public function login($senha = true) {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $result = DBselect('usuario', "where email = '{$this->email}'");
            $result = $result[0];
            
            // Verifica se usuário está cadastrado
            if (!empty($result)) {        
                // Verifica se senha está correta
                if (!$senha or $result['senha'] == md5($this->senha.$result['hash'])) {
                    if ($result['estado_conta']<=1) {
                        unset($_SESSION['usuario']);
                        // echo "empresa encontrado";

                        // CAPTURAR TODAS AS INFORMAÇÕES DO empresa
                        $dados = $result;
                        $this->fromArray($dados);
                        $this->valores_atualizar = array();
                        
                        // TEMPO PARA EXPIRAR SESSÃO
                        $_SESSION['expire'] = time();

                        // IDENTIFICAR SESSÃO
                        $_SESSION['donoSessao']=md5('sat'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
                        // session_name($_SESSION['donoSessao']);
                        
                        // VERIFICAR SE É MOD OU ADM
                        $result = DBselect("moderador", "where id_usuario={$this->id}");
                        if (count($result)>0) {
                            $result = $result[0];
                            if ($result['administradorGeral']==0) $_SESSION['tipo_usuario'] = 2;
                            else if ($result['administradorGeral']==1) $_SESSION['tipo_usuario'] = 3;
                        } else {
                            $_SESSION['tipo_usuario'] = 1;
                        }
                        
                        // MENSAGEM de boa vindas
                        if ($this->ultimo_login==null or $this->ultimo_login=="") {
                            $_SESSION['conf_msg'] = "Bem vindo a ZINNES. Toda a equipe estará a disposição!";
                        }

                        // VERIFICAR SE TEM CAPITULOS COMO RASUNHO
                        $capitulos = DBselect('titulo t INNER JOIN projeto p ON t.id_projeto = p.id', "where p.id_usuario={$this->id} and t.rascunho=1", 'COUNT(t.id) as qtd')[0]['qtd'];
                        if ($capitulos>0) {
                        	$_SESSION['info_msg'] = "Você tem {$capitulos} capítulo".($captiulos>1?"s":"")." não publicado".($captiulos>1?"s":"").".<br><br>Capítulos como rascunho não podem ser vistos pelos leitores.";
                        }
                        
                        DBupdate('usuario',array('ultimo_login'=>date('Y-m-d H:i:s', time())) , "where id={$this->id}");
                        $this->ultimo_login = date('Y-m-d H:i:s', time());
                        // RETORNO
                        $_SESSION['usuario'] = serialize($this);
                        if ($_SESSION['tipo_usuario']>=2) $_SESSION['usuario_moderador'] = serialize($this);
                        return array('estado'=>1, 'mensagem'=> "Redirecionando...");
                        
                    } else if ($result['estado_conta']==2) {
                        $this->fromArray($result);
                        if (strtotime($this->dias_bloqueio)<time()) {
                            $this->valores_atualizar = array();

                            DBupdate("usuario", array("estado_conta"=>0), "where id={$this->id}");

                            return array('estado'=>2, 'mensagem'=> "Sua conta foi desbloqueada, faça login novamente para acessá-la!");              
                        } else {
                            $data = date("d/m/Y", strtotime($this->dias_bloqueio));
                            return array('estado'=>2, 'mensagem'=> "Sua conta está bloqueada temporariamente até {$data}!");
                        }
                        
                    } else if ($result['estado_conta']==3) {
                        return array('estado'=>2, 'mensagem'=> "Sua conta está bloqueada permanentemente!");
                    }
                } else {
                    return array('estado'=>2, 'mensagem'=> "Senha incorreta para esta conta!");
                }
            } else {
                return array('estado'=>2, 'mensagem'=> "Credenciais inválidas!");
            }
        } else {
            return array('estado'=>2, 'mensagem'=> "Digite um email válido");
        }
    }

    public function apiFacebook() {
        $result = DBselect('usuario', "where email = '{$this->email}'");

        if (count($result)>0) {
            $result = $result[0];

            if ($result['facebook']==null) return array('estado'=>2, 'mensagem'=>"Este email já está cadastrado em nosso sistema, tente fazer login da maneira convencional!");

            return $this->login(false);
        } else {
            $qtd = DBselect("moderador", "", "count(*) as qtd")[0]['qtd'];

            $dados = array_filter($this->toArray());
            $dados['nickname'] = "u".time();
            $dados['confirmado'] = 1;
            $this->id = DBcreate('usuario', $dados);
            $retorno = "Conta criada com sucesso. Para publicar Series vá em <b>Publicar</b>!";

            if ($qtd==0) {
                DBcreate("moderador", array(
                    'administradorGeral' => 1,
                    'id_usuario' => $this->id
                ));
                
                $retorno = "Conta Administrador geral criada com sucesso!";
            }
            
            return $this->login(false);
        }
    }

    public function apiGoogle() {
        $result = DBselect('usuario', "where email = '{$this->email}'");

        if (count($result)>0) {
            $result = $result[0];

            if ($result['google']==null) return array('estado'=>2, 'mensagem'=>"Este email já está cadastrado em nosso sistema, tente fazer login da maneira convencional!");

            return $this->login(false);
        } else {
            $qtd = DBselect("moderador", "", "count(*) as qtd")[0]['qtd'];

            $dados = array_filter($this->toArray());
            $dados['nickname'] = "u".time();
            $dados['confirmado'] = 1;
            $this->id = DBcreate('usuario', $dados);
            $retorno = "Conta criada com sucesso. Para publicar Series vá em <b>Publicar</b>!";

            if ($qtd==0) {
                DBcreate("moderador", array(
                    'administradorGeral' => 1,
                    'id_usuario' => $this->id
                ));
                
                $retorno = "Conta Administrador geral criada com sucesso!";
            }
            
            return $this->login(false);
        }
    }

    public function confirmarConta() {
        $result = DBselect("usuario", "where id={$this->id} and senha='{$this->senha}'");

        if (count($result)>0) {

            $result = $result[0];
            DBupdate("usuario",array('confirmado'=>1) , "where id={$result['id']}");

            return array('estado'=>1, 'mensagem'=> "Sua conta foi confirmada com sucesso, agora você poderá publicar Comics e Novels!");
        } else {
            return "Conta não existe";
            exit;
        }
    }

    public function recuperarSenha() {
        if (!$this->carregar(null,null,$this->email)) {
            return array('estado'=>2, 'mensagem'=>"Email não encontrado!");
        }
        // link válido por 1 hora
        $agora = time();
        $url = "www.zinnes.com.br/recuperarSenha?hash={$this->senha}&time={$agora}&id={$this->id}";

        $texto = "<p>Você solicitou recuperação de senha. Para alterar sua senha acesse o link abaixo clicando no botão ou colando o LINK diretamente no navegador!</p>";
        $texto .= "<a href='{$url}' class='botao' target='_BLANK'>Clique Aqui</a>";
        $texto .= "<br>";
        $texto .= "<p>Não consegue acessar o link? Copie e cole-o no navegador: {$url}</p>";

        if ($this->mensagem($texto, "Recuperar senha")==1) {
            return array('estado'=>1, 'mensagem'=>"Email enviado com sucesso, verifique sua caixa de emails!");
        } else {
            return array('estado'=>2, 'mensagem'=>"Problemas ao enviar email, tente novamente mais tarde!");
        }
    }
    
    public function atualizar($arquivos = null) {
        if ($this->estaDeclarado('nickname')) {
            $nickname = DBselect("usuario", "where nickname='{$this->nickname}' and id<>{$this->id}");
            
            if (count($nickname)>0) {
                return array('estado'=>2, 'mensagem'=>"Já existe um usuário com esse Nickname!");
            }
        } else if ($this->estaDeclarado('email')) {
            $email = DBselect("usuario", "where email='{$this->email}' and id<>{$this->id}");
            
            if (count($email)>0) {
                return array('estado'=>2, 'mensagem'=>"Já existe um usuário com esse Email!");
            }
        }
        $bloqueio = false;
        if ($this->estaDeclarado('estado_conta') and $this->estado_conta>1) {
            if ($this->estado_conta==2) {
                $this->dias_bloqueio = date('Y-m-d H:i:s', time() + $this->dias_bloqueio*24*60*60);
                $bloqueio = true;
            }
        }
        
        if ($this->senha=="") $this->unsetAtributo('senha');
        else {
            $this->hash = time();
            $this->senha = md5($this->senha.$this->hash);
        }
        
        if ($arquivos!=null and is_uploaded_file($arquivos['imagem-perfil']['tmp_name'])) {
            $this->foto_perfil = $this->mudarFoto($arquivos['imagem-perfil'], $this->larguraImagem, $this->alturaImagem);
            $this->unsetAtributo('alturaImagem');
            $this->unsetAtributo('larguraImagem');
        }
        
        
        $temp = $this->id;
        DBupdate("usuario", $this->valores_atualizar, "where id={$temp}");
        
        if ($bloqueio) $this->dias_bloqueio = strtotime($this->dias_bloqueio);
        
        $this->valores_atualizar = array();
        return array('estado'=>1, 'mensagem'=>"Informações atualizadas com sucesso!", 'atualizado' => $this->toArray());
    }
    
    public function carregar($id = null, $nome = null, $email = null) {
        if ($id!=null) {
            $result = DBselect("usuario", "where id = {$id}");
        } else if ($nome!=null) {
            $result = DBselect("usuario", "where nickname = '{$nome}'");
        } else if ($email!=null) {
            $result = DBselect("usuario", "where email = '{$email}'");
        }
        
        if (count($result)>0) {
            $this->fromArray($result[0]);
            return true;
        }
        
        return false;
    }

    public function pesquisar($pesquisa, $limite = 0) {
        $pesquisa = explode(" ", $pesquisa);
        $query = "";
        foreach ($pesquisa as $key => $value) {
            $query .= "nome LIKE '%{$value}%' or nickname LIKE '%{$value}%' ";
            if ($key<count($pesquisa)-1) $query .= "or ";
        }
        $result = [];
        $result = DBselect("usuario", "where {$query} order by nickname ASC limit {$limite}, 20", "id, nickname, descricao, foto_perfil");
        $qtd = DBselect("usuario", "where $query", "COUNT(id) as qtd")[0]['qtd'];

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
            //erro
            // echo $mail->ErrorInfo;
            return 2;
        } else {
            $mail->ClearAllRecipients();
            
            return 1;
        }
    }

    public function mudarFoto($imagem, $w, $h) {
        $dirname = realpath("../..".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'servidor'.DIRECTORY_SEPARATOR.'thumbs-usuarios'.DIRECTORY_SEPARATOR;
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
        
        if ($this->foto_perfil!=null || $this->foto_perfil!="") unlink($dirname.$this->foto_perfil);
        
        return $save;
    }

    public function atualizarPin() {
        DBupdate("moderador", array('pin' => "{$this->pin}"), "where id_usuario={$this->id_usuario}");

        return array('estado'=>1, 'mensagem'=>"PIN atualizado com sucesso!");
    }

    // parte dos comentários

    public function comentar() {
        $id = DBcreate("comentario_usuario", $this->toArray());
        $this->id = $id;
        $this->data = time();
        return array('estado'=>1, 'mensagem'=>"Comentário postado!", 'comentario' => $this->toArray());
    }
    
    public function atualizarComentario() {
        $temp = $this->id;
        DBupdate("comentario_usuario", $this->valores_atualizar, "where id={$temp}");
        
        return array('estado'=>1, 'mensagem'=>"Comentário editado!", 'comentario' => $this->toArray());
    }
    
    public function apagarComentario() {
        // var_dump($this->toArray()); exit;
        DBdelete("comentario_usuario", "where id={$this->id} or id_referencia={$this->id}");
        
        return array('estado'=>1, 'mensagem'=>"Comentário apagado!");
    }
    
    public function pegarComentarios() {
        $ultimo = $this->ultimoId!=0?" and c.id<{$this->ultimoId}":"";
        
        $comentarios1 = DBselect("comentario_usuario c INNER JOIN usuario u ON c.de = u.id", "where para={$this->id}{$ultimo} and id_referencia IS NULL order by data DESC limit 10", "c.*, u.foto_perfil, u.nickname");
        
        $comentarios2 = [];
        if ($this->ultimoId==0) $comentarios2 = DBselect("comentario_usuario c INNER JOIN usuario u ON c.de = u.id", "where para={$this->id}{$ultimo} and id_referencia IS NOT NULL order by data DESC", "c.*, u.foto_perfil, u.nickname");

        foreach ($comentarios1 as $key => $value) {
            $comentarios1[$key]['data'] = strtotime($value['data']);
        }

        foreach ($comentarios2 as $key => $value) {
            $comentarios2[$key]['data'] = strtotime($value['data']);
        }
        
        return array(
            "estado"=>1,
            "comentariosNivel1"=>$comentarios1,
            "comentariosNivel2"=>$comentarios2
        );
    }

    // parte do moderador

    public function moderador() {
        $moderador = DBselect("moderador", "where id_usuario={$this->id}");

        if (count($moderador)==0) {
            DBcreate("moderador", array('id_usuario'=>$this->id));

            return array("estado"=>1, "mensagem"=>"Este usuário agora é um moderador!", "moderador" => true);
        } else {
            DBdelete("moderador", "where id_usuario={$this->id}");

            return array("estado"=>1, "mensagem"=>"Este usuário deixou de ser um moderador!", "moderador" => false);
        }

        return array("estado"=>0, "mensagem"=>"Função inválida!");
    }

    public function adicionarSlide($imagem) {
        $dirname = realpath(dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."slides").DIRECTORY_SEPARATOR;
        
        if ($this->atualizar) $numero = explode("-", $this->imagemAntiga)[0];
        else $numero = $this->numero;

        $save = $numero."-".time().".jpg";
        
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
        
        $img2 = imagecrop($img, ['x' => 0, 'y' => 0, 'width' => $this->w, 'height' => $this->h]);

        imagejpeg($img2, $dirname.$save, 90);
        imagedestroy($img2);
        
        if ($this->imagemAntiga!=null || $this->imagemAntiga!="") {
            unlink($dirname.$this->imagemAntiga);
        } else {
            DBcreate("slides", array("numero"=>$numero));
        }
        
        $result = array();
        $result['estado'] = 1;
        $result['mensagem'] = "Slide atualizado com sucesso!";
        $result['slide'] = $save;
        $result['links'] = DBselect("slides");

        return $result;
    }

    public function removerSlide() {
        $numero = explode("-", $this->imagem)[0];
        $diretorio = realpath(dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."slides").DIRECTORY_SEPARATOR;


        $slides = eval("return " . $this->slides . ";");
        unset($slides[$numero-1]);
        // var_dump($slides); exit;

        unlink($diretorio.$this->imagem);
        DBdelete("slides", "where numero={$numero}");

        foreach ($slides as $key => $value) {
            $temp = explode("-", $value)[0];

            if ($temp>$numero) {
                $novo = ($temp-1)."-".time().".jpg";
                $numero = explode("-", $value)[0];
                DBupdate("slides", array('numero'=>$temp-1), "where numero={$numero}");
                rename($diretorio.$value, $diretorio.$novo);
                $slides[$key] = $novo;
            }
        }

        $result = array();
        $result['estado']=1;
        $result['mensagem']="Slide removido com sucesso!";
        $result['slides']=array_values($slides);
        $result['links'] = DBselect("slides");

        return $result;
    }
    
    public function atualizarLinkSlide() {
        foreach ($this->toArray() as $key => $value) {
            $numero = explode("_", $key)[1];
            DBupdate("slides", array('link'=>$value), "where numero={$numero}");
        }

        $result = array();
        $result['estado']=1;
        $result['mensagem']="Links atualizados com sucesso!";
        $result['slides']=array_values($slides);
        $result['links'] = DBselect("slides");

        return $result;
    }

    public function emailModerador($lista = null) {
        // return $this->usuario;
        if ($lista==null) {
            if ($this->usuario==null or $this->usuario=="") {
                $this->sexo = $this->sexo==0?"":" where sexo = {$this->sexo}";
                $lista = DBselect("usuario", $this->sexo, "nome, email");
            } else {
                $this->sexo = $this->sexo==0?"":"and sexo = {$this->sexo}";
                $lista = DBselect("usuario", "where nickname='{$this->usuario}' {$this->sexo}", "nome, email");

                if (count($lista)==0) return array('estado'=>2, 'mensagem'=>"Usuario não encontrado!");
            }
        }

        $mensagem = file_get_contents(dirname(__DIR__, 2)."/html/emailGeral.html");

        $mensagem = str_replace("--TITULO--", "Email Sistema", $mensagem);
        $mensagem = str_replace("--MENSAGEM--", $this->mensagem, $mensagem);

        $mail = new PHPMailer;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->addAddress("suporte@zinnes.com.br", "Email ZINNES");

        foreach ($lista as $key => $value) {
            $mail->addCC($value['email'], $value['nome']);
        }

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
            //erro
            return array('estado'=>2, 'mensagem'=>"Erro ao enviar email!");
        } else {
            $mail->ClearAllRecipients();
            
            return array('estado'=>1, 'mensagem'=>"Email enviado com sucesso!");
        }
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
    
    public function novoLogModerador($log) {
        $id = DBcreate('log_moderador', $log);
    }
    
    public function novaNotificacao($notificacao) {
       // $id = DBcreate('notificacao', $notificacao);
    }
    
    public function pegarNotificacoes($data = 0) {
        $extra = $data!=0?" and n.data < '".date('Y-m-d H:i:s', $data)."'":"";
        $extra2 = $data!=0?" and data < '".date('Y-m-d H:i:s', $data)."'":"";
        
        $notificacoes = DBselect('notificacao n INNER JOIN titulo t ON n.id_titulo = t.id INNER JOIN projeto p ON t.id_projeto = p.id', "where n.id_usuario = {$this->id}{$extra} order by n.data DESC limit 10", "n.*, t.nome, t.descricao, t.id_projeto, t.thumb_titulo, p.thumb_projeto, p.tipo");
        
        $logs = DBselect('log_moderador', "where id_usuario = {$this->id}{$extra2} order by data DESC limit 10");
        
        DBupdate("notificacao", array('lido' => 1), "where id_usuario={$this->id}");
        DBupdate("log_moderador", array('lido' => 1), "where id_usuario={$this->id}");
        
        $notificacoes = $notificacoes==null?[]:$notificacoes;
        $logs = $logs==null?[]:$logs;

        $retorno = [];
        $retorno['notificacoes'] = $notificacoes;
        $retorno['logs'] = $logs;
        if (count($notificacoes)<10 and count($logs)<10) $retorno['acabou'] = 1;
        
        return $retorno;
    }
    
    public function lerNotificacao($data) {
        
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

// ESTADOS DA CONTA DO USUÁRIO
// 0 = NÃO CONFIRMADA
// 1 = CONFIRMADA
// 2 = BLOQUEADA TEMPORARIAMENTE
// 3 = BLOQUEADA PERMANENTEMENTE

// TIPO DE USUÁRIO NA SESSÃO
// 0 = NÃO CADASTRADO
// 1 = CADASTRADO 
// 2 = MODERADOR
// 3 = ADMINISTRADOR GERAL
?>