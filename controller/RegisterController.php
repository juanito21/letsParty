<?php
require_once '../handler/UserHandler.php';

/**
 * Handle the registration actions
 *
 * @author Jean
 */
class RegisterController {
    
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
        $this->register();
        $this->unregister();
        $this->isNewUser();
    }
    
    
    /**
     * Register an user, matched by HTTP POST method
     */
    public function register() {
        $app = \Slim\Slim::getInstance();
        $app->post('/register', function() use ($app) {
            $fields = array('name', 'mail', 'password', 'sex', 'age', 'description');
            $this->helper->verifyRequiredParams($fields);
            $params = $this->helper->constructParamsArray($fields, 'post');
            
            $this->helper->validateEmail($params['mail']);
            
            if(!$this->uh->isUserExistByMail($params['mail'])) {
                $res = $this->uh->createUser(   $params['name'],
                                                $params['password'],
                                                $params['mail'],
                                                $params['age'],
                                                $params['sex'],
                                                $params['description']);
                if($res) $this->helper->simpleRender(REGISTER_SUCCESS, false, 201);
                else $this->helper->simpleRender(REGISTER_ERROR, true, 200);
            } else $this->helper->simpleRender(USER_ALREADY_EXISTED, true, 200);
        });
    }
    
        
    public function unregister() {
        $app = \Slim\Slim::getInstance();  
        $app->delete('/unregister', function() use ($app) { 
            global $userId;
            $res = $this->uh->deleteUser($userId);
            if($res) {
                deletePicturesFromDiskByUser($userId);
                $this->helper->simpleRenderData(UNREGISTER_SUCCESS, false, 200, $res);
            } else $this->helper->simpleRender(UNREGISTER_ERROR, true, 200);
        }); 
    }
    
    /**
     * Check if a user is new or not, matched by HTTP POST method
     */
    public function isNewUser() {
        $app = \Slim\Slim::getInstance();
        $app->get('/isNewUser/:mail', function($mail) use ($app) {
            
            $this->helper->validateEmail($mail);
            
            if(!$this->uh->isUserExistByMail($mail)) $this->helper->simpleRender(USER_NOT_EXISTS, false, 200);
            else $this->helper->simpleRender(USER_EXISTS, true, 200);
        });
    }
    
}
