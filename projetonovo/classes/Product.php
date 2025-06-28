<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Product Management Class
 * Handles all product-related database operations
 */
class Product {
    private $db;
    
    public function __construct() {
        $this->db = getDbConnection();
    }
    
    /**
     * Get all products with supplier information
     */
    public function getAllProducts() {
        try {
            $query = "
                SELECT 
                    p.id_produto,
                    p.nome,
                    p.categoria,
                    p.estoque,
                    p.preco,
                    p.id_fornecedor,
                    f.nome as fornecedor_nome
                FROM produtos p
                LEFT JOIN fornecedores f ON p.id_fornecedor = f.id_fornecedor
                ORDER BY p.nome ASC
            ";
            
            $result = $this->db->query($query);
            
            if (!$result) {
                throw new Exception("Erro na consulta: " . $this->db->error);
            }
            
            return $result->fetch_all(MYSQLI_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error in getAllProducts: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get product by ID
     */
    public function getProductById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    f.nome as fornecedor_nome
                FROM produtos p
                LEFT JOIN fornecedores f ON p.id_fornecedor = f.id_fornecedor
                WHERE p.id_produto = ?
            ");
            
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_assoc();
            
        } catch (Exception $e) {
            error_log("Error in getProductById: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Check if product has sufficient stock
     */
    public function checkStock($productId, $quantity) {
        try {
            $stmt = $this->db->prepare("SELECT estoque FROM produtos WHERE id_produto = ?");
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            
            if (!$product) {
                return ['success' => false, 'message' => 'Produto não encontrado'];
            }
            
            if ($product['estoque'] < $quantity) {
                return [
                    'success' => false, 
                    'message' => 'Estoque insuficiente',
                    'available' => $product['estoque']
                ];
            }
            
            return ['success' => true, 'available' => $product['estoque']];
            
        } catch (Exception $e) {
            error_log("Error in checkStock: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno'];
        }
    }
}
?>
