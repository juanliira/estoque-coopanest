/**
 * Script principal do sistema
 * Utiliza jQuery para AJAX e manipulacao do DOM
 */

$(document).ready(function () {

    var produtoSelecionado = 0;

    $('.btn-movimentar').on('click', function (e) {
        e.stopPropagation();
        var linha = $(this).closest('tr');
        abrirModal(linha);
    });

    $('.tabela-produtos tbody tr').on('click', function (e) {
        if (!$(e.target).hasClass('btn-excluir')) {
            abrirModal($(this));
        }
    });

    $('.tabela-produtos').on('click', '.btn-excluir', function (e) {
        e.stopPropagation();
        var tr = $(this).closest('tr');
        var id = tr.data('id');
        var nome = tr.data('nome');

        if (!confirm('Tem certeza que deseja excluir o produto "' + nome + '"?\n\nTodas as movimentacões deste produto serão apagadas.')) {
            return;
        }

        $.ajax({
            url: 'ajax/excluir_produto.php',
            type: 'POST',
            dataType: 'json',
            data: { produto_id: id },
            success: function (resp) {
                if (resp.sucesso) {
                    tr.fadeOut(300, function () {
                        $(this).remove();
                        if ($('.tabela-produtos tbody tr').length === 0) {
                            $('.tabela-produtos tbody').append(
                                '<tr id="linha-vazia"><td colspan="4" style="text-align:center;padding:20px;color:#999;">Nenhum produto cadastrado.</td></tr>'
                            );
                        }
                    });
                } else {
                    alert('Erro: ' + resp.mensagem);
                }
            },
            error: function () {
                alert('Erro de comunicacão com o servidor.');
            }
        });
    });

    function abrirModal(linha) {
        var id = linha.data('id');
        var nome = linha.data('nome');
        var qtd = linha.find('.col-quantidade').text().trim();

        produtoSelecionado = id;

        $('#modal-produto-nome').text(nome);
        $('#modal-produto-estoque').text(qtd);
        $('#form-tipo').val('entrada');
        $('#form-quantidade').val('');
        $('#form-observacao').val('');
        esconderMensagem();

        $('#modal-movimentacao').fadeIn(200);
        carregarHistorico(id);
    }

    $('#btn-fechar-modal').on('click', function () {
        fecharModal();
    });

    $('#modal-movimentacao').on('click', function (e) {
        if ($(e.target).is('#modal-movimentacao')) {
            fecharModal();
        }
    });

    $(document).on('keydown', function (e) {
        if (e.keyCode === 27) {
            fecharModal();
        }
    });

    function fecharModal() {
        $('#modal-movimentacao').fadeOut(200);
        produtoSelecionado = 0;
    }

    $('#btn-registrar').on('click', function () {
        var tipo = $('#form-tipo').val();
        var quantidade = $('#form-quantidade').val();
        var observacao = $('#form-observacao').val();

        if (!tipo) {
            mostrarMensagem('Selecione o tipo de movimentação.', 'erro');
            return;
        }

        if (!quantidade || quantidade !== String(parseInt(quantidade)) || parseInt(quantidade) <= 0) {
            mostrarMensagem('Informe uma quantidade válida (número inteiro maior que zero).', 'erro');
            return;
        }

        if (produtoSelecionado <= 0) {
            mostrarMensagem('Nenhum produto selecionado.', 'erro');
            return;
        }

        var botao = $(this);
        botao.prop('disabled', true).text('Registrando...');

        $.ajax({
            url: 'ajax/registrar_movimentacao.php',
            type: 'POST',
            dataType: 'json',
            data: {
                produto_id: produtoSelecionado,
                tipo: tipo,
                quantidade: parseInt(quantidade),
                observacao: observacao
            },
            success: function (resp) {
                if (resp.sucesso) {
                    mostrarMensagem(resp.mensagem, 'sucesso');

                    var linha = $('tr[data-id="' + produtoSelecionado + '"]');
                    linha.find('.col-quantidade').text(resp.nova_quantidade);

                    $('#modal-produto-estoque').text(resp.nova_quantidade);

                    var celula = linha.find('.col-quantidade');
                    celula.removeClass('estoque-baixo estoque-ok');
                    if (resp.nova_quantidade <= 50) {
                        celula.addClass('estoque-baixo');
                    } else {
                        celula.addClass('estoque-ok');
                    }

                    $('#form-quantidade').val('');
                    $('#form-observacao').val('');

                    carregarHistorico(produtoSelecionado);
                } else {
                    mostrarMensagem(resp.mensagem, 'erro');
                }
            },
            error: function () {
                mostrarMensagem('Erro de comunicacão com o servidor. Tente novamente.', 'erro');
            },
            complete: function () {
                botao.prop('disabled', false).text('Registrar');
            }
        });
    });

    function carregarHistorico(produto_id) {
        var container = $('#historico-conteudo');
        container.html('<p class="carregando">Carregando historico...</p>');

        $.ajax({
            url: 'ajax/buscar_historico.php',
            type: 'GET',
            dataType: 'json',
            data: {
                produto_id: produto_id
            },
            success: function (resp) {
                if (resp.sucesso && resp.historico.length > 0) {
                    var html = '<table class="tabela-historico">';
                    html += '<thead><tr>';
                    html += '<th>Data</th>';
                    html += '<th>Tipo</th>';
                    html += '<th>Qtd</th>';
                    html += '<th>Observacao</th>';
                    html += '</tr></thead><tbody>';

                    for (var i = 0; i < resp.historico.length; i++) {
                        var mov = resp.historico[i];
                        var classeTipo = (mov.tipo === 'entrada') ? 'tipo-entrada' : 'tipo-saida';
                        var textoTipo = (mov.tipo === 'entrada') ? 'Entrada' : 'Saida';

                        html += '<tr>';
                        html += '<td>' + mov.data_formatada + '</td>';
                        html += '<td class="' + classeTipo + '">' + textoTipo + '</td>';
                        html += '<td>' + mov.quantidade + '</td>';
                        html += '<td>' + (mov.observacao || '-') + '</td>';
                        html += '</tr>';
                    }

                    html += '</tbody></table>';
                    container.html(html);
                } else {
                    container.html('<p class="historico-vazio">Nenhuma movimentação registrada para este produto.</p>');
                }
            },
            error: function () {
                container.html('<p class="historico-vazio">Erro ao carregar histórico.</p>');
            }
        });
    }

    function mostrarMensagem(texto, tipo) {
        var elemento = $('#mensagem-feedback');
        elemento.removeClass('mensagem-sucesso mensagem-erro');
        elemento.addClass(tipo === 'sucesso' ? 'mensagem-sucesso' : 'mensagem-erro');
        elemento.text(texto).fadeIn(150);

        if (tipo === 'sucesso') {
            setTimeout(function () {
                elemento.fadeOut(150);
            }, 4000);
        }
    }

    function esconderMensagem() {
        $('#mensagem-feedback').hide();
    }

    $('#btn-novo-produto').on('click', function () {
        $('#novo-nome').val('');
        $('#novo-quantidade').val('0');
        $('#mensagem-novo-feedback').hide();
        $('#modal-novo-produto').fadeIn(200);
        $('#novo-nome').focus();
    });

    $('#btn-fechar-novo').on('click', function () {
        $('#modal-novo-produto').fadeOut(200);
    });

    $('#modal-novo-produto').on('click', function (e) {
        if ($(e.target).is('#modal-novo-produto')) {
            $('#modal-novo-produto').fadeOut(200);
        }
    });

    $('#btn-salvar-produto').on('click', function () {
        var nome = $.trim($('#novo-nome').val());
        var quantidade = $('#novo-quantidade').val();

        if (!nome) {
            mostrarMensagemNovo('Informe o nome do produto.', 'erro');
            return;
        }

        if (quantidade === '' || quantidade !== String(parseInt(quantidade)) || parseInt(quantidade) < 0) {
            mostrarMensagemNovo('Quantidade deve ser um número inteiro (zero ou maior).', 'erro');
            return;
        }

        var botao = $(this);
        botao.prop('disabled', true).text('Cadastrando...');

        $.ajax({
            url: 'ajax/cadastrar_produto.php',
            type: 'POST',
            dataType: 'json',
            data: {
                nome: nome,
                quantidade: parseInt(quantidade)
            },
            success: function (resp) {
                if (resp.sucesso) {
                    mostrarMensagemNovo(resp.mensagem, 'sucesso');

                    $('#linha-vazia').remove();

                    var prod = resp.produto;
                    var classeEstoque = (parseInt(prod.quantidade) <= 50) ? 'estoque-baixo' : 'estoque-ok';
                    var novaLinha = '<tr data-id="' + parseInt(prod.id) + '" data-nome="' + $('<span>').text(prod.nome).html() + '">';
                    novaLinha += '<td class="col-id">' + parseInt(prod.id) + '</td>';
                    novaLinha += '<td>' + $('<span>').text(prod.nome).html() + '</td>';
                    novaLinha += '<td class="col-quantidade ' + classeEstoque + '">' + parseInt(prod.quantidade) + '</td>';
                    novaLinha += '<td class="col-acao">';
                    novaLinha += '<button class="btn-movimentar" type="button">Movimentar</button>';
                    novaLinha += '<button class="btn-excluir" type="button">Excluir</button>';
                    novaLinha += '</td>';
                    novaLinha += '</tr>';

                    $('.tabela-produtos tbody').append(novaLinha);

                    bindEventosLinha($('.tabela-produtos tbody tr:last'));

                    $('#novo-nome').val('');
                    $('#novo-quantidade').val('0');

                    setTimeout(function () {
                        $('#modal-novo-produto').fadeOut(200);
                    }, 1000);
                } else {
                    mostrarMensagemNovo(resp.mensagem, 'erro');
                }
            },
            error: function () {
                mostrarMensagemNovo('Erro de comunicacão com o servidor.', 'erro');
            },
            complete: function () {
                botao.prop('disabled', false).text('Cadastrar Produto');
            }
        });
    });

    function mostrarMensagemNovo(texto, tipo) {
        var elemento = $('#mensagem-novo-feedback');
        elemento.removeClass('mensagem-sucesso mensagem-erro');
        elemento.addClass(tipo === 'sucesso' ? 'mensagem-sucesso' : 'mensagem-erro');
        elemento.text(texto).fadeIn(150);

        if (tipo === 'sucesso') {
            setTimeout(function () {
                elemento.fadeOut(150);
            }, 4000);
        }
    }

    function bindEventosLinha(linha) {
        linha.find('.btn-movimentar').on('click', function (e) {
            e.stopPropagation();
            abrirModal($(this).closest('tr'));
        });

        linha.on('click', function (e) {
            if (!$(e.target).hasClass('btn-excluir')) {
                abrirModal($(this));
            }
        });

        linha.find('.btn-excluir').on('click', function (e) {
            e.stopPropagation();
            var tr = $(this).closest('tr');
            var id = tr.data('id');
            var nome = tr.data('nome');

            if (!confirm('Tem certeza que deseja excluir o produto "' + nome + '"?\n\nTodas as movimentacões deste produto serão apagadas.')) {
                return;
            }

            $.ajax({
                url: 'ajax/excluir_produto.php',
                type: 'POST',
                dataType: 'json',
                data: { produto_id: id },
                success: function (resp) {
                    if (resp.sucesso) {
                        tr.fadeOut(300, function () {
                            $(this).remove();

                            if ($('.tabela-produtos tbody tr').length === 0) {
                                $('.tabela-produtos tbody').append(
                                    '<tr id="linha-vazia"><td colspan="4" style="text-align:center;padding:20px;color:#999;">Nenhum produto cadastrado.</td></tr>'
                                );
                            }
                        });
                    } else {
                        alert('Erro: ' + resp.mensagem);
                    }
                },
                error: function () {
                    alert('Erro de comunicacão com o servidor.');
                }
            });
        });
    }

});
