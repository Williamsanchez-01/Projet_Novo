<?php


header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../classes/Product.php';

try {
    
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método não permitido');
    }
    
    $productManager = new Product();
    
    
    $productId = $_GET['id'] ?? null;
    
    if ($productId) {
        $product = $productManager->getProductById($productId);
        if (!$product) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Produto não encontrado'
            ]);
            exit();
        }
        
        echo json_encode([
            'success' => true,
            'data' => $product
        ]);
    } else {
        $products = $productManager->getAllProducts();
        echo json_encode([
            'success' => true,
            'data' => $products,
            'count' => count($products)
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error in products.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>
