<?php
function apenasDigitos($valor)
{
    return preg_replace('/\D+/', '', (string)$valor);
}

function todosDigitosIguais($valor)
{
    return preg_match('/^(\d)\1+$/', $valor) === 1;
}

function cpfValido($cpf)
{
    $cpf = apenasDigitos($cpf);

    if (strlen($cpf) !== 11 || todosDigitosIguais($cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += (int)$cpf[$i] * (($t + 1) - $i);
        }

        $digito = ((10 * $soma) % 11) % 10;
        if ((int)$cpf[$t] !== $digito) {
            return false;
        }
    }

    return true;
}

function cnpjValido($cnpj)
{
    $cnpj = apenasDigitos($cnpj);

    if (strlen($cnpj) !== 14 || todosDigitosIguais($cnpj)) {
        return false;
    }

    $pesosPrimeiro = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $pesosSegundo = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    $soma = 0;
    for ($i = 0; $i < 12; $i++) {
        $soma += (int)$cnpj[$i] * $pesosPrimeiro[$i];
    }
    $resto = $soma % 11;
    $digitoPrimeiro = $resto < 2 ? 0 : 11 - $resto;

    if ((int)$cnpj[12] !== $digitoPrimeiro) {
        return false;
    }

    $soma = 0;
    for ($i = 0; $i < 13; $i++) {
        $soma += (int)$cnpj[$i] * $pesosSegundo[$i];
    }
    $resto = $soma % 11;
    $digitoSegundo = $resto < 2 ? 0 : 11 - $resto;

    return (int)$cnpj[13] === $digitoSegundo;
}

function valorEntre($valor, $minimo, $maximo)
{
    $tamanho = function_exists('mb_strlen') ? mb_strlen($valor, 'UTF-8') : strlen($valor);
    return $tamanho >= $minimo && $tamanho <= $maximo;
}
