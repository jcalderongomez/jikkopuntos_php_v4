<?php
// Configuración de la base de datos PostgreSQL
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'jikkopuntos_v4');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres');

class Database {
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $connectionString = "host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS;
            $this->conn = pg_connect($connectionString);
            
            if (!$this->conn) {
                throw new Exception("Error de conexión a PostgreSQL");
            }
        } catch(Exception $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}
?>
