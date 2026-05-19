<?php
// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = [])
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

function salvarImagem($pastaDestino)
{
    if (isset($_FILES['imagem'])) {
        $arquivo = $_FILES['imagem'];

        // Validações básicas
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            die("Erro no upload do arquivo.");
        }
        
        $tamanhoMaximo = 5 * 1024 * 1024; // 5MB
        if ($arquivo['size'] > $tamanhoMaximo) {
            die("Arquivo muito grande. O limite é 5MB.");
        }

        // Valida o tipo da imagem
        $permitidos = ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($arquivo['type'], $permitidos)) {
            die("Tipo de arquivo não permitido.");
        }

        // Renomeia o arquivo para evitar conflitos (usando uniqid)
        $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid('img_') . '.' . $extensao;

        // Move a imagem para a pasta no servidor
        $destino = '../imagems/' . $pastaDestino;
        if (!is_dir($destino)) {
            mkdir($destino, 0755, true); // Cria a pasta se não existir
        }

        $caminhoCompleto = $destino . $novoNome;

        move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto);
    }
}