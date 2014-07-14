<?php

require_once 'Handler.php';

/**
 * Handle the contacts between users with the database
 *
 * @author Jean
 */
class ContactHandler extends Handler {
    
    var $uh;
    
    public function __construct($myConn, $uh) {
        parent::__construct($myConn);
        $this->uh = $uh;
    }
    
    public function addContact($sender, $receiver) {
        $sql = "INSERT INTO " . T_CONTACTS . " (".C_SENDER.",".C_RECEIVER.") VALUES (?,?)";
        $params = array($sender, $receiver);
        return parent::preparedUpdate($sql, $params);
    }
    
    public function deleteContact($sender, $receiver) {
        $this->conn->beginTransaction();
        $sql = "DELETE FROM " . T_CONTACTS . " WHERE (".C_SENDER.",".C_RECEIVER.") IN "
                . "((?,?),(?,?))";
        $params = array($sender, $receiver, $receiver, $sender);
        if(!parent::preparedUpdate($sql, $params)) return false;
        $sql = "INSERT INTO " . T_WHODELWHO . " (".W_SENDER.",".W_RECEIVER.") VALUES (?,?)";
        $params = array($sender, $receiver);
        if(!parent::preparedUpdate($sql, $params)) return false;
        $this->conn->commit();
        return true;
    }
    
    public function isContactExist($sender, $receiver) {
        $sql = "SELECT COUNT(*) FROM " . T_CONTACTS . " WHERE (".C_SENDER.",".C_RECEIVER.") IN "
                . "((?,?),(?,?))";
        $params = array($sender, $receiver, $receiver, $sender);
        return (parent::preparedQueryCount($sql, $params)>0);
    }
    
}
