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
        $this->setUserInfo();
        $this->setUserActive();
    }
    
    /**
     * Get user info, matched by HTTP GET method
     */
    public function getUserInfo() {
        $app = \Slim\Slim::getInstance();  
        $app->get('/getUserInfo/:id', function($id) use ($app) { 
            $res = $this->uh->getUserById($id);
            if($res) $this->helper->simpleRenderData(GET_USER_SUCCESS, false, 200, array('user' => $res));
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
            if($res) $this->helper->simpleRenderData(GET_USER_SUCCESS, false, 200, array('user'=> $res));
            else $this->helper->simpleRender(GET_USER_ERROR, true, 200);
        });
    }
    
    /**
     * Set my user info, matched by HTTP PUT method
     */
    public function setUserInfo() {
        $app = \Slim\Slim::getInstance();  
        $app->put('/setMyUserInfo', function() use ($app) { 
            $fields = array('name', 'age', 'sex', 'description');
            $this->helper->verifyRequiredParams($fields);
            $params = $this->helper->constructParamsArray($fields, 'put');
            global $userId;
            $res = $this->uh->setUserInfo(  $userId, 
                                            $params['name'],
                                            $params['description'],
                                            $params['sex'],
                                            $params['age']);
            if($res) $this->helper->simpleRender(SET_USER_SUCCESS, false, 200);
            else $this->helper->simpleRender(SET_USER_ERROR, true, 200);
        });
    }
        
    /**
    * Set my activity, matched by HTTP PUT method
    */
    public function setUserActive() {
        $app = \Slim\Slim::getInstance();  
        $app->put('/setUserActive', function($description) use ($app) { 
            $fields = array('active');
            $this->helper->verifyRequiredParams($fields);
            $params = $this->helper->constructParamsArray($fields, 'put');
            global $userId;
            $res = $this->uh->setUserActive($userId, $params['active']);
            if(!$res) $this->helper->simpleRender(SET_USER_SUCCESS, false, 200);
            else $this->helper->simpleRender(SET_USER_ERROR, true, 200);
        });
    }
}
