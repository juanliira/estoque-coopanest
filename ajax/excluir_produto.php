<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../config/conexao.php';
require_once '../includes/produtos.php';

$resposta = array(
    'sucesso' => false,
    'mensagem' => ''
);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $resposta['mensagem'] = 'Metodo nao permitido.';
    echo json_encode($resposta);
    exit;
}

$produto_id = isset($_POST['produto_id']) ? intval($_POST['produto_id']) : 0;

if ($produto_id <= 0) {
    $resposta['mensagem'] = 'Produto invalido.';
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

$stmt = $conexao->prepare("DELETE FROM produtos WHERE id = ?");
$stmt->bind_param('i', $produto_id);
$resultado = $stmt->execute();

$stmt->close();

if (!$resultado) {
    $resposta['mensagem'] = 'Erro ao excluir o produto. Tente novamente.';
    $conexao->close();
    echo json_encode($resposta);
    exit;
}

$resposta['sucesso'] = true;
$resposta['mensagem'] = 'Produto excluido com sucesso!';

$conexao->close();

echo json_encode($resposta);
