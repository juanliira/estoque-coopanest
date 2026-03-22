# Sistema de movimentação de estoque

Sistema para controle de entradas e saídas de produtos em estoque, desenvolvido com PHP, MySQL e jQuery.

## Requisitos

- Docker e Docker Compose

## Instalação

### 1. Clonar o repositório

```bash
git clone https://github.com/usuario/estoque-coopanest.git
cd estoque-coopanest
```

### 2. Subir os containers

```bash
docker-compose up -d
```

Isso vai criar dois containers:

- **web**: PHP com Apache (porta 8080) — código compatível com PHP 5.3
- **db**: MySQL 5.7 (porta 3310)

O banco de dados `estoque_coopanest` vai ser criado automaticamente na primeira execução, com as tabelas e os dados de exemplo.

### 3. Acessar

```
http://localhost:8080
```

### 4. Parar

```bash
docker-compose down
```

## Estrutura de pastas

```
estoque-coopanest/
├── config/
│   └── conexao.php
├── includes/
│   ├── produtos.php
│   └── movimentacoes.php
├── ajax/
│   ├── registrar_movimentacao.php
│   ├── buscar_historico.php
│   ├── cadastrar_produto.php
│   └── excluir_produto.php
├── css/
│   └── estilo.css
├── js/
│   └── app.js
├── sql/
│   └── banco.sql
├── index.php
├── Dockerfile
├── docker-compose.yml
└── README.md
```

## Funcionalidades

- **Listagem de produtos**: tabela com ID, nome e quantidade atual, carregada do banco ao abrir a página.
- **Registrar movimentação**: modal com formulário para entrada ou saída, com envio via AJAX e validação no front-end e no servidor.
- **Atualização sem reload**: após registrar, a quantidade na tabela atualiza instantaneamente via JavaScript.
- **Histórico do produto**: no modal, exibe as 10 últimas movimentações do produto selecionado, atualizadas automaticamente após novo registro.
- **Cadastro de produtos**: permite adicionar novos produtos com nome e quantidade inicial.
- **Exclusão de produtos**: remove o produto e todas as suas movimentações.

## Tecnologias

| Tecnologia | Versão | Papel |
|------------|--------|-------|
| PHP | 5.3+ | Backend, validações, comunicação com o banco |
| MySQL | 5.7 | Armazenamento de produtos e movimentações |
| MySQLi | - | Extensão PHP com suporte a prepared statements |
| jQuery | 1.12.4 | Manipulação do DOM e chamadas AJAX |
| HTML5/CSS3 | - | Estrutura e aparência da página |
| Docker | - | Containerização do ambiente |

## Segurança

- **SQL Injection**: prevenido com prepared statements em todas as queries que recebem dados do usuário.
- **XSS**: dados de entrada sanitizados com `htmlspecialchars()`.
- **Validação em camadas**: HTML, JavaScript e PHP.
- **ENUM no banco**: a coluna `tipo` só aceita 'entrada' ou 'saída', rejeitando qualquer outro valor.
- **FOREIGN KEY com CASCADE**: garante integridade referencial entre produtos e movimentações.

## Observações técnicas

- jQuery carregado via CDN (versão 1.12.4).
- CSS escrito manualmente, sem frameworks.
- Nenhum framework PHP utilizado (conforme requisito do teste).