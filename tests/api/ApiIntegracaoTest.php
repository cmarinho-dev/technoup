<?php

use PHPUnit\Framework\TestCase;

final class ApiIntegracaoTest extends TestCase
{
    private function requisitar(string $caminho): array
    {
        $base = getenv('TEST_API_BASE_URL') ?: 'http://localhost/technoPhp/api';
        $cliente = curl_init(rtrim($base, '/') . '/' . ltrim($caminho, '/'));
        self::assertInstanceOf(CurlHandle::class, $cliente);

        curl_setopt_array($cliente, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 10,
        ]);

        $resposta = curl_exec($cliente);
        $erro = curl_error($cliente);
        $codigoHttp = curl_getinfo($cliente, CURLINFO_HTTP_CODE);
        curl_close($cliente);

        self::assertNotFalse($resposta, $erro);
        self::assertSame(200, $codigoHttp, $resposta);

        $json = json_decode($resposta, true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($json);

        return $json;
    }

    public function testListaProdutosPublicos(): void
    {
        $resposta = $this->requisitar('produtos/get.php');

        self::assertSame('ok', $resposta['status']);
        self::assertIsArray($resposta['data']);
        foreach ($resposta['data'] as $produto) {
            self::assertArrayHasKey('id', $produto);
            self::assertArrayHasKey('nome', $produto);
        }
    }

    public function testProdutoInexistenteRetornaListaVazia(): void
    {
        $resposta = $this->requisitar('produtos/get.php?id=-1');

        self::assertSame('ok', $resposta['status']);
        self::assertSame([], $resposta['data']);
    }

    public function testLojaInexistenteRetornaListaVazia(): void
    {
        $resposta = $this->requisitar('lojas/get.php?id=-1');

        self::assertSame('nok', $resposta['status']);
        self::assertSame([], $resposta['data']);
    }

    public function testChatRecusaAcessoAnonimo(): void
    {
        $resposta = $this->requisitar('chat/listar.php');

        self::assertSame('nok', $resposta['status']);
        self::assertStringContainsString('Acesso restrito', $resposta['mensagem']);
    }
}
