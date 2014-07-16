<?php
/**
 * Handler class. Implements the basic request methods
 *
 * @author Jean
 */
abstract class Handler {
    
    var $conn;
    
    /**
     * The constructor
     * @param PDO $conn
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Make a prepare query and execute it
     * @param string $sql
     * @param array $params
     * @param int $mode
     * @return array else false if there is any error
     */
    public function preparedQueryFirst($sql, $params, $mode) {
        $res = false;
        try {
            $stmnt = $this->conn->prepare($sql);
            foreach($params as $key => &$param) {
                $stmnt->bindParam($key+1,$param);
            }
            $stmnt->execute();
            $res = $stmnt->fetch($mode);
            $stmnt->closeCursor();
        } catch (Exception $e) {
            echo 'Exception -> ';var_dump($e->getMessage());
        } return $res;
    }
    
    /**
     * Make a prepare query and execute it
     * @param string $sql
     * @param array $params
     * @param int $mode
     * @return array else false if there is any error
     */
    public function preparedQueryAll($sql, $params, $mode) {
        $res = false;
        try {
            $stmnt = $this->conn->prepare($sql);
            foreach($params as $key => &$param) {
                $stmnt->bindParam($key+1,$param);
            }
            $stmnt->execute();
            $res = $stmnt->fetchAll($mode);
            $stmnt->closeCursor();
        } catch (Exception $e) {
            echo 'Exception -> ';var_dump($e->getMessage());
        } return $res;
    }
    
    /**
     * Make a prepare count query and execute it
     * @param string $sql
     * @param array $params
     * @return int else false if there is any error
     */
    public function preparedQueryCount($sql, $params) {
        $res = false;
        try {
            $stmnt = $this->conn->prepare($sql);
            foreach($params as $key => &$param) {
                $stmnt->bindParam($key+1,$param);
            }
            $stmnt->execute();
            $res = $stmnt->fetchColumn();
            $stmnt->closeCursor();
        } catch (Exception $e) {
            echo 'Exception -> ';var_dump($e->getMessage());
        } return $res;
    }
    
    /**
     * Make a prepare update query (INSERT/UPDATE/DELETE) and execute it
     * @param string $sql
     * @param array $params
     * @return boolean
     */
    public function preparedUpdate($sql, $params) {
        $res = false;
        try {
            $stmnt = $this->conn->prepare($sql);
            foreach($params as $key => &$param) {
                $stmnt->bindParam($key+1,$param);
            }
            $res = $stmnt->execute();
            $stmnt->closeCursor();
        } catch (Exception $e) {
            echo 'Exception -> ';var_dump($e->getMessage());
        } return $res;
    }
    
}
