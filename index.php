<?php

require_once 'config/conexao.php';
require_once 'includes/produtos.php';

$conexao = conectar();
$produtos = listarProdutos($conexao);
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimentação de estoque</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>


    <div class="cabecalho">
        <div class="container">
            <h1>Movimentação de estoque</h1>
        </div>
    </div>

    <div class="container">

        <div class="barra-acoes">
            <button id="btn-novo-produto" class="btn-novo-produto" type="button">Novo Produto</button>
        </div>

        <table class="tabela-produtos">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th>Produto</th>
                    <th class="col-quantidade">Estoque</th>
                    <th class="col-acao">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($produtos) > 0): ?>
                    <?php foreach ($produtos as $prod): ?>
                        <?php
                            $classe_estoque = (intval($prod['quantidade']) <= 50) ? 'estoque-baixo' : 'estoque-ok';
                        ?>
                        <tr data-id="<?php echo intval($prod['id']); ?>" 
                            data-nome="<?php echo htmlspecialchars($prod['nome'], ENT_QUOTES, 'UTF-8'); ?>">
                            <td class="col-id"><?php echo intval($prod['id']); ?></td>
                            <td><?php echo htmlspecialchars($prod['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="col-quantidade <?php echo $classe_estoque; ?>">
                                <?php echo intval($prod['quantidade']); ?>
                            </td>
                            <td class="col-acao">
                                <button class="btn-movimentar" type="button">Movimentar</button>
                                <button class="btn-excluir" type="button">Excluir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="linha-vazia">
                        <td colspan="4" style="text-align: center; padding: 20px; color: #999;">
                            Nenhum produto cadastrado.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>


    </div>

    <div id="modal-movimentacao" class="modal-fundo">
        <div class="modal-conteudo">

            <div class="modal-cabecalho">
                <h2>Registrar Movimentação</h2>
                <button id="btn-fechar-modal" class="btn-fechar" type="button">&times;</button>
            </div>

            <div class="modal-corpo">

                <div class="info-produto">
                    Produto: <strong id="modal-produto-nome">-</strong><br>
                    Estoque atual: <strong id="modal-produto-estoque">0</strong> unidades
                </div>

                <div id="mensagem-feedback" class="mensagem"></div>

                <div class="grupo-campo">
                    <label for="form-tipo">Tipo de Movimentação</label>
                    <select id="form-tipo">
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saida</option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label for="form-quantidade">Quantidade</label>
                    <input type="number" id="form-quantidade" min="1" step="1" placeholder="Ex: 10">
                </div>

                <div class="grupo-campo">
                    <label for="form-observacao">Observacao (opcional)</label>
                    <textarea id="form-observacao" placeholder="Motivo da movimentação..."></textarea>
                </div>

                <button id="btn-registrar" class="btn-registrar" type="button">Registrar</button>

                <h3 class="historico-titulo">Ultimas Movimentações</h3>
                <div id="historico-conteudo">
                    <p class="historico-vazio">Selecione um produto para ver o historico.</p>
                </div>

            </div>
        </div>
    </div>

    <div id="modal-novo-produto" class="modal-fundo">
        <div class="modal-conteudo">

            <div class="modal-cabecalho">
                <h2>Novo Produto</h2>
                <button id="btn-fechar-novo" class="btn-fechar" type="button">&times;</button>
            </div>

            <div class="modal-corpo">

                <div id="mensagem-novo-feedback" class="mensagem"></div>

                <div class="grupo-campo">
                    <label for="novo-nome">Nome do Produto</label>
                    <input type="text" id="novo-nome" maxlength="150" placeholder="Ex: Seringa Descartavel 10ml">
                </div>

                <div class="grupo-campo">
                    <label for="novo-quantidade">Quantidade Inicial</label>
                    <input type="number" id="novo-quantidade" min="0" step="1" value="0" placeholder="Ex: 100">
                </div>

                <button id="btn-salvar-produto" class="btn-registrar" type="button">Cadastrar Produto</button>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="js/app.js"></script>

</body>
</html>
