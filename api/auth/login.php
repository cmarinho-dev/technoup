<?php
include_once '../conexao.php';

// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (empty($email) || empty($senha)) {
    respostaJson('nok', 'Email e senha são obrigatórios.');
}

// Busca a conta pelo email
$stmt = $conexao->prepare("SELECT * FROM conta WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();

if ($resultado->num_rows === 0) {
    respostaJson('nok', 'Credenciais inválidas.');
}

$usuario = $resultado->fetch_assoc();

if ($usuario['senha'] !== $senha) {
    respostaJson('nok', 'Credenciais inválidas.');
}

// Remove a senha antes de salvar na sessão
unset($usuario['senha']);

session_start();
$_SESSION['usuario'] = $usuario;

// Se for lojista, busca os dados da loja e salva na sessão
$loja = null;
if ($usuario['tipo'] === 'lojista') {
    $stmtLoja = $conexao->prepare("SELECT * FROM loja WHERE conta_id = ?");
    $stmtLoja->bind_param('i', $usuario['id']);
    $stmtLoja->execute();
    $resultadoLoja = $stmtLoja->get_result();
    $stmtLoja->close();

    if ($resultadoLoja->num_rows > 0) {
        $loja = $resultadoLoja->fetch_assoc();
        $_SESSION['loja'] = $loja;
    }
}

$conexao->close();
respostaJson('ok', 'Login realizado com sucesso.', [
    'usuario' => $usuario,
    'loja'    => $loja
]);
