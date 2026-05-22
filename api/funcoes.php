<?php
// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = [])
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

function salvarImagem($nomePasta)
{
    $nomeInput = "imagem";
    $pastaDestino = "/imagens/" . $nomePasta;
    $tiposPermitidos = ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'];
    $tamanhoMaximoMB = 5;

    salvarArquivo($nomeInput, $pastaDestino, $tiposPermitidos, $tamanhoMaximoMB);
}

function salvarVideo($nomePasta)
{
    $nomeInput = "video";
    $pastaDestino = "/videos/" . $nomePasta;
    $tiposPermitidos = ['video/mp4', 'video/webm'];
    $tamanhoMaximoMB = 20;

    salvarArquivo($nomeInput, $pastaDestino, $tiposPermitidos, $tamanhoMaximoMB);
}

function salvarArquivo($nomeInput, $pastaDestino, $tiposPermitidos, $tamanhoMaximoMB)
{
    if (isset($_FILES[$nomeInput])) {
        $arquivo = $_FILES[$nomeInput];

        // Validações básicas
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            die("Erro no upload do arquivo.");
        }

        define("MB", 1024 * 1024);
        if ($arquivo['size'] > $tamanhoMaximoMB * MB) {
            die("Arquivo muito grande. O limite é " . MB . "MB.");
        }

        // Valida o tipo da imagem
        if (!in_array($arquivo['type'], $tiposPermitidos)) {
            die("Tipo de arquivo não permitido.");
        }

        // Renomeia o arquivo para evitar conflitos (usando uniqid)
        $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid() . '.' . $extensao;

        // Move a imagem para a pasta no servidor
        $destino = '..' . $pastaDestino;
        if (!is_dir($destino)) {
            mkdir($destino, 0755, true); // Cria a pasta se não existir
        }

        $caminhoCompleto = $destino . $novoNome;

        move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto);
    }
}