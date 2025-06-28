<?php
require_once 'config/database.php';

class Supplier {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function getAll() {
        $query = "SELECT * FROM suppliers ORDER BY name";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM suppliers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function create($name, $contact_person, $phone, $email, $address) {
        if (empty($name)) {
            return ['success' => false, 'message' => 'Supplier name is required'];
        }
        
        $stmt = $this->db->prepare("INSERT INTO suppliers (name, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $contact_person, $phone, $email, $address);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Supplier created successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to create supplier'];
        }
    }
    
    public function update($id, $name, $contact_person, $phone, $email, $address) {
        if (empty($name)) {
            return ['success' => false, 'message' => 'Supplier name is required'];
        }
        
        $stmt = $this->db->prepare("UPDATE suppliers SET name = ?, contact_person = ?, phone = ?, email = ?, address = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $contact_person, $phone, $email, $address, $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Supplier updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update supplier'];
        }
    }
    
    public function delete($id) {
        
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM products WHERE supplier_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            return ['success' => false, 'message' => 'Cannot delete supplier with existing products'];
        }
        
        $stmt = $this->db->prepare("DELETE FROM suppliers WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Supplier deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete supplier'];
        }
    }
}
?>
