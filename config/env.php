<?php
function carregarEnv(string $caminho): void
{
    if (!file_exists($caminho)) {
        throw new RuntimeException('Arquivo .env não encontrado. Crie o arquivo com base no .env.example.');
    }

    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($linhas as $linha) {
        $linha = trim($linha);

        if ($linha === '' || substr($linha, 0, 1) === '#') {
            continue;
        }

        $partes = explode('=', $linha, 2);

        if (count($partes) !== 2) {
            continue;
        }

        $chave = trim($partes[0]);
        $valor = trim($partes[1]);
        $valor = trim($valor, " \t\n\r\0\x0B\"'");

        $_ENV[$chave] = $valor;
        putenv($chave . '=' . $valor);
    }
}

function env(string $chave, ?string $padrao = null): string
{
    $valor = $_ENV[$chave] ?? getenv($chave);

    if ($valor === false || $valor === null) {
        if ($padrao !== null) {
            return $padrao;
        }

        throw new RuntimeException('Variável de ambiente não configurada: ' . $chave);
    }

    return (string) $valor;
}
