<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Product.php';


class StockMovement {
    private $db;
    private $productManager;
    
    public function __construct() {
        $this->db = getDbConnection();
        $this->productManager = new Product();
    }
    
    
    public function addStockEntry($productId, $quantity, $observations = '') {
        
        $validation = $this->validateInput($productId, $quantity);
        if (!$validation['success']) {
            return $validation;
        }
        
        
        $this->db->begin_transaction();
        
        try {
            
            $stmt = $this->db->prepare("
                INSERT INTO entradas_estoque (id_produto, quantidade, observacoes) 
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iis", $productId, $quantity, $observations);
            
            if (!$stmt->execute()) {
                throw new Exception("Erro ao inserir entrada no estoque");
            }
            
            
            $updateStmt = $this->db->prepare("
                UPDATE produtos 
                SET estoque = estoque + ? 
                WHERE id_produto = ?
            ");
            $updateStmt->bind_param("ii", $quantity, $productId);
            
            if (!$updateStmt->execute()) {
                throw new Exception("Erro ao atualizar estoque do produto");
            }
            
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Entrada registrada e estoque atualizado com sucesso!',
                'entry_id' => $this->db->insert_id
            ];
            
        } catch (Exception $e) {
            
            $this->db->rollback();
            error_log("Error in addStockEntry: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    
    public function addStockExit($productId, $quantity, $observations = '') {
        
        $validation = $this->validateInput($productId, $quantity);
        if (!$validation['success']) {
            return $validation;
        }
        
        
        $stockCheck = $this->productManager->checkStock($productId, $quantity);
        if (!$stockCheck['success']) {
            return $stockCheck;
        }
        
        
        $this->db->begin_transaction();
        
        try {
            
            $stmt = $this->db->prepare("
                INSERT INTO saidas_estoque (id_produto, quantidade, observacoes) 
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iis", $productId, $quantity, $observations);
            
            if (!$stmt->execute()) {
                throw new Exception("Erro ao registrar saída");
            }
            
            
            $updateStmt = $this->db->prepare("
                UPDATE produtos 
                SET estoque = estoque - ? 
                WHERE id_produto = ?
            ");
            $updateStmt->bind_param("ii", $quantity, $productId);
            
            if (!$updateStmt->execute()) {
                throw new Exception("Erro ao atualizar estoque");
            }
            
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Saída registrada e estoque atualizado com sucesso!',
                'exit_id' => $this->db->insert_id
            ];
            
        } catch (Exception $e) {
            
            $this->db->rollback();
            error_log("Error in addStockExit: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    
    public function getMovementHistory($productId = null, $limit = 50) {
        try {
            $whereClause = $productId ? "WHERE e.id_produto = ?" : "";
            
            $query = "
                (SELECT 
                    'entrada' as tipo,
                    e.id_entrada as id,
                    e.id_produto,
                    p.nome as produto_nome,
                    e.quantidade,
                    e.data_entrada as data_movimento,
                    e.observacoes
                FROM entradas_estoque e
                JOIN produtos p ON e.id_produto = p.id_produto
                $whereClause)
                
                UNION ALL
                
                (SELECT 
                    'saida' as tipo,
                    s.id_saida as id,
                    s.id_produto,
                    p.nome as produto_nome,
                    s.quantidade,
                    s.data_saida as data_movimento,
                    s.observacoes
                FROM saidas_estoque s
                JOIN produtos p ON s.id_produto = p.id_produto
                $whereClause)
                
                ORDER BY data_movimento DESC
                LIMIT ?
            ";
            
            $stmt = $this->db->prepare($query);
            
            if ($productId) {
                $stmt->bind_param("iii", $productId, $productId, $limit);
            } else {
                $stmt->bind_param("i", $limit);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error in getMovementHistory: " . $e->getMessage());
            return [];
        }
    }
    
    
    private function validateInput($productId, $quantity) {
        if (empty($productId) || !is_numeric($productId)) {
            return ['success' => false, 'message' => 'ID do produto é obrigatório e deve ser numérico'];
        }
        
        if (empty($quantity) || !is_numeric($quantity) || $quantity <= 0) {
            return ['success' => false, 'message' => 'Quantidade deve ser um número positivo'];
        }
        
        
        $product = $this->productManager->getProductById($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Produto não encontrado'];
        }
        
        return ['success' => true];
    }
}
?>
