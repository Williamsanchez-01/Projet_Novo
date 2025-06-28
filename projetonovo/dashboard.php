<?php
require_once 'includes/auth.php';
require_once 'classes/Product.php';
require_once 'classes/Supplier.php';

requireLogin();

$productManager = new Product();
$supplierManager = new Supplier();

$products = $productManager->getAll();
$suppliers = $supplierManager->getAll();

$message = '';
$activeTab = $_GET['tab'] ?? 'products';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_product':
                $result = $productManager->create(
                    $_POST['name'], $_POST['category'], $_POST['price'],
                    $_POST['supplier_id'], $_POST['stock_quantity'], $_POST['description']
                );
                $message = $result['message'];
                break;
                
            case 'update_product':
                $result = $productManager->update(
                    $_POST['id'], $_POST['name'], $_POST['category'], $_POST['price'],
                    $_POST['supplier_id'], $_POST['stock_quantity'], $_POST['description']
                );
                $message = $result['message'];
                break;
                
            case 'delete_product':
                $result = $productManager->delete($_POST['id']);
                $message = $result['message'];
                break;
                
            case 'create_supplier':
                $result = $supplierManager->create(
                    $_POST['name'], $_POST['contact_person'], $_POST['phone'],
                    $_POST['email'], $_POST['address']
                );
                $message = $result['message'];
                break;
                
            case 'update_supplier':
                $result = $supplierManager->update(
                    $_POST['id'], $_POST['name'], $_POST['contact_person'],
                    $_POST['phone'], $_POST['email'], $_POST['address']
                );
                $message = $result['message'];
                break;
                
            case 'delete_supplier':
                $result = $supplierManager->delete($_POST['id']);
                $message = $result['message'];
                break;
                
            case 'adjust_stock':
                $adjustment = $_POST['adjustment_type'] === 'increase' ? $_POST['quantity'] : -$_POST['quantity'];
                $result = $productManager->adjustStock($_POST['product_id'], $adjustment);
                $message = $result['message'];
                break;
        }
        
        
        $products = $productManager->getAll();
        $suppliers = $supplierManager->getAll();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Construction Store Management - Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">Construction Store Management</div>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn btn-secondary btn-small">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, 'success') !== false ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="nav-tabs">
            <a href="?tab=products" class="nav-tab <?php echo $activeTab === 'products' ? 'active' : ''; ?>">Products</a>
            <a href="?tab=suppliers" class="nav-tab <?php echo $activeTab === 'suppliers' ? 'active' : ''; ?>">Suppliers</a>
            <a href="?tab=stock" class="nav-tab <?php echo $activeTab === 'stock' ? 'active' : ''; ?>">Stock Adjustment</a>
        </div>
        
        <?php if ($activeTab === 'products'): ?>
           
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Product Management</h2>
                    <button onclick="showProductForm()" class="btn btn-primary">Add Product</button>
                </div>
                
              
                <form id="productForm" method="POST" class="hidden mb-2">
                    <input type="hidden" name="action" id="productAction" value="create_product">
                    <input type="hidden" name="id" id="productId">
                    
                    <div class="form-group">
                        <label class="form-label">Product Name:</label>
                        <input type="text" name="name" id="productName" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Category:</label>
                        <input type="text" name="category" id="productCategory" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Price:</label>
                        <input type="number" step="0.01" name="price" id="productPrice" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Supplier:</label>
                        <select name="supplier_id" id="productSupplier" class="form-select">
                            <option value="">Select Supplier</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?php echo $supplier['id']; ?>"><?php echo htmlspecialchars($supplier['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Stock Quantity:</label>
                        <input type="number" name="stock_quantity" id="productStock" class="form-input" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description:</label>
                        <textarea name="description" id="productDescription" class="form-textarea"></textarea>
                    </div>
                    
                    <div class="flex gap-1">
                        <button type="submit" class="btn btn-success">Save Product</button>
                        <button type="button" onclick="hideProductForm()" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
                
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Supplier</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($product['supplier_name'] ?? 'N/A'); ?></td>
                                <td><?php echo $product['stock_quantity']; ?></td>
                                <td>
                                    <button onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)" class="btn btn-primary btn-small">Edit</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_product">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
        <?php elseif ($activeTab === 'suppliers'): ?>
           
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Supplier Management</h2>
                    <button onclick="showSupplierForm()" class="btn btn-primary">Add Supplier</button>
                </div>
                
            
                <form id="supplierForm" method="POST" class="hidden mb-2">
                    <input type="hidden" name="action" id="supplierAction" value="create_supplier">
                    <input type="hidden" name="id" id="supplierId">
                    
                    <div class="form-group">
                        <label class="form-label">Supplier Name:</label>
                        <input type="text" name="name" id="supplierName" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Contact Person:</label>
                        <input type="text" name="contact_person" id="supplierContact" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone:</label>
                        <input type="text" name="phone" id="supplierPhone" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email:</label>
                        <input type="email" name="email" id="supplierEmail" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Address:</label>
                        <textarea name="address" id="supplierAddress" class="form-textarea"></textarea>
                    </div>
                    
                    <div class="flex gap-1">
                        <button type="submit" class="btn btn-success">Save Supplier</button>
                        <button type="button" onclick="hideSupplierForm()" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
                
               
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($supplier['name']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['contact_person'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($supplier['phone'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($supplier['email'] ?? ''); ?></td>
                                <td>
                                    <button onclick="editSupplier(<?php echo htmlspecialchars(json_encode($supplier)); ?>)" class="btn btn-primary btn-small">Edit</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_supplier">
                                        <input type="hidden" name="id" value="<?php echo $supplier['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
        <?php elseif ($activeTab === 'stock'): ?>
           
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Stock Adjustment</h2>
                </div>
                
                <form method="POST" class="mb-2">
                    <input type="hidden" name="action" value="adjust_stock">
                    
                    <div class="form-group">
                        <label class="form-label">Select Product:</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">Choose a product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>">
                                    <?php echo htmlspecialchars($product['name']); ?> (Current: <?php echo $product['stock_quantity']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Adjustment Type:</label>
                        <select name="adjustment_type" class="form-select" required>
                            <option value="increase">Increase Stock</option>
                            <option value="decrease">Decrease Stock</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Quantity:</label>
                        <input type="number" name="quantity" class="form-input" min="1" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Adjust Stock</button>
                </form>
                
                
                <h3>Current Stock Levels</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td><?php echo $product['stock_quantity']; ?></td>
                                <td>
                                    <?php if ($product['stock_quantity'] <= 10): ?>
                                        <span style="color: #e74c3c; font-weight: bold;">Low Stock</span>
                                    <?php elseif ($product['stock_quantity'] <= 50): ?>
                                        <span style="color: #f39c12; font-weight: bold;">Medium Stock</span>
                                    <?php else: ?>
                                        <span style="color: #27ae60; font-weight: bold;">Good Stock</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
      
        function showProductForm() {
            document.getElementById('productForm').classList.remove('hidden');
            document.getElementById('productAction').value = 'create_product';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
        }
        
        function hideProductForm() {
            document.getElementById('productForm').classList.add('hidden');
        }
        
        function editProduct(product) {
            document.getElementById('productForm').classList.remove('hidden');
            document.getElementById('productAction').value = 'update_product';
            document.getElementById('productId').value = product.id;
            document.getElementById('productName').value = product.name;
            document.getElementById('productCategory').value = product.category;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productSupplier').value = product.supplier_id || '';
            document.getElementById('productStock').value = product.stock_quantity;
            document.getElementById('productDescription').value = product.description || '';
        }
        
      
        function showSupplierForm() {
            document.getElementById('supplierForm').classList.remove('hidden');
            document.getElementById('supplierAction').value = 'create_supplier';
            document.getElementById('supplierForm').reset();
            document.getElementById('supplierId').value = '';
        }
        
        function hideSupplierForm() {
            document.getElementById('supplierForm').classList.add('hidden');
        }
        
        function editSupplier(supplier) {
            document.getElementById('supplierForm').classList.remove('hidden');
            document.getElementById('supplierAction').value = 'update_supplier';
            document.getElementById('supplierId').value = supplier.id;
            document.getElementById('supplierName').value = supplier.name;
            document.getElementById('supplierContact').value = supplier.contact_person || '';
            document.getElementById('supplierPhone').value = supplier.phone || '';
            document.getElementById('supplierEmail').value = supplier.email || '';
            document.getElementById('supplierAddress').value = supplier.address || '';
        }
    </script>
</body>
</html>
