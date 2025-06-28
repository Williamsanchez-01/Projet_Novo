CREATE DATABASE IF NOT EXISTS construcao;
USE construcao;

-- Tabela de fornecedores
CREATE TABLE IF NOT EXISTS fornecedores (
    id_fornecedor INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    cnpj VARCHAR(14) UNIQUE NOT NULL,
    telefone VARCHAR(14),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de produtos
CREATE TABLE IF NOT EXISTS produtos (
    id_produto INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    categoria VARCHAR(50),
    estoque INT DEFAULT 0,
    preco DECIMAL(10,2),
    id_fornecedor INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_fornecedor) REFERENCES fornecedores(id_fornecedor) ON DELETE SET NULL
);

-- Tabela de entradas de estoque
CREATE TABLE IF NOT EXISTS entradas_estoque (
    id_entrada INT PRIMARY KEY AUTO_INCREMENT,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    data_entrada DATETIME DEFAULT CURRENT_TIMESTAMP,
    observacoes TEXT,
    FOREIGN KEY (id_produto) REFERENCES produtos(id_produto) ON DELETE CASCADE
);

-- Tabela de saídas de estoque
CREATE TABLE IF NOT EXISTS saidas_estoque (
    id_saida INT PRIMARY KEY AUTO_INCREMENT,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    data_saida DATETIME DEFAULT CURRENT_TIMESTAMP,
    observacoes TEXT,
    FOREIGN KEY (id_produto) REFERENCES produtos(id_produto) ON DELETE CASCADE
);

-- Inserir fornecedores
INSERT INTO fornecedores (nome, cnpj, telefone, email) VALUES
('Construfácil', '12345678000101', '(13) 3234-1001', 'contato@construfacil.com.br'),
('Areial do Zé', '12345678000202', '(13) 3222-2020', 'vendas@areialdoze.com.br'),
('Pedreira Boa Pedra', '12345678000303', '(13) 3245-3030', 'pedidos@boapedra.com'),
('Tijolaria São Jorge', '12345678000404', '(13) 3211-4040', 'comercial@tijolosjorge.com.br'),
('Blocos & Cia', '12345678000505', '(13) 3255-5050', 'atendimento@blocoscia.com.br'),
('Químicos Vale Verde', '12345678000606', '(13) 3266-6060', 'quimicos@valeverde.com'),
('Telhas Brasil', '12345678000707', '(13) 3277-7070', 'sac@telhasbrasil.com.br'),
('Metalúrgica União', '12345678000808', '(13) 3288-8080', 'contato@metalunic.com'),
('HidroCenter', '12345678000909', '(13) 3299-9090', 'suporte@hidrocenter.com'),
('EletroMais', '12345678001010', '(13) 3200-1010', 'info@eletromais.com.br');

-- Inserir produtos (corrigindo os nomes das colunas)
INSERT INTO produtos (nome, categoria, estoque, preco, id_fornecedor) VALUES
('Cimento Votoran', 'Cimento', 100, 32.50, 1),
('Areia Média', 'Areia', 200, 18.00, 2),
('Brita 1', 'Brita', 150, 22.50, 3),
('Tijolo Baiano', 'Tijolos', 500, 0.80, 4),
('Bloco de Concreto', 'Blocos', 300, 2.50, 5),
('Cal Hidratada', 'Cal', 120, 10.00, 6),
('Telha Cerâmica', 'Telhas', 250, 3.20, 7),
('Viga de Aço', 'Aço', 80, 45.00, 8),
('Tubo PVC 100mm', 'Hidráulico', 180, 27.00, 9),
('Fio Elétrico 2,5mm', 'Elétrico', 220, 1.90, 10);
