<?php

/**
 * Handle the connect/disconnect actions
 *
 * @author Jean
 */
class ConnectController {
    
    var $helper;
    var $uh;
    
    /**
     * The constructor
     * @param Helper $myHelper
     * @param PDO $myConn
     */
    public function __construct($myHelper, $myConn) {
        $this->helper = $myHelper;
        $this->uh = new UserHandler($myConn);
        $this->connect();
        $this->disconnect();
    }
    
    /**
     * Connect an user, matched by HTTP POST method
     */
    public function connect() {
        $app = \Slim\Slim::getInstance();  
        $app->put('/connect/:mail/:pass', function($mail, $pass) use ($app) {
            
            $this->helper->validateEmail($mail);
            
            if($this->uh->isUserExistByMail($mail)) {
                if($user = $this->uh->checkLogin($mail, $pass)) {
                    if(!$this->uh->isUserConnectedById($user[U_ID])) {
                        $res = $this->uh->setStatusUser($user[U_ID], CONNECTED_STATUS);
                        if($res) {
                            $this->helper->simpleRenderData(CONNECT_SUCCESS, false, 201, array('user' => $user));
                            $res = $this->uh->updateLastConnection($mail);
                        }
                        else $this->helper->simpleRender(CONNECT_ERROR, true, 200);
                    } else {
                        $this->helper->simpleRenderData(USER_ALREADY_CONNECTED, false, 200, array('user' => $user));
                        $res = $this->uh->updateLastConnection($mail);
                    }
                } else $this->helper->simpleRender(WRONG_LOG, true, 200);
            } else $this->helper->simpleRender(WRONG_LOG, true, 200);
        });  
    }
    
    /**
     * Disconnect an user, matched by HTTP POST method
     */
    public function disconnect() {
        $app = \Slim\Slim::getInstance();  
        $app->put('/disconnect', function() use ($app) {
            global $userId;
            $res = $this->uh->setStatusUser($userId, DISCONNECTED_STATUS);
            if($res) $this->helper->simpleRender(DISCONNECT_SUCCESS, false, 201);
            else $this->helper->simpleRender(DISCONNECT_ERROR, true, 200);
        });  
    }
    
}
