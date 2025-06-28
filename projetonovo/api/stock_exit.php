<?php


header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../classes/StockMovement.php';

try {
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }
    
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    
    $productId = $input['id_produto'] ?? null;
    $quantity = $input['quantidade'] ?? null;
    $observations = $input['observacoes'] ?? '';
    
    
    $stockMovement = new StockMovement();
    
    
    $result = $stockMovement->addStockExit($productId, $quantity, $observations);
    
    
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
