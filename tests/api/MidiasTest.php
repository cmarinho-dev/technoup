<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../api/midias.php';

final class MidiasTest extends TestCase
{
    public function testNormalizaDadosDeDoisArquivosSemMisturarTamanhos(): void
    {
        $arquivos = [
            'name' => ['foto.jpg', 'video.mp4'],
            'type' => ['image/jpeg', 'video/mp4'],
            'tmp_name' => ['foto.tmp', 'video.tmp'],
            'error' => [UPLOAD_ERR_OK, UPLOAD_ERR_OK],
            'size' => [1024, 2048],
        ];

        $normalizados = normalizarArquivosUpload($arquivos);

        self::assertCount(2, $normalizados);
        self::assertSame('foto.jpg', $normalizados[0]['name']);
        self::assertSame(1024, $normalizados[0]['size']);
        self::assertSame('video.mp4', $normalizados[1]['name']);
        self::assertSame(2048, $normalizados[1]['size']);
    }
}
