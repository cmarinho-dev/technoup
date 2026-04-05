<?php

$_servidor = "localhost:3306";
$_usuario = "root";
$_senha = "";
$_nomeBanco = "technoup";

$conn = mysqli_connect($_servidor, $_usuario, $_senha, $_nomeBanco);

if (!$conn) {
    die("Erro de conexao: " . mysqli_connect_error());
}

function validarIdentificador($valor)
{
    return is_string($valor) && preg_match('/^[a-zA-Z0-9_]+$/', $valor);
}

function garantirIdentificadorValido($valor, $tipo)
{
    if (!validarIdentificador($valor)) {
        throw new InvalidArgumentException("$tipo invalido.");
    }
}

function montarTiposBind($quantidade)
{
    return str_repeat('s', $quantidade);
}

function criar($tabela, $dados)
{
    global $conn;

    garantirIdentificadorValido($tabela, 'Tabela');

    if (empty($dados) || !is_array($dados)) {
        return false;
    }

    $colunas = array_keys($dados);

    foreach ($colunas as $coluna) {
        garantirIdentificadorValido($coluna, 'Coluna');
    }

    $marcadores = implode(', ', array_fill(0, count($dados), '?'));
    $nomesColunas = implode(', ', $colunas);
    $sql = "INSERT INTO {$tabela} ({$nomesColunas}) VALUES ({$marcadores})";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    $valores = array_values($dados);
    mysqli_stmt_bind_param($stmt, montarTiposBind(count($valores)), ...$valores);

    $sucesso = mysqli_stmt_execute($stmt);
    $idInserido = $sucesso ? mysqli_insert_id($conn) : null;
    mysqli_stmt_close($stmt);

    if (!$sucesso) {
        return false;
    }

    $registroCriado = $dados;

    if ($idInserido) {
        $registroCriado['id'] = $idInserido;
    }

    return $registroCriado;
}

function ler($tabela, $id = null, $nome_coluna_id = 'id')
{
    global $conn;

    garantirIdentificadorValido($tabela, 'Tabela');
    garantirIdentificadorValido($nome_coluna_id, 'Coluna');

    if ($id === null) {
        return mysqli_query($conn, "SELECT * FROM {$tabela}");
    }

    $sql = "SELECT * FROM {$tabela} WHERE {$nome_coluna_id} = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    $valorId = (string) $id;
    mysqli_stmt_bind_param($stmt, 's', $valorId);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    return $resultado;
}

function atualizar($tabela, $id, $dados, $nome_coluna_id = 'id')
{
    global $conn;

    garantirIdentificadorValido($tabela, 'Tabela');
    garantirIdentificadorValido($nome_coluna_id, 'Coluna');

    if (empty($dados) || !is_array($dados)) {
        return false;
    }

    $partesAtualizacao = [];

    foreach (array_keys($dados) as $coluna) {
        garantirIdentificadorValido($coluna, 'Coluna');
        $partesAtualizacao[] = "{$coluna} = ?";
    }

    $sql = "UPDATE {$tabela} SET " . implode(', ', $partesAtualizacao) . " WHERE {$nome_coluna_id} = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    $valores = array_values($dados);
    $valores[] = (string) $id;
    mysqli_stmt_bind_param($stmt, montarTiposBind(count($valores)), ...$valores);

    $sucesso = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $sucesso;
}

function deletar($tabela, $id, $nome_coluna_id = 'id')
{
    global $conn;

    garantirIdentificadorValido($tabela, 'Tabela');
    garantirIdentificadorValido($nome_coluna_id, 'Coluna');

    $sql = "DELETE FROM {$tabela} WHERE {$nome_coluna_id} = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    $valorId = (string) $id;
    mysqli_stmt_bind_param($stmt, 's', $valorId);

    $sucesso = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $sucesso;
}
