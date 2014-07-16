<?php

require_once 'Handler.php';

/**
 * Handle the invitations between users with the database
 *
 * @author Jean
 */
class InvitationHandler extends Handler {
    
    var $uh;
    var $ch;
    
    /**
     * The constructor
     * @param PDO $myConn
     * @param UserHandler $uh
     * @param ContactHandler $ch
     */
    public function __construct($myConn, $uh, $ch) {
        parent::__construct($myConn);
        $this->uh = $uh;
        $this->ch = $ch;
    }
    
    /**
     * Add a pending invitation in the database
     * @param int $senderId
     * @param int $receiverId
     * @param boolean $exist : if true then UPDATE
     * @return boolean
     */
    public function addInvitation($senderId, $receiverId, $exist) {
        if(!$exist)
            $sql = "INSERT INTO " . T_INVITATIONS . " (".I_STATUS.",".I_SENDER.",".I_RECEIVER.", ".I_UPDATEAT.") "
                . "VALUES (?,?,?,now())";
        else
            $sql = "UPDATE " . T_INVITATIONS . " SET " . I_STATUS . " = ? WHERE "
                . I_SENDER . " = ? AND " . I_RECEIVER . " = ?";
        $status = INVITATION_PENDING;
        $params = array($status, $senderId, $receiverId);
        return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Accept an invitation, so delete the invitation in the database and create contact
     * @param int $senderId
     * @param int $receiverId
     * @return boolean
     */
    public function acceptInvitation($senderId, $receiverId) {
        $this->conn->beginTransaction();
        $sql = "DELETE FROM " . T_INVITATIONS . " WHERE "
                . I_SENDER . " = ? AND " . I_RECEIVER . " = ?";
        $params = array($senderId, $receiverId);
        if(!parent::preparedUpdate($sql, $params)) return false;
        if(!$this->ch->addContact($senderId, $receiverId)) return false;
        
        $this->conn->commit();
        return true;
    }
    
    /**
     * Reject an invitation, so update the status
     * @param int $senderId
     * @param int $receiverId
     * @return boolean
     */
    public function rejectInvitation($senderId, $receiverId) {
        $sql = "UPDATE " . T_INVITATIONS . " SET " . I_STATUS . " = ?, " . I_UPDATEAT . " = now() WHERE "
                . I_SENDER . " = ? AND " . I_RECEIVER . " = ?";
        $status = INVITATION_REJECTED;
        $params = array($status, $senderId, $receiverId);
        return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Delete an invitation in the database
     * @param int $senderId
     * @param int $receiverId
     * @return boolean
     */
    public function deleteInvitation($senderId, $receiverId) {
        $sql = "DELETE FROM " . T_INVITATIONS . " WHERE " . I_SENDER . " = ? AND " . I_RECEIVER . " = ?";
        $params = array($senderId, $receiverId);
        return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Check if the invitation is pending
     * @param int $senderId
     * @param int $receiverId
     * @return boolean
     */
    public function isPendingInvitationExist($senderId, $receiverId) {
        $sql = "SELECT COUNT(*) FROM " . T_INVITATIONS . ""
                . " WHERE " . I_SENDER . " = ? AND " . I_RECEIVER . " = ? AND " . I_STATUS . " = ?";
        $status = INVITATION_PENDING;
        $params = array($senderId, $receiverId, $status);
        return (parent::preparedQueryCount($sql, $params)>0);
    }
    
    /**
     * Check if an invitation is rejected
     * @param int $senderId
     * @param int $receiverId
     * @return boolean
     */
    public function isRejectedInvitationExist($senderId, $receiverId) {
        $sql = "SELECT " . I_UPDATEAT . " FROM " . T_INVITATIONS . ""
                . " WHERE " . I_SENDER . " = ? AND " . I_RECEIVER . " = ? AND " . I_STATUS . " = ?";
        $status = INVITATION_REJECTED;
        $params = array($senderId, $receiverId, $status);
        $res = parent::preparedQueryFirst($sql, $params, PDO::FETCH_ASSOC);
        if(count($res)>0) {
            return $res[I_UPDATEAT];
        } else return false;
    }
    
    /**
     * Get the invitations received by an user
     * @param int $receiver
     * @return array
     */
    public function getInvitationsReceived($receiver) {
        $sql = "SELECT * FROM " . T_INVITATIONS . " WHERE " . I_RECEIVER . " = ? AND " . I_STATUS . " = ?";
        $status = INVITATION_PENDING;
        $params = array($receiver, $status);
        return parent::preparedQueryAll($sql, $params, PDO::FETCH_ASSOC);
    }
}