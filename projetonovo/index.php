<?php
/**
 * Main entry point for the Construction Inventory System
 * Provides a simple web interface for testing the API
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Estoque - Materiais de Construção</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            text-align: center;
            margin-bottom: 2rem;
            border-radius: 10px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: transform 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .loading {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sistema de Estoque</h1>
            <p>Gerenciamento de Materiais de Construção</p>
        </div>
        
        <div class="grid">
            <!-- Stock Entry Form -->
            <div class="card">
                <h2>Entrada de Estoque</h2>
                <form id="entryForm">
                    <div class="form-group">
                        <label for="entryProduct">Produto:</label>
                        <select id="entryProduct" name="id_produto" required>
                            <option value="">Carregando produtos...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="entryQuantity">Quantidade:</label>
                        <input type="number" id="entryQuantity" name="quantidade" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="entryObservations">Observações:</label>
                        <textarea id="entryObservations" name="observacoes" rows="3"></textarea>
                    </div>
                    <button type="submit">Registrar Entrada</button>
                </form>
                <div id="entryResult"></div>
            </div>
            
            <!-- Stock Exit Form -->
            <div class="card">
                <h2>Saída de Estoque</h2>
                <form id="exitForm">
                    <div class="form-group">
                        <label for="exitProduct">Produto:</label>
                        <select id="exitProduct" name="id_produto" required>
                            <option value="">Carregando produtos...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exitQuantity">Quantidade:</label>
                        <input type="number" id="exitQuantity" name="quantidade" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="exitObservations">Observações:</label>
                        <textarea id="exitObservations" name="observacoes" rows="3"></textarea>
                    </div>
                    <button type="submit">Registrar Saída</button>
                </form>
                <div id="exitResult"></div>
            </div>
        </div>
        
        <!-- Products Table -->
        <div class="card">
            <h2>Produtos em Estoque</h2>
            <div id="productsTable">
                <div class="loading">Carregando produtos...</div>
            </div>
        </div>
        
        <!-- Stock History -->
        <div class="card">
            <h2>Histórico de Movimentações</h2>
            <div id="historyTable">
                <div class="loading">Carregando histórico...</div>
            </div>
        </div>
    </div>
    
    <script>
        // API base URL
        const API_BASE = './api/';
        
        // Load products on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            loadProductsTable();
            loadStockHistory();
        });
        
        // Load products for select dropdowns
        async function loadProducts() {
            try {
                const response = await fetch(API_BASE + 'products.php');
                const data = await response.json();
                
                if (data.success) {
                    const entrySelect = document.getElementById('entryProduct');
                    const exitSelect = document.getElementById('exitProduct');
                    
                    // Clear loading options
                    entrySelect.innerHTML = '<option value="">Selecione um produto</option>';
                    exitSelect.innerHTML = '<option value="">Selecione um produto</option>';
                    
                    // Add products to selects
                    data.data.forEach(product => {
                        const option = `<option value="${product.id_produto}">${product.nome} (Estoque: ${product.estoque})</option>`;
                        entrySelect.innerHTML += option;
                        exitSelect.innerHTML += option;
                    });
                }
            } catch (error) {
                console.error('Error loading products:', error);
            }
        }
        
        // Load products table
        async function loadProductsTable() {
            try {
                const response = await fetch(API_BASE + 'products.php');
                const data = await response.json();
                
                if (data.success) {
                    let tableHTML = `
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Estoque</th>
                                    <th>Preço</th>
                                    <th>Fornecedor</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    data.data.forEach(product => {
                        tableHTML += `
                            <tr>
                                <td>${product.id_produto}</td>
                                <td>${product.nome}</td>
                                <td>${product.categoria}</td>
                                <td>${product.estoque}</td>
                                <td>R$ ${parseFloat(product.preco).toFixed(2)}</td>
                                <td>${product.fornecedor_nome || 'N/A'}</td>
                            </tr>
                        `;
                    });
                    
                    tableHTML += '</tbody></table>';
                    document.getElementById('productsTable').innerHTML = tableHTML;
                }
            } catch (error) {
                console.error('Error loading products table:', error);
                document.getElementById('productsTable').innerHTML = '<div class="error">Erro ao carregar produtos</div>';
            }
        }
        
        // Load stock history
        async function loadStockHistory() {
            try {
                const response = await fetch(API_BASE + 'stock_history.php?limit=20');
                const data = await response.json();
                
                if (data.success) {
                    let tableHTML = `
                        <table>
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Data</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    data.data.forEach(movement => {
                        const tipo = movement.tipo === 'entrada' ? 'Entrada' : 'Saída';
                        const date = new Date(movement.data_movimento).toLocaleString('pt-BR');
                        
                        tableHTML += `
                            <tr>
                                <td><span style="color: ${movement.tipo === 'entrada' ? 'green' : 'red'}">${tipo}</span></td>
                                <td>${movement.produto_nome}</td>
                                <td>${movement.quantidade}</td>
                                <td>${date}</td>
                                <td>${movement.observacoes || '-'}</td>
                            </tr>
                        `;
                    });
                    
                    tableHTML += '</tbody></table>';
                    document.getElementById('historyTable').innerHTML = tableHTML;
                }
            } catch (error) {
                console.error('Error loading stock history:', error);
                document.getElementById('historyTable').innerHTML = '<div class="error">Erro ao carregar histórico</div>';
            }
        }
        
        // Handle stock entry form
        document.getElementById('entryForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch(API_BASE + 'stock_entry.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                const resultDiv = document.getElementById('entryResult');
                resultDiv.innerHTML = `<div class="${result.success ? 'success' : 'error'}">${result.message}</div>`;
                
                if (result.success) {
                    this.reset();
                    loadProducts();
                    loadProductsTable();
                    loadStockHistory();
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('entryResult').innerHTML = '<div class="error">Erro ao processar solicitação</div>';
            }
        });
        
        // Handle stock exit form
        document.getElementById('exitForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch(API_BASE + 'stock_exit.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                const resultDiv = document.getElementById('exitResult');
                resultDiv.innerHTML = `<div class="${result.success ? 'success' : 'error'}">${result.message}</div>`;
                
                if (result.success) {
                    this.reset();
                    loadProducts();
                    loadProductsTable();
                    loadStockHistory();
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('exitResult').innerHTML = '<div class="error">Erro ao processar solicitação</div>';
            }
        });
    </script>
</body>
</html>
