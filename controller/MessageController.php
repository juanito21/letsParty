<?php

require_once '../handler/MessageHandler.php';
require_once '../handler/UserHandler.php';
require_once '../handler/ContactHandler.php';

/**
 * Handle the message actions
 *
 * @author Jean
 */
class MessageController {

    var $helper;
    var $uh;
    var $ch;
    var $mh;
    
    /**
     * The constructor
     * @param Helper $myHelper
     * @param PDO $myConn
     */
    public function __construct($myHelper, $myConn) {
        $this->helper = $myHelper;
        $this->uh = new UserHandler($myConn);
        $this->mh = new MessageHandler($myConn);
        $this->ch = new ContactHandler($myConn, $this->uh);
        $this->sendMessage();
        $this->getMessages();
        $this->viewMessages();
    }
    
    /*
     * Send a message to a user, matched by HTTP POST method
     */
    public function sendMessage() {
        $app = \Slim\Slim::getInstance();
        $app->post('/sendMessage', function() use ($app) {
            $fields = array('receiver', 'content');
            $this->helper->verifyRequiredParams($fields);
            $params = $this->helper->constructParamsArray($fields, 'post');
            global $userId;
            if($userId == $params['receiver']) {
                $this->helper->simpleRender(MESSAGE_YOURSELF, true, 200);
                return;
            } else if(!$this->ch->isContactExist($userId, $params['receiver'])) {
                $this->helper->simpleRender(CONTACT_DOES_NOT_EXIST, true, 200);
                return;
            } else if(!$this->mh->addMessage($userId, $params['receiver'], $params['content'])) {
                $this->helper->simpleRender(MESSAGE_SENT_ERROR, true, 200);
                return;
            } else $this->helper->simpleRender(MESSAGE_SENT_SUCCESS, false, 200);   
        });
    }
    
    /**
     * Get my messages, matched by HTTP PUT method
     */
    public function getMessages() {
        $app = \Slim\Slim::getInstance();
        $app->put('/getMessages/:id', function($id) use ($app) {
            global $userId;
            if(!$this->ch->isContactExist($userId, $id)) {
                $this->helper->simpleRender(CONTACT_DOES_NOT_EXIST, true, 200);
                return;
            } else if(!$res = $this->mh->getMessages($userId, $id)) {
                $this->helper->simpleRender(GET_MESSAGES_ERROR, true, 200);
                return;
            } else $this->helper->simpleRenderData(GET_MESSAGES_SUCCESS, false, 200, $res);   
        });
    }
    
    /**
     * View the messages with a contact, matched by HTTP PUT method
     */
    public function viewMessages() {
        $app = \Slim\Slim::getInstance();
        $app->put('/viewMessages/:id', function($id) use ($app) {
            global $userId;
            if(!$this->ch->isContactExist($userId, $id)) {
                $this->helper->simpleRender(CONTACT_DOES_NOT_EXIST, true, 200);
                return;
            } else if(!$this->mh->setViewAt($userId, $id)) {
                $this->helper->simpleRender(SET_VIEWAT_MESSAGES_ERROR, true, 200);
                return;
            } else $this->helper->simpleRender(SET_VIEWAT_MESSAGES_SUCCESS, false, 200);   
        });
    }
}
