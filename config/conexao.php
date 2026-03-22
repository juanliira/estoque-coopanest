<?php

define('DB_HOST', 'db');
define('DB_USUARIO', 'root');
define('DB_SENHA', 'root');
define('DB_NOME', 'estoque_coopanest');

function conectar()
{
    $conexao = new mysqli(DB_HOST, DB_USUARIO, DB_SENHA, DB_NOME);

    if ($conexao->connect_error) {
        die('Erro ao conectar no banco: ' . $conexao->connect_error);
    }

    $conexao->set_charset('utf8');

    return $conexao;
}
