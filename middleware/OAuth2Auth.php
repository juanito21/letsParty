<?php
/**
 * Middleware for the user authentification to the API
 *
 * @author Jean
 */

class OAuth2Auth extends \Slim\Middleware {
    
    var $uh;
    var $helper;
    var $check;
    
    public function __construct($helper, $myConn) {
        $this->uh = new UserHandler($myConn);
        $this->helper = $helper;
        $this->check = array(   '/getUserInfo',
                                '/uploadPicture',
                                '/deletePicture',
                                '/getMyUserInfo',
                                '/setUserDesc',
                                '/sendInvitation',
                                '/rejectInvitation',
                                '/acceptInvitation',
                                '/cancelInvitation',
                                '/disconnect',
                                '/getInvitations',
                                '/deleteContact',
                                '/getMessages',
                                '/sendMessage',
                                '/viewMessages',
                                '/addBlackListedContact',
                                '/unregister'
                            );
    }
    
    public function call() {
        $b = false;
        foreach($this->check as $check) {
            if(strpos($this->app->request()->getPathInfo(), $check) !== false) {
                $b = true;
            }
        }
        if(!$b) {
           $this->next->call();
           return; 
        }
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $apiKey = $headers['Authorization'];
            if(!$this->uh->isApiKeyExist($apiKey)) {
                $this->helper->simpleRender(INVALID_API_KEY, true, 401);
                try {
                    $this->app->stop();
                } catch (Exception $e) {
                    exit();
                }
            } else {
                global $userId;
                $user = $this->uh->getUserByApiKey($apiKey);
                $userId = $user[U_ID];
                if(!$this->uh->isUserConnectedById($userId)){
                    $this->helper->simpleRender(USER_NOT_CONNECTED, true, 401);
                    try {
                       $this->app->stop();
                    } catch (Exception $e) {
                        exit();
                    }
                } else {
                    // Everything is OK, update of the last connection time...
                    $this->uh->setUserLastConn($userId);
                }
            }
        }  else {
            $this->helper->simpleRender(API_KEY_MISSING, true, 400);
            try {
               $this->app->stop();
            } catch (Exception $e) {
                exit();
            }
        }
        $this->next->call();
    }
}
