<?php

require_once 'Handler.php';

/**
 * Handle user messages in the database
 *
 * @author Jean
 */
class MessageHandler extends Handler {
    
    /**
     * The constructor
     * @param PDO $conn
     */
    public function __construct($conn) {
        parent::__construct($conn);
    }
    
    /**
     * Add a message in the database
     * @param int $sender
     * @param int $receiver
     * @param int $content
     * @return boolean
     */
    public function addMessage($sender, $receiver, $content) {
        $sql = "INSERT INTO " . T_MESSAGES . " (".M_CONTENT.", ".M_SENTAT.", ".M_SENDER.", ".M_RECEIVER.") "
                . "VALUES (?,now(),?,?)";
        $params = array($content, $sender, $receiver);
        return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Set the view time messages at now()
     * @param int $sender
     * @param int $receiver
     * @return boolean
     */
    public function setViewAt($sender, $receiver) {
        $sql = "UPDATE " . T_MESSAGES . " SET " . M_VIEWAT . " = now() WHERE (".M_RECEIVER.",".M_SENDER.") IN "
                . "((?,?),(?,?)) AND ".M_VIEWAT." IS NULL";
        $params = array($sender, $receiver, $receiver, $sender);
        return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Get the messages
     * @param int $sender
     * @param int $receiver
     * @return boolean
     */
    public function getMessages($sender, $receiver) {
        $sql = "SELECT * FROM " . T_MESSAGES . " WHERE (".M_SENDER.",".M_RECEIVER.") IN ((?,?),(?,?)) ORDER BY " . M_ID . " DESC";
        $params = array($sender, $receiver, $receiver, $sender);
        return parent::preparedQueryAll($sql, $params, PDO::FETCH_ASSOC);
    }
    
}
