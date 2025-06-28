<?php
/**
 * API Endpoint for Stock Exit
 * Handles POST requests to add stock exits
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../classes/StockMovement.php';

try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }
    
    // Get JSON input or form data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Extract data
    $productId = $input['id_produto'] ?? null;
    $quantity = $input['quantidade'] ?? null;
    $observations = $input['observacoes'] ?? '';
    
    // Create stock movement instance
    $stockMovement = new StockMovement();
    
    // Add stock exit
    $result = $stockMovement->addStockExit($productId, $quantity, $observations);
    
    // Set appropriate HTTP status code
    http_response_code($result['success'] ? 200 : 400);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("Error in stock_exit.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>
