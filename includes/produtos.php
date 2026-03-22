<?php

function listarProdutos($conexao)
{
    $sql = "SELECT id, nome, quantidade FROM produtos ORDER BY nome ASC, id ASC";
    $resultado = $conexao->query($sql);

    $produtos = array();

    if ($resultado && $resultado->num_rows > 0) {
        while ($linha = $resultado->fetch_assoc()) {
            $produtos[] = $linha;
        }
    }

    return $produtos;
}

function buscarProduto($conexao, $id)
{
    $stmt = $conexao->prepare("SELECT id, nome, quantidade FROM produtos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($prod_id, $prod_nome, $prod_quantidade);

    $produto = null;

    if ($stmt->fetch()) {
        $produto = array(
            'id' => $prod_id,
            'nome' => $prod_nome,
            'quantidade' => $prod_quantidade
        );
    }

    $stmt->close();

    return $produto;
}

function cadastrarProduto($conexao, $nome, $quantidade)
{
    $stmt = $conexao->prepare("INSERT INTO produtos (nome, quantidade) VALUES (?, ?)");
    $stmt->bind_param('si', $nome, $quantidade);
    $resultado = $stmt->execute();

    if (!$resultado) {
        $stmt->close();
        return false;
    }

    $novo_id = $conexao->insert_id;
    $stmt->close();

    return $novo_id;
}

function atualizarEstoque($conexao, $produto_id, $tipo, $quantidade)
{
    if ($tipo === 'entrada') {
        $sql = "UPDATE produtos SET quantidade = quantidade + ? WHERE id = ?";
    } else {
        $sql = "UPDATE produtos SET quantidade = quantidade - ? WHERE id = ?";
    }

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('ii', $quantidade, $produto_id);
    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
}