<?php

/**
 * Handle connection to the database
 *
 * @author Jean
 */
class DBConnect {
    
    var $conn;
    
    /**
     * The constructor
     */
    function __construct() {
	
    }
  
    /**
     * The destructor
     */
    function __destruct() {
        $this->close();
    }
  
    /**
     * Create a connection to the database
     */
    public function connect() {
        try {
                $this->conn = new PDO(DB_SYS . ":host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $this->conn->exec("set names utf8");
        } catch (PDOException $e) {
                print "Error ! : " . $e->getMessage() . "<br/>";
                die();
        }
    }
    
    /**
     * Get the connection object
     * @return PDO object
     */
    public function getCon() {
        return $this->conn;
    }
  
    /*
     * Close the database connection
     */
    public function close() {
        $this->conn = null;
    }
  
} 

?>