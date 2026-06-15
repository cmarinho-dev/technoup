<?php
function salvarMidiasAvaliacao($nomeInput = 'midias', $limiteArquivos = 8)
{
    if (!isset($_FILES[$nomeInput])) {
        return [];
    }

    $arquivos = normalizarArquivosUpload($_FILES[$nomeInput]);
    $arquivos = array_values(array_filter($arquivos, function ($arquivo) {
        return ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }));

    if (count($arquivos) === 0) {
        return [];
    }

    if (count($arquivos) > $limiteArquivos) {
        respostaJson('nok', 'Envie no máximo ' . $limiteArquivos . ' arquivos.');
    }

    $arquivosPreparados = [];
    foreach ($arquivos as $arquivo) {
        $tipoInformado = detectarTipoArquivo($arquivo);
        $tiposImagem = ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'];
        $tiposVideo = ['video/mp4', 'video/webm', 'video/quicktime', 'video/x-m4v'];

        if (in_array($tipoInformado, $tiposImagem, true)) {
            $arquivosPreparados[] = [
                'arquivo' => $arquivo,
                'pasta' => '/imagens/avaliacoes',
                'tipos' => $tiposImagem,
                'limite' => 5,
                'tipo_arquivo' => 'imagem'
            ];
            continue;
        }

        if (in_array($tipoInformado, $tiposVideo, true)) {
            $arquivosPreparados[] = [
                'arquivo' => $arquivo,
                'pasta' => '/videos/avaliacoes',
                'tipos' => $tiposVideo,
                'limite' => 20,
                'tipo_arquivo' => 'video'
            ];
            continue;
        }

        respostaJson('nok', 'Envie imagens ou vídeos válidos.');
    }

    foreach ($arquivosPreparados as $item) {
        if ($item['arquivo']['error'] !== UPLOAD_ERR_OK) {
            respostaJson('nok', 'Erro no upload do arquivo.');
        }

        if ($item['arquivo']['size'] > $item['limite'] * 1024 * 1024) {
            respostaJson('nok', 'Arquivo muito grande. O limite é ' . $item['limite'] . 'MB.');
        }
    }

    $midias = [];
    foreach ($arquivosPreparados as $item) {
        $midia = salvarArquivoRecebido(
            $item['arquivo'],
            $item['pasta'],
            $item['tipos'],
            $item['limite']
        );
        $midia['tipo_arquivo'] = $item['tipo_arquivo'];
        $midias[] = $midia;
    }

    return $midias;
}

function normalizarArquivosUpload($arquivos)
{
    if (!is_array($arquivos['name'])) {
        return [$arquivos];
    }

    $normalizados = [];
    foreach ($arquivos['name'] as $indice => $nome) {
        $normalizados[] = [
            'name' => $nome,
            'type' => $arquivos['type'][$indice] ?? '',
            'tmp_name' => $arquivos['tmp_name'][$indice] ?? '',
            'error' => $arquivos['error'][$indice] ?? UPLOAD_ERR_NO_FILE,
            'size' => $arquivos['size'][$indice] ?? 0
        ];
    }

    return $normalizados;
}

function detectarTipoArquivo($arquivo)
{
    $tipoDetectado = $arquivo['type'] ?? '';
    if (function_exists('finfo_open') && ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoDetectado = finfo_file($finfo, $arquivo['tmp_name']);
        finfo_close($finfo);
    }

    return $tipoDetectado;
}

function salvarImagem($nomePasta, $nomeInput = 'imagem')
{
    $pastaDestino = "/imagens/" . $nomePasta;
    $tiposPermitidos = ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'];
    $tamanhoMaximoMB = 5;

    return salvarArquivo($nomeInput, $pastaDestino, $tiposPermitidos, $tamanhoMaximoMB);
}

function salvarVideo($nomePasta, $nomeInput = 'video')
{
    $pastaDestino = "/videos/" . $nomePasta;
    $tiposPermitidos = ['video/mp4', 'video/webm', 'video/quicktime', 'video/x-m4v'];
    $tamanhoMaximoMB = 20;

    return salvarArquivo($nomeInput, $pastaDestino, $tiposPermitidos, $tamanhoMaximoMB);
}

function salvarArquivo($nomeInput, $pastaDestino, $tiposPermitidos, $tamanhoMaximoMB)
{
    if (!isset($_FILES[$nomeInput]) || $_FILES[$nomeInput]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $arquivo = $_FILES[$nomeInput];
    return salvarArquivoRecebido($arquivo, $pastaDestino, $tiposPermitidos, $tamanhoMaximoMB);
}

function salvarArquivoRecebido($arquivo, $pastaDestino, $tiposPermitidos, $tamanhoMaximoMB)
{
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        respostaJson('nok', 'Erro no upload do arquivo.');
    }

    $tamanhoMaximoBytes = $tamanhoMaximoMB * 1024 * 1024;
    if ($arquivo['size'] > $tamanhoMaximoBytes) {
        respostaJson('nok', 'Arquivo muito grande. O limite é ' . $tamanhoMaximoMB . 'MB.');
    }

    $tipoDetectado = detectarTipoArquivo($arquivo);

    if (!in_array($tipoDetectado, $tiposPermitidos, true)) {
        respostaJson('nok', 'Tipo de arquivo não permitido.');
    }

    $extensoesPorTipo = [
        'image/jpg' => 'jpg',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'video/mp4' => 'mp4',
        'video/webm' => 'webm',
        'video/quicktime' => 'mov',
        'video/x-m4v' => 'm4v'
    ];
    $extensao = $extensoesPorTipo[$tipoDetectado] ?? strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $novoNome = uniqid('', true) . ($extensao ? '.' . $extensao : '');
    $pastaNormalizada = '/' . trim($pastaDestino, '/');
    $destino = dirname(__DIR__) . $pastaNormalizada;

    if (!is_dir($destino) && !mkdir($destino, 0755, true)) {
        respostaJson('nok', 'Não foi possível preparar a pasta de upload.');
    }

    $caminhoCompleto = $destino . DIRECTORY_SEPARATOR . $novoNome;
    if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        respostaJson('nok', 'Não foi possível salvar o arquivo enviado.');
    }

    return [
        'arquivo' => $novoNome,
        'caminho' => '..' . $pastaNormalizada . '/',
        'tipo' => $tipoDetectado,
        'caminho_completo' => $caminhoCompleto
    ];
}

function anexarMidiasAvaliacoes($conexao, $avaliacoes)
{
    if (count($avaliacoes) === 0) {
        return $avaliacoes;
    }

    $ids = array_values(array_unique(array_map(function ($avaliacao) {
        return (int)$avaliacao['id'];
    }, $avaliacoes)));

    $midiasPorAvaliacao = [];
    foreach ($ids as $id) {
        $midiasPorAvaliacao[$id] = [];
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $tipos = str_repeat('i', count($ids));
    $stmt = $conexao->prepare("
        SELECT id, avaliacao_id, arquivo, caminho, tipo_arquivo, criado_em
        FROM avaliacao_item_midia
        WHERE avaliacao_id IN ($placeholders)
        ORDER BY id ASC
    ");
    $stmt->bind_param($tipos, ...$ids);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($linha = $resultado->fetch_assoc()) {
        $avaliacaoId = (int)$linha['avaliacao_id'];
        $midiasPorAvaliacao[$avaliacaoId][] = $linha;
    }
    $stmt->close();

    foreach ($avaliacoes as &$avaliacao) {
        $avaliacao['midias'] = $midiasPorAvaliacao[(int)$avaliacao['id']] ?? [];
    }
    unset($avaliacao);

    return $avaliacoes;
}
