<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config/conexao.php';
require_once '../includes/movimentacoes.php';

$resposta = array(
    'sucesso' => false,
    'mensagem' => '',
    'historico' => array()
);

$produto_id = isset($_GET['produto_id']) ? intval($_GET['produto_id']) : 0;

if ($produto_id <= 0) {
    $resposta['mensagem'] = 'Produto invalido.';
    echo json_encode($resposta);
    exit;
}

$conexao = conectar();

$historico = buscarHistorico($conexao, $produto_id, 10);

$resposta['sucesso'] = true;
$resposta['historico'] = $historico;

$conexao->close();

echo json_encode($resposta);
