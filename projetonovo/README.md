# Sistema de Estoque - Materiais de Construção

Um sistema completo de gerenciamento de estoque para materiais de construção, desenvolvido em PHP com MySQL.

## 🚀 Funcionalidades

- ✅ Gerenciamento de produtos e fornecedores
- ✅ Controle de entradas e saídas de estoque
- ✅ Histórico completo de movimentações
- ✅ API RESTful para integração
- ✅ Interface web responsiva
- ✅ Validação de dados e tratamento de erros
- ✅ Transações de banco de dados para consistência
- ✅ Arquitetura orientada a objetos

## 📋 Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache/Nginx com mod_rewrite
- Extensão MySQLi habilitada

## 🛠️ Instalação

### 1. Clone ou baixe o projeto
\`\`\`bash
git clone <repository-url>
cd construction-inventory-system
\`\`\`

### 2. Configure o banco de dados
\`\`\`bash
# Acesse o MySQL
mysql -u root -p

# Execute o script de criação
source scripts/database_schema.sql
\`\`\`

### 3. Configure a conexão
Edite o arquivo `config/database.php` com suas credenciais:
\`\`\`php
private const DB_HOST = 'localhost';
private const DB_NAME = 'construcao';
private const DB_USER = 'seu_usuario';
private const DB_PASS = 'sua_senha';
\`\`\`

### 4. Configure o servidor web
- **XAMPP/WAMP**: Coloque os arquivos na pasta `htdocs`
- **Apache**: Configure um virtual host apontando para o diretório do projeto
- **Nginx**: Configure um server block

### 5. Acesse o sistema
Abra o navegador e acesse: `http://localhost/seu-projeto/`

## 📚 Estrutura do Projeto

\`\`\`
construction-inventory-system/
├── api/                    # Endpoints da API
│   ├── products.php       # Gerenciamento de produtos
│   ├── stock_entry.php    # Entradas de estoque
│   ├── stock_exit.php     # Saídas de estoque
│   └── stock_history.php  # Histórico de movimentações
├── classes/               # Classes PHP
│   ├── Product.php        # Gerenciamento de produtos
│   └── StockMovement.php  # Movimentações de estoque
├── config/                # Configurações
│   └── database.php       # Configuração do banco
├── scripts/               # Scripts SQL
│   └── database_schema.sql # Schema do banco
├── index.php              # Interface principal
├── .htaccess             # Configuração Apache
└── README.md             # Este arquivo
\`\`\`

## 🔌 API Endpoints

### Produtos
- `GET /api/products.php` - Lista todos os produtos
- `GET /api/products.php?id=1` - Busca produto específico

### Movimentações de Estoque
- `POST /api/stock_entry.php` - Registra entrada de estoque
- `POST /api/stock_exit.php` - Registra saída de estoque
- `GET /api/stock_history.php` - Histórico de movimentações

### Exemplos de Uso da API

#### Registrar entrada de estoque
\`\`\`javascript
fetch('/api/stock_entry.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        id_produto: 1,
        quantidade: 50,
        observacoes: 'Compra mensal'
    })
});
\`\`\`

#### Registrar saída de estoque
\`\`\`javascript
fetch('/api/stock_exit.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        id_produto: 1,
        quantidade: 10,
        observacoes: 'Venda para cliente X'
    })
});
\`\`\`

## 🔒 Segurança

- Prepared statements para prevenir SQL injection
- Validação de entrada de dados
- Tratamento de erros sem exposição de informações sensíveis
- Headers de segurança configurados
- Transações de banco para consistência de dados

## 🚀 Deploy

### Servidor Tradicional (Apache/Nginx)
1. Faça upload dos arquivos para o servidor
2. Configure o banco de dados
3. Ajuste as permissões de arquivo se necessário
4. Configure SSL/HTTPS para produção

### Observação sobre Vercel
Este é um projeto PHP que requer um servidor com suporte a PHP e MySQL. O Vercel é otimizado para aplicações Node.js/frontend. Para deploy em Vercel, seria necessário:
- Converter para Node.js/Express
- Usar um banco de dados compatível (PostgreSQL, MongoDB)
- Reescrever a lógica em JavaScript

## 🐛 Troubleshooting

### Erro de conexão com banco
- Verifique as credenciais em `config/database.php`
- Certifique-se que o MySQL está rodando
- Verifique se o banco `construcao` foi criado

### Erro 500
- Verifique os logs do Apache/PHP
- Certifique-se que a extensão MySQLi está habilitada
- Verifique permissões de arquivo

### API não funciona
- Verifique se mod_rewrite está habilitado
- Certifique-se que o arquivo `.htaccess` está presente
- Verifique headers CORS no navegador

## 📝 Licença

Este projeto é open source e está disponível sob a licença MIT.
