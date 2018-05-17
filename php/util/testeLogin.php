<?php
require 'conexao.php';
require 'classes/contador.class.php';
require 'classes/funcionario.class.php';
require 'classes/empresa.class.php';

session_start();

//$usuario = new Empresa();

$email = $_POST['email'];
$senha = $_POST['senha'];

$result = DBselect("contador");

$usuario = new Contador();
$usuario->email = $email;
$usuario->senha = $senha;
$existe = count($result)==0?0:1;

$result = $usuario->login($existe);

if ($result['estado']==1 || $result['estado']==3) {
    echo json_encode($result);
    $_SESSION['usuario_logado'] = 1;
    exit; 
}

$usuario = new Empresa();
$usuario->email = $email;
$usuario->senha = $senha;

$result1 = $usuario->login();

if ($result1['estado']==4) {
    echo json_encode($result1);
    $_SESSION['usuario_logado'] = 2;
    exit; 
}

$usuario = new Funcionario();
$usuario->email = $email;
$usuario->senha = $senha;
$result2 = $usuario->login();

if ($result2!=0) {
    echo json_encode($result2);
    $_SESSION['usuario_logado'] = 3;
    exit;
}

echo json_encode($result);
exit;
?>