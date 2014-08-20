<?php

require_once '../handler/ContactHandler.php';

/**
 * Handle the contact actions
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
        $this->addBlackListedContact();
        $this->getMyContacts();
    }
    
    /**
     * Delete a contact, matched by DELETE HTTP method
     */
    public function deleteContact() {
        $app = \Slim\Slim::getInstance();
        $app->delete('/deleteContact/:id', function($id) use ($app) {
            global $userId;
            if($userId == $id) {
               $this->helper->simpleRender(DELETE_CONTACT_YOURSELF, true, 200);
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
    
    
   /**
    * Add a blacklisted contact, matched by HTTP POST method
    */
    public function addBlackListedContact() {
        $app = \Slim\Slim::getInstance();
        $app->post('/addBlackListedContact', function() use ($app) {
            $fields = array('id');
            $this->helper->verifyRequiredParams($fields);
            $params = $this->helper->constructParamsArray($fields, 'post');
            global $userId;
            if($userId == $params['id']) {
                $this->helper->simpleRender (BLACKLIST_YOURSELF_ERROR, true, 200);
                return; 
            } else if(!$this->uh->isUserExistById($params['id'])) {
                $this->helper->simpleRender(USER_NOT_EXISTS, true, 200);
                return;
            } else if($this->ch->isBlackListed($userId, $params['id'])) {
                $this->helper->simpleRender(ALREADY_BLACKLISTED, true, 200);
                return;
            } else if(!$this->ch->addBlackList($userId, $params['id'])) {
                $this->helper->simpleRender(BLACKLIST_ERROR, true, 200);
                return;
            } else $this->helper->simpleRender(BLACKLIST_SUCCESS, false, 200);
        });
    }
    
    public function getMyContacts() {
        $app = \Slim\Slim::getInstance();  
        $app->get('/getMyContacts', function() use ($app) {
            global $userId;
            $res = $this->ch->getContacts($userId);
            $resContact = array();
            foreach($res as $key => $value) {
                if($value[C_SENDER] != $userId) $idContact = $value[C_SENDER];
                else $idContact = $value[C_RECEIVER];
                if($contact = $this->uh->getUserById($idContact)) {
                    $resContact[] = $contact;
                } else $this->helper->simpleRender(GET_CONTACTS_ERROR, true, 200);
            }
            if($res) $this->helper->simpleRenderData(GET_CONTACTS_SUCCESS, false, 200, array('contacts'=> $resContact));
            else $this->helper->simpleRender(GET_CONTACTS_ERROR, true, 200);
        });
    }
}
