<?php

/**
 * Handle the user actions
 *
 * @author Jean
 */
class UserController {
    
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
        $this->getUserInfo();
        $this->getMyUserInfo();
        $this->setUserDesc();
    }
    
    /**
     * Get user info, matched by HTTP GET method
     */
    public function getUserInfo() {
        $app = \Slim\Slim::getInstance();  
        $app->get('/getUserInfo/:id', function($id) use ($app) { 
            $res = $this->uh->getUserById($id);
            if($res) $this->helper->simpleRenderData(GET_USER_SUCCESS, false, 200, $res);
            else $this->helper->simpleRender(GET_USER_ERROR, true, 200);
        });
    }
    
    /**
     * Get my user info, matched by HTTP GET method
     */
    public function getMyUserInfo() {
        $app = \Slim\Slim::getInstance();  
        $app->get('/getMyUserInfo', function() use ($app) {
            global $userId;
            $res = $this->uh->getUserById($userId);
            if($res) $this->helper->simpleRenderData(GET_USER_SUCCESS, false, 200, $res);
            else $this->helper->simpleRender(GET_USER_ERROR, true, 200);
        });
    }
    
    /**
     * Get my user info, matched by HTTP GET method
     */
    public function setUserDesc() {
        $app = \Slim\Slim::getInstance();  
        $app->put('/setUserDesc/:desc', function($desc) use ($app) { 
            global $userId;
            $res = $this->uh->setUserDesc($userId, $desc);
            if($res) $this->helper->simpleRenderData(SET_USER_SUCCESS, false, 200, $res);
            else $this->helper->simpleRender(SET_USER_ERROR, true, 200);
        });
    }
}
