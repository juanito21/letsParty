<?php
require_once '../handler/UserHandler.php';
require_once '../handler/ContactHandler.php';
require_once '../handler/InvitationHandler.php';
/**
 * Handle the invitation actions
 *
 * @author Jean
 */
class InvitationController {
    
    var $helper;
    var $ih;
    var $ch;
    var $uh;
    
    /**
     * The constructor
     * @param Helper $myHelper
     * @param PDO $myConn
     */
    public function __construct($myHelper, $myConn) {
        $this->helper = $myHelper;
        $this->uh = new UserHandler($myConn);
        $this->ch = new ContactHandler($myConn, $this->uh);
        $this->ih = new InvitationHandler($myConn, $this->uh, $this->ch);
        $this->sendInvitation();
        $this->rejectInvitation();
        $this->acceptInvitation();
        $this->cancelInvitation();
        $this->getInvitations();
    }
    
    /*
     * Send an invitation, matched by HTTP POST method
     */
    public function sendInvitation() {
        $app = \Slim\Slim::getInstance();
        $app->post('/sendInvitation', function() use ($app) {
            $fields = array('receiver');
            $this->helper->verifyRequiredParams($fields);
            $params = $this->helper->constructParamsArray($fields, 'post');
            global $userId;
            $exist = false;
            if($userId == $params['receiver']) {
                $this->helper->simpleRender (INVITATION_YOURSELF_ERROR, true, 200);
                return;
            } else if(!$this->uh->isUserExistById($params['receiver'])) {
                $this->helper->simpleRender(USER_NOT_EXISTS, true, 200);
                return;
            } else if(!$this->uh->isUserActive($params['receiver'])) {
                $this->helper->simpleRender(INVATION_USER_NOT_ACTIVE, true, 200);
                return;  
            } else if($this->ih->isPendingInvitationExist($userId, $params['receiver'])) {
                $this->helper->simpleRender(INVITATION_ALREADY_SENT, true, 200);
                return;  
            } else if($res = $this->ih->isRejectedInvitationExist($userId, $params['receiver'])) {
                if(getDiffHour($res)<RETRY_INVITATION) {
                    $this->helper->simpleRender(INVITATION_ALREADY_REJECTED, true, 200);
                    return;   
                } $exist = true;
            } else if($this->ch->isContactExist($userId, $params['receiver'])) {
                $this->helper->simpleRender(ALREADY_YOUR_CONTACT, true, 200);
                return;
            } else if($this->ch->isBlackListed($params['receiver'], $userId)) {
                $this->helper->simpleRender(BLACKLISTED, true, 200);
                return;
            } else if(!$this->ih->addInvitation($userId, $params['receiver'], $exist)) {
                $this->helper->simpleRender(SEND_INVITATION_ERROR, true, 200);
                return;
            } else $this->helper->simpleRender(SEND_INVITATION_SUCCESS, false, 201);
        });
    }
    
    /**
     * Reject an invitation, matched by HTTP PUT method
     */
    public function rejectInvitation() {
        $app = \Slim\Slim::getInstance();
        $app->put('/rejectInvitation/:sender', function($sender) use ($app) {
            global $userId;
            if($userId == $sender) {
               $this->helper->simpleRender (REJECT_YOURSELF_ERROR, true, 200);
                return; 
            } else if(!$this->uh->isUserExistsById($sender)) {
                $this->helper->simpleRender(USER_NOT_EXISTS, true, 200);
                return;
            } else if(!$this->ih->isPendingInvitationExist($sender, $userId)) {
                $this->helper->simpleRender(INVITATION_DOES_NOT_EXIST, true, 200);
                return;  
            } else if(!$this->ih->rejectInvitation($sender, $userId)) {
                $this->helper->simpleRender(REJECT_INVITATION_ERROR, true, 200);
                return;
            } else $this->helper->simpleRender(REJECT_INVITATION_SUCCESS, false, 200);
        });
    }
    
    /**
     * Accept an invitation, matched by HTTP POST method
     */
    public function acceptInvitation() {
        $app = \Slim\Slim::getInstance();
        $app->post('/acceptInvitation', function() use ($app) {
            $fields = array('sender');
            $this->helper->verifyRequiredParams($fields);
            $params = $this->helper->constructParamsArray($fields, 'post');
            global $userId;
            if($userId == $params['sender']) {
                $this->helper->simpleRender (ACCEPT_YOURSELF_ERROR, true, 200);
                return; 
            } else if(!$this->uh->isUserExistById($params['sender'])) {
                $this->helper->simpleRender(USER_NOT_EXISTS, true, 200);
                return;
            } else if(!$this->ih->isPendingInvitationExist($params['sender'], $userId)) {
                $this->helper->simpleRender(INVITATION_DOES_NOT_EXIST, true, 200);
                return;  
            } else if(!$this->ih->acceptInvitation($params['sender'], $userId)) {
                $this->helper->simpleRender(ACCEPT_INVITATION_ERROR, true, 200);
                return;
            } else $this->helper->simpleRender(ACCEPT_INVITATION_SUCCESS, false, 200);
        });
    }
    
    /**
     * Cancel an invitation, matched by HTTP DELETE method
     */
    public function cancelInvitation() {
        $app = \Slim\Slim::getInstance();
        $app->delete('/cancelInvitation/:receiver', function($receiver) use ($app) {
            global $userId;
            if($userId == $receiver) {
               $this->helper->simpleRender(CANCEL_YOURSELF_ERROR, true, 200);
                return; 
            } else if(!$this->uh->isUserExistById($receiver)) {
                $this->helper->simpleRender(USER_NOT_EXISTS, true, 200);
                return;
            } else if(!$this->ih->isPendingInvitationExist($userId, $receiver)) {
                $this->helper->simpleRender(INVITATION_DOES_NOT_EXIST, true, 200);
                return;  
            } else if(!$this->ih->deleteInvitation($userId, $receiver)) {
                $this->helper->simpleRender(CANCEL_INVITATION_ERROR, true, 200);
                return;
            } else $this->helper->simpleRender(CANCEL_INVITATION_SUCCESS, false, 200);
        });
    }
    
    /**
     * Get my invitations, matched by HTTP GET method
     */
    public function getInvitations() {
        $app = \Slim\Slim::getInstance();
        $app->get('/getInvitations', function() use ($app) {
            global $userId;
            if($res = $this->ih->getInvitationsReceived($userId)) {
                foreach($res as $key => $value) {
                    if(($senderRes = $this->uh->getUserById($res[$key][I_SENDER])) &&
                       ($receiverRes = $this->uh->getUserById($res[$key][I_RECEIVER]))) {
                        $res[$key][I_SENDER] = $senderRes;
                        $res[$key][I_RECEIVER] = $receiverRes;
                    } else $this->helper->simpleRender(GET_INVITATIONS_ERROR, true, 200);
                }
                $this->helper->simpleRenderData(GET_INVITATIONS_SUCCESS, false, 200, array('invitations' => $res));
            } else $this->helper->simpleRender(GET_INVITATIONS_ERROR, true, 200);
        });
    }
}
