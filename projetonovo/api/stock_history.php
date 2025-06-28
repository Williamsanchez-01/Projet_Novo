<?php


header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../classes/StockMovement.php';

try {
    
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método não permitido');
    }
    
    $stockMovement = new StockMovement();
    
    
    $productId = $_GET['product_id'] ?? null;
    $limit = $_GET['limit'] ?? 50;
    
    
    $limit = min(max((int)$limit, 1), 200); 
    
    $history = $stockMovement->getMovementHistory($productId, $limit);
    
    echo json_encode([
        'success' => true,
        'data' => $history,
        'count' => count($history)
    ]);
    
} catch (Exception $e) {
    error_log("Error in stock_history.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>
