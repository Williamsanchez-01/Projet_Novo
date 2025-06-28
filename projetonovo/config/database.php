<?php


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
    
    
     
     
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    
    private function connect() {
        try {
            
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            
            $this->connection = new mysqli(
                self::DB_HOST,
                self::DB_USER,
                self::DB_PASS,
                self::DB_NAME
            );
            
            
            $this->connection->set_charset('utf8mb4');
            
        } catch (mysqli_sql_exception $e) {
            $this->handleConnectionError($e);
        }
    }
    
    
    private function handleConnectionError($exception) {
        error_log("Database connection error: " . $exception->getMessage());
        
        
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
    
    
    public function getConnection() {
        return $this->connection;
    }
    
    
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    
    private function __clone() {}
    
    
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}


function getDbConnection() {
    return DatabaseConfig::getInstance()->getConnection();
}
?>
