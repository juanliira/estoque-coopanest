<?php
/**
 * Endpoint AJAX para cadastrar um novo produto
 * Recebe POST com nome e a quantidade
 * Retorna JSON com o status e dados do produto criado
 */

header('Content-Type: application/json; charset=utf-8');

require_once '../config/conexao.php';
require_once '../includes/produtos.php';

$resposta = array(
    'sucesso' => false,
    'mensagem' => '',
    'produto' => null
);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $resposta['mensagem'] = 'Metodo nao permitido.';
    echo json_encode($resposta);
    exit;
}

$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$quantidade_raw = isset($_POST['quantidade']) ? trim($_POST['quantidade']) : '0';

if ($nome === '') {
    $resposta['mensagem'] = 'Informe o nome do produto.';
    echo json_encode($resposta);
    exit;
}

if (strlen($nome) > 150) {
    $resposta['mensagem'] = 'O nome deve ter no maximo 150 caracteres.';
    echo json_encode($resposta);
    exit;
}

$nome = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');

if ($quantidade_raw !== strval(intval($quantidade_raw))) {
    $resposta['mensagem'] = 'A quantidade deve ser um numero inteiro.';
    echo json_encode($resposta);
    exit;
}

$quantidade = intval($quantidade_raw);

if ($quantidade < 0) {
    $resposta['mensagem'] = 'A quantidade nao pode ser negativa.';
    echo json_encode($resposta);
    exit;
}

$conexao = conectar();

$novo_id = cadastrarProduto($conexao, $nome, $quantidade);

if (!$novo_id) {
    $resposta['mensagem'] = 'Erro ao cadastrar o produto. Tente novamente.';
    $conexao->close();
    echo json_encode($resposta);
    exit;
}

$produto = buscarProduto($conexao, $novo_id);

$resposta['sucesso'] = true;
$resposta['mensagem'] = 'Produto cadastrado com sucesso!';
$resposta['produto'] = $produto;

$conexao->close();

echo json_encode($resposta);