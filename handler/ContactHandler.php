<?php

require_once 'Handler.php';

/**
 * Handle the contacts between users with the database
 *
 * @author Jean
 */
class ContactHandler extends Handler {
    
    var $uh;
    
    /**
     * The constructor
     * @param PDO $myConn
     * @param UserHandler $uh
     */
    public function __construct($myConn, $uh) {
        parent::__construct($myConn);
        $this->uh = $uh;
    }
    
    /**
     * Add a contact (a couple of users) in the database
     * @param int $sender
     * @param int $receiver
     * @return boolean
     */
    public function addContact($sender, $receiver) {
        $sql = "INSERT INTO " . T_CONTACTS . " (".C_SENDER.",".C_RECEIVER.") VALUES (?,?)";
        $params = array($sender, $receiver);
        return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Delete a contact
     * @param int $sender
     * @param int $receiver
     * @return boolean
     */
    public function deleteContact($sender, $receiver) {
        $sql = "DELETE FROM " . T_CONTACTS . " WHERE (".C_SENDER.",".C_RECEIVER.") IN "
                . "((?,?),(?,?))";
        $params = array($sender, $receiver, $receiver, $sender);
        return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Add to the black list a couple of users
     * @param int $sender
     * @param int $receiver
     * @return boolean
     */
    public function addBlackList($sender, $receiver) {
        $sql = "INSERT INTO " . T_BLACKLIST . " (".B_SENDER.",".B_RECEIVER.") VALUES (?,?)";
        $params = array($sender, $receiver);
        return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Check if a user is black listed from an other one
     * @param int $sender
     * @param int $receiver
     * @return boolean
     */
    public function isBlackListed($sender, $receiver) {
        $sql = "SELECT COUNT(*) FROM " . T_BLACKLIST . " WHERE " . B_SENDER . " = ? AND " . B_RECEIVER . " = ?";
        $params = array($sender, $receiver);
        return (parent::preparedQueryCount($sql, $params)>0);
    }
    
    /**
     * Check if a contact exists
     * @param int $sender
     * @param int $receiver
     * @return boolean
     */
    public function isContactExist($sender, $receiver) {
        $sql = "SELECT COUNT(*) FROM " . T_CONTACTS . " WHERE (".C_SENDER.",".C_RECEIVER.") IN "
                . "((?,?),(?,?))";
        $params = array($sender, $receiver, $receiver, $sender);
        return (parent::preparedQueryCount($sql, $params)>0);
    }
    
    /**
     * Get contacts
     * @param int $id
     * @return array or false
     */
    public function getContacts($id) {
        $sql = "SELECT * FROM " . T_CONTACTS . " WHERE " . C_SENDER . " = ? OR " . C_RECEIVER . " = ?";
        $params = array($id, $id);
        return parent::preparedQueryAll($sql, $params, PDO::FETCH_ASSOC);
    }
    
}
