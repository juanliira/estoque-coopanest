<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config/conexao.php';
require_once '../includes/produtos.php';
require_once '../includes/movimentacoes.php';

$resposta = array(
    'sucesso' => false,
    'mensagem' => '',
    'nova_quantidade' => 0
);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $resposta['mensagem'] = 'Metodo nao permitido.';
    echo json_encode($resposta);
    exit;
}

$produto_id = isset($_POST['produto_id']) ? intval($_POST['produto_id']) : 0;
$tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
$quantidade_raw = isset($_POST['quantidade']) ? trim($_POST['quantidade']) : '';
$observacao = isset($_POST['observacao']) ? trim($_POST['observacao']) : '';

if ($quantidade_raw === '' || $quantidade_raw !== strval(intval($quantidade_raw))) {
    $resposta['mensagem'] = 'A quantidade deve ser um numero inteiro.';
    echo json_encode($resposta);
    exit;
}
$quantidade = intval($quantidade_raw);

$observacao = htmlspecialchars($observacao, ENT_QUOTES, 'UTF-8');

if ($produto_id <= 0) {
    $resposta['mensagem'] = 'Produto invalido.';
    echo json_encode($resposta);
    exit;
}

if ($tipo !== 'entrada' && $tipo !== 'saida') {
    $resposta['mensagem'] = 'Tipo de movimentacao invalido. Use "entrada" ou "saida".';
    echo json_encode($resposta);
    exit;
}

if ($quantidade <= 0) {
    $resposta['mensagem'] = 'A quantidade deve ser maior que zero.';
    echo json_encode($resposta);
    exit;
}

$conexao = conectar();

$produto = buscarProduto($conexao, $produto_id);

if (!$produto) {
    $resposta['mensagem'] = 'Produto nao encontrado.';
    $conexao->close();
    echo json_encode($resposta);
    exit;
}

if ($tipo === 'saida' && $quantidade > intval($produto['quantidade'])) {
    $resposta['mensagem'] = 'Estoque insuficiente. Quantidade disponivel: ' . $produto['quantidade'];
    $conexao->close();
    echo json_encode($resposta);
    exit;
}

$movimentou = registrarMovimentacao($conexao, $produto_id, $tipo, $quantidade, $observacao);

if (!$movimentou) {
    $resposta['mensagem'] = 'Erro ao registrar a movimentacao. Tente novamente.';
    $conexao->close();
    echo json_encode($resposta);
    exit;
}

$atualizou = atualizarEstoque($conexao, $produto_id, $tipo, $quantidade);

if (!$atualizou) {
    $resposta['mensagem'] = 'Movimentacao registrada, mas houve erro ao atualizar o estoque.';
    $conexao->close();
    echo json_encode($resposta);
    exit;
}

$produto_atualizado = buscarProduto($conexao, $produto_id);

$resposta['sucesso'] = true;
$resposta['mensagem'] = 'Movimentacao registrada com sucesso!';
$resposta['nova_quantidade'] = intval($produto_atualizado['quantidade']);

$conexao->close();

echo json_encode($resposta);
