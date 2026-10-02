<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../api/validacoes.php';

final class ValidacoesTest extends TestCase
{
    public function testRemoveMascaraDeDocumento(): void
    {
        self::assertSame('52998224725', apenasDigitos('529.982.247-25'));
    }

    public function testValidaCpf(): void
    {
        self::assertTrue(cpfValido('529.982.247-25'));
        self::assertFalse(cpfValido('529.982.247-26'));
        self::assertFalse(cpfValido('111.111.111-11'));
    }

    public function testValidaCnpj(): void
    {
        self::assertTrue(cnpjValido('04.252.011/0001-10'));
        self::assertFalse(cnpjValido('04.252.011/0001-11'));
        self::assertFalse(cnpjValido('11.111.111/1111-11'));
    }

    public function testContaCaracteresAcentuados(): void
    {
        self::assertTrue(valorEntre('ação', 4, 4));
        self::assertFalse(valorEntre('ação', 5, 10));
    }
}
