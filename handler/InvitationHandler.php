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
    
    public function __construct($myConn, $uh, $ch) {
        parent::__construct($myConn);
        $this->uh = $uh;
        $this->ch = $ch;
    }
    
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
    
    public function rejectInvitation($senderId, $receiverId) {
        $sql = "UPDATE " . T_INVITATIONS . " SET " . I_STATUS . " = ?, " . I_UPDATEAT . " = now() WHERE "
                . I_SENDER . " = ? AND " . I_RECEIVER . " = ?";
        $status = INVITATION_REJECTED;
        $params = array($status, $senderId, $receiverId);
        return parent::preparedUpdate($sql, $params);
    }
    
    public function deleteInvitation($senderId, $receiverId) {
        $sql = "DELETE FROM " . T_INVITATIONS . " WHERE " . I_SENDER . " = ? AND " . I_RECEIVER . " = ?";
        $params = array($senderId, $receiverId);
        return parent::preparedUpdate($sql, $params);
    }
    
    public function isPendingInvitationExist($senderId, $receiverId) {
        $sql = "SELECT COUNT(*) FROM " . T_INVITATIONS . ""
                . " WHERE " . I_SENDER . " = ? AND " . I_RECEIVER . " = ? AND " . I_STATUS . " = ?";
        $status = INVITATION_PENDING;
        $params = array($senderId, $receiverId, $status);
        return (parent::preparedQueryCount($sql, $params)>0);
    }
    
    public function isRejectedInvitationExist($senderId, $receiverId) {
        $sql = "SELECT " . I_UPDATEAT . " FROM " . T_INVITATIONS . ""
                . " WHERE " . I_SENDER . " = ? AND " . I_RECEIVER . " = ? AND " . I_STATUS . " = ?";
        $status = INVITATION_REJECTED;
        $params = array($senderId, $receiverId, $status);
        $res = parent::preparedQuery($sql, $params, PDO::FETCH_ASSOC);
        if(count($res)>0) {
            return $res[I_UPDATEAT];
        } else return false;
    }
    
    public function getInvitationsReceived($receiver) {
        $sql = "SELECT * FROM " . T_INVITATIONS . " WHERE " . I_RECEIVER . " = ? AND " . I_STATUS . " = ?";
        $status = INVITATION_PENDING;
        $params = array($receiver, $status);
        return parent::preparedQuery($sql, $params, PDO::FETCH_ASSOC);
    }
    
    
}