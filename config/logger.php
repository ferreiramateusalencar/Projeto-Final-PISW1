<?php
function registrarErro(Throwable $erro): void
{
    $diretorioLogs = __DIR__ . '/../logs';

    if (!is_dir($diretorioLogs)) {
        mkdir($diretorioLogs, 0777, true);
    }

    $mensagem = '[' . date('Y-m-d H:i:s') . '] '
        . $erro->getMessage()
        . ' em ' . $erro->getFile()
        . ':' . $erro->getLine()
        . PHP_EOL;

    error_log($mensagem, 3, $diretorioLogs . '/app.log');
}
