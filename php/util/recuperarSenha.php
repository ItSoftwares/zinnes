<?
require "conexao.php";
require "vendor/autoload.php";

$dados = DBescape($_POST);
$email = $dados['email'];
$funcao = 0;
$result = "";

// verificador se é contador
$result = DBselect("contador", "where email='{$email}'");

if (count($result)>0) {
    $funcao=1;
} else {
    // verificador se é funcionario
    $result = DBselect("funcionario", "where email='{$email}'");

    if (count($result)>0) {
        $funcao=2;
    } else {
        // verificador se é empresa
        $result = DBselect("empresa", "where email='{$email}'");

        if (count($result)>0) {
            $funcao=2;
        }
    }
}

if ($funcao==1) {
    $url = "https://www.cnpj.net.br";
    
    $novaSenha = gerarSenha();

    $mensagem = file_get_contents("../html/emailGeral.html");
    $mensagem2 = "Olá você acabou de solicitar recuperação de senha para sua conta, abaixo estará uma senha temporária que deverá ser atualizada ao acessar o sistema!<br><br>";
    $mensagem2 = "Informações para Login:<br> <b>Email: </b><i>{$email}</i>";
    $mensagem2 .= "<br><b>Senha: </b><i>{$novaSenha}</i>";
    $mensagem2 .= "<br>";
    $mensagem2 .= "<a href='".$url."'>Link para Login!</a>";

    $mensagem = str_replace("--MENSAGEM--", $mensagem2, $mensagem);

    $mail = new PHPMailer;

    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->addAddress($email, "Contador");

    $mail->SMTPDebug = 0;                            // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'br316.hostgator.com.br';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'notificacoes_ktprime@cnpj.net.br';                 // SMTP username
    $mail->Password = '12345';                           // SMTP password
    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 465;                                  // TCP port to connect to

    $mail->setFrom('notificacoes_ktprime@cnpj.net.br', 'KTPrime');

    $mail->DEBUG = 0;
    $mail->Subject = 'Recuperação de senha - KTPrime';
    $mail->isHTML(true);
    $mail->Body = $mensagem;
    $mail->CharSet = 'UTF-8';
    
    if (!$mail->send()) {
//        echo 'Message could not be sent.<pre>';
//        echo $mail->ErrorInfo;
    } else {
        $mail->ClearAllRecipients();
    }
    
    DBupdate("contador", array('senha_temporaria'=>$novaSenha, 'trocar_senha'=>1), "where id=1");
    echo json_encode(array('estado'=>1, 'mensagem'=>"Enviamos um email com as informações de recuperação!"));
} else if ($funcao==2) {
    $url = "https://www.cnpj.net.br";

    $mensagem = file_get_contents("../html/emailGeral.html");
    $mensagem2 = "Olá você acabou de solicitar recuperação de senha para sua conta, abaixo estará sua senha!<br><br>";
    $mensagem2 = "Informações para Login:<br> <b>Email: </b><i>{$email}</i>";
    $mensagem2 .= "<br><b>Senha: </b><i>{$result[0]['senha']}</i>";
    $mensagem2 .= "<br>";
    $mensagem2 .= "<a href='".$url."'>Link para Login!</a>";

    $mensagem = str_replace("--MENSAGEM--", $mensagem2, $mensagem);

    $mail = new PHPMailer;

    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->addAddress($email, "Usuario");

    $mail->SMTPDebug = 0;                            // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'br316.hostgator.com.br';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'notificacoes_ktprime@cnpj.net.br';                 // SMTP username
    $mail->Password = '12345';                           // SMTP password
    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 465;                                  // TCP port to connect to

    $mail->setFrom('notificacoes_ktprime@cnpj.net.br', 'KTPrime');

    $mail->DEBUG = 0;
    $mail->Subject = 'Recuperação de senha - KTPrime';
    $mail->isHTML(true);
    $mail->Body = $mensagem;
    $mail->CharSet = 'UTF-8';
    
    if (!$mail->send()) {
//        echo 'Message could not be sent.<pre>';
//        echo $mail->ErrorInfo;
    } else {
        $mail->ClearAllRecipients();
    }
    
    echo json_encode(array('estado'=>1, 'mensagem'=>"Enviamos um email com as informações de recuperação!"));
} else {
    echo json_encode(array('estado'=>2, 'mensagem'=>"Email inválido!"));
}

function gerarSenha($tamanho=8, $forca=0) {
    $vogais = 'aeuy';
    $consoantes = 'bdghjmnpqrstvz';
    if ($forca >= 1) {
        $consoantes .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($forca >= 2) {
        $vogais .= "AEUY";
    }
    if ($forca >= 4) {
        $consoantes .= '23456789';
    }
    if ($forca >= 8 ) {
        $vogais .= '@#$%';
    }
 
    $senha = '';
    $alt = time() % 2;
    for ($i = 0; $i < $tamanho; $i++) {
        if ($alt == 1) {
            $senha .= $consoantes[(rand() % strlen($consoantes))];
            $alt = 0;
        } else {
            $senha .= $vogais[(rand() % strlen($vogais))];
            $alt = 1;
        }
    }
    return $senha;
}
?>