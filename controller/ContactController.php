<?php

require_once '../handler/ContactHandler.php';

/**
 * Description of InvitationController
 *
 * @author Jean
 */
class ContactController {
    
    var $helper;
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
        $this->deleteContact();
    }
    
    public function deleteContact() {
        $app = \Slim\Slim::getInstance();
        $app->delete('/deleteContact/:id', function($id) use ($app) {
            global $userId;
            if($userId == $id) {
               $this->helper->simpleRender (DELETE_CONTACT_YOURSELF, true, 200);
                return; 
            } else if(!$this->uh->isUserExistById($id)) {
                $this->helper->simpleRender(USER_NOT_EXISTS, true, 200);
                return;
            } else if(!$this->ch->isContactExist($id, $userId)) {
                $this->helper->simpleRender(CONTACT_DOES_NOT_EXIST, true, 200);
                return;
            } else if(!$this->ch->deleteContact($userId, $id)) {
                $this->helper->simpleRender(DELETE_CONTACT_ERROR, true, 200);
                return;
            } else $this->helper->simpleRender(DELETE_CONTACT_SUCCESS, true, 200);
        });
    }
}
