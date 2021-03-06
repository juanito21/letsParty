<?php

require_once 'Handler.php';

/**
 * Handle pictures with the database
 *
 * @author Jean
 */
class PictureHandler extends Handler {
    
    var $uh;
    
    /**
     * The constructor
     * @param PDO $myConn
     */
    function __construct($myConn, $uh) {
        parent::__construct($myConn);
        $this->uh = $uh;
    }
    
    /**
     * Insert a picture in the database
     * @param int $id
     * @param string $extension
     * @return string|false
     */
    function insertPicture($idUser, $extension) {
        $this->conn->beginTransaction();
        $tmp = "tmp";
      
        $sql = "INSERT INTO " . T_PICTURES . " (".P_NAME.", ".P_USER.") VALUES (?,?)";
        $params = array($tmp, $idUser);
        if(!parent::preparedUpdate($sql, $params)) return false;
        $idPicture = $this->conn->lastInsertId();
        $sql = "UPDATE " . T_PICTURES . " SET " . P_NAME . " = ? WHERE " . P_ID . " = ?";
        $name = $idUser . "_" . $idPicture . "." . $extension;
        $params = array($name, $idPicture);
        if(!parent::preparedUpdate($sql, $params)) return false;
        $sql = "SELECT " . P_ID . ", " . P_NAME . " FROM " . T_PICTURES . " WHERE " . P_ID . " = ?";
        $params = array($idPicture);
        if(!$res = parent::preparedQueryFirst($sql, $params, PDO::FETCH_ASSOC)) return false;
        
        $this->conn->commit();
        return $res;
    }
    
    /**
     * Get the number of user's picture
     * @param int $id
     * @return int
     */
    function getNumberOfPictureByUser($id) {
        $sql = "SELECT COUNT(*) FROM " . T_PICTURES . " WHERE " . P_USER . " = ?";
        $params = array($id);
        return parent::preparedQueryCount($sql, $params);
    }
    
    /**
     * Delete a picture in the database
     * @param string $name
     * @return boolean
     */
    function deletePicture($name) {
       $sql = "DELETE FROM " . T_PICTURES . " WHERE " . P_NAME . " = ?";
       $params = array($name);
       return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Get all user pictures
     * @param int $id
     * @return array or false
     */
    function getPictures($id) {
        $sql = "SELECT * FROM " . T_PICTURES . " WHERE " . P_USER . " = ?";
        $params = array($id);
        return parent::preparedQueryAll($sql, $params, PDO::FETCH_ASSOC);
    }

}
