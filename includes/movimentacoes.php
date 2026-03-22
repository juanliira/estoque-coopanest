<?php

function registrarMovimentacao($conexao, $produto_id, $tipo, $quantidade, $observacao)
{
    $sql = "INSERT INTO movimentacoes (produto_id, tipo, quantidade, observacao, data_registro) 
            VALUES (?, ?, ?, ?, NOW())";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('isis', $produto_id, $tipo, $quantidade, $observacao);
    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
}

function buscarHistorico($conexao, $produto_id, $limite)
{
    $sql = "SELECT tipo, quantidade, observacao, 
                   DATE_FORMAT(data_registro, '%d/%m/%Y %H:%i') as data_formatada
            FROM movimentacoes 
            WHERE produto_id = ? 
            ORDER BY data_registro DESC 
            LIMIT ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('ii', $produto_id, $limite);
    $stmt->execute();
    $stmt->bind_result($tipo, $quantidade, $observacao, $data_formatada);

    $historico = array();

    while ($stmt->fetch()) {
        $historico[] = array(
            'tipo' => $tipo,
            'quantidade' => $quantidade,
            'observacao' => $observacao,
            'data_formatada' => $data_formatada
        );
    }

    $stmt->close();

    return $historico;
}