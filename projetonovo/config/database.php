<?php
/**
 * Database Configuration and Connection Class
 * Improved version with proper error handling and security
 */

class DatabaseConfig {
    private const DB_HOST = 'localhost';
    private const DB_NAME = 'construcao';
    private const DB_USER = 'root';
    private const DB_PASS = 'root';
    
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Singleton pattern to ensure single database connection
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Establish database connection with error handling
     */
    private function connect() {
        try {
            // Enable mysqli error reporting
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            
            $this->connection = new mysqli(
                self::DB_HOST,
                self::DB_USER,
                self::DB_PASS,
                self::DB_NAME
            );
            
            // Set charset to UTF-8
            $this->connection->set_charset('utf8mb4');
            
        } catch (mysqli_sql_exception $e) {
            $this->handleConnectionError($e);
        }
    }
    
    /**
     * Handle connection errors with proper logging
     */
    private function handleConnectionError($exception) {
        error_log("Database connection error: " . $exception->getMessage());
        
        // In production, don't expose database details
        if (getenv('APP_ENV') === 'production') {
            die(json_encode([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente mais tarde.'
            ]));
        } else {
            die(json_encode([
                'success' => false,
                'message' => 'Erro ao conectar ao banco de dados: ' . $exception->getMessage(),
                'debug' => [
                    'host' => self::DB_HOST,
                    'database' => self::DB_NAME,
                    'user' => self::DB_USER
                ]
            ]));
        }
    }
    
    /**
     * Get the database connection
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Close the database connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Helper function to get database connection
 */
function getDbConnection() {
    return DatabaseConfig::getInstance()->getConnection();
}
?>
