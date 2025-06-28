<?php
// XAMPP Setup Checker for Construction Store Management System

echo "<h1>XAMPP Setup Checker</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { background: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 4px solid #007cba; }
</style>";

// Check if we're running on localhost
echo "<h2>Environment Check</h2>";
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    echo "<span class='success'>✓ Running on localhost</span><br>";
} else {
    echo "<span class='warning'>⚠ Not running on localhost - make sure XAMPP is configured properly</span><br>";
}

// Check if mysqli extension is loaded
if (extension_loaded('mysqli')) {
    echo "<span class='success'>✓ MySQLi extension is loaded</span><br>";
} else {
    echo "<span class='error'>✗ MySQLi extension is not loaded</span><br>";
}

// Test MySQL connection
echo "<h2>MySQL Connection Test</h2>";
try {
    $connection = new mysqli('localhost', 'root', '');
    if ($connection->connect_error) {
        echo "<span class='error'>✗ MySQL connection failed: " . $connection->connect_error . "</span><br>";
        
        echo "<div class='info'>";
        echo "<strong>Troubleshooting Steps:</strong><br>";
        echo "1. Make sure XAMPP Control Panel shows MySQL as 'Running'<br>";
        echo "2. If MySQL won't start, check if port 3306 is being used by another service<br>";
        echo "3. Try restarting XAMPP as Administrator<br>";
        echo "4. Check XAMPP error logs in xampp/mysql/data/mysql_error.log<br>";
        echo "</div>";
    } else {
        echo "<span class='success'>✓ MySQL connection successful</span><br>";
        
        // Test database creation
        if ($connection->query("CREATE DATABASE IF NOT EXISTS construction_store")) {
            echo "<span class='success'>✓ Database creation/access successful</span><br>";
            
            // Select the database
            $connection->select_db('construction_store');
            
            // Test table creation
            $testTable = "CREATE TABLE IF NOT EXISTS test_table (id INT AUTO_INCREMENT PRIMARY KEY, test_field VARCHAR(50))";
            if ($connection->query($testTable)) {
                echo "<span class='success'>✓ Table creation successful</span><br>";
                
                // Clean up test table
                $connection->query("DROP TABLE IF EXISTS test_table");
            } else {
                echo "<span class='error'>✗ Table creation failed: " . $connection->error . "</span><br>";
            }
        } else {
            echo "<span class='error'>✗ Database creation failed: " . $connection->error . "</span><br>";
        }
        
        $connection->close();
    }
} catch (Exception $e) {
    echo "<span class='error'>✗ MySQL connection error: " . $e->getMessage() . "</span><br>";
}

// Check file permissions
echo "<h2>File Permissions Check</h2>";
if (is_writable('.')) {
    echo "<span class='success'>✓ Directory is writable</span><br>";
} else {
    echo "<span class='warning'>⚠ Directory may not be writable - this could cause issues</span><br>";
}

// PHP version check
echo "<h2>PHP Information</h2>";
echo "PHP Version: " . phpversion() . "<br>";
if (version_compare(phpversion(), '7.4.0', '>=')) {
    echo "<span class='success'>✓ PHP version is compatible</span><br>";
} else {
    echo "<span class='warning'>⚠ PHP version might be too old</span><br>";
}

echo "<div class='info'>";
echo "<strong>Next Steps:</strong><br>";
echo "1. If all checks pass, go to <a href='login.php'>login.php</a> to start using the system<br>";
echo "2. If there are errors, fix them before proceeding<br>";
echo "3. The system will automatically create the database and tables on first run<br>";
echo "4. Default login will be created when you register your first user<br>";
echo "</div>";

echo "<h2>XAMPP Quick Start Guide</h2>";
echo "<div class='info'>";
echo "<strong>To set up XAMPP:</strong><br>";
echo "1. Start XAMPP Control Panel<br>";
echo "2. Click 'Start' for Apache and MySQL<br>";
echo "3. Both should show 'Running' status<br>";
echo "4. Place this project in xampp/htdocs/ folder<br>";
echo "5. Access via http://localhost/your-project-folder/<br>";
echo "<br><strong>If MySQL won't start:</strong><br>";
echo "• Check if Skype or other software is using port 3306<br>";
echo "• Run XAMPP as Administrator<br>";
echo "• Check Windows Services for conflicting MySQL services<br>";
echo "</div>";
?>
