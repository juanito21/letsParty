<?php
require_once '../handler/UserHandler.php';
require_once '../handler/PictureHandler.php';

/**
 * Handle the picture upload with the systme file and the database
 *
 * @author Jean
 */
class PictureController {
 
    var $helper;
    var $uh;
    var $ph;
    
    /**
     * The constructor
     * @param Helper $myHelper
     * @param PDO $myConn
     */
    public function __construct($myHelper, $myConn) {
        $this->helper = $myHelper;
        $this->uh = new UserHandler($myConn);
        $this->ph = new PictureHandler($myConn, $this->uh);
        $this->uploadPicture();
        $this->deletePicture();
       
    }
    
    /**
     * Upload a picture, matched by HTTP POST method
     */
    public function uploadPicture() {
        $app = \Slim\Slim::getInstance();
        $app->post('/uploadPicture', function() use ($app) {
            if(isset($_FILES['upload'])) {
                if(verifyUploadPicture($_FILES['upload'])) {
                    global $userId;
                    $nbPicture = $this->ph->getNumberOfPictureByUser($userId);
                    if($nbPicture !== false) {
                        if($nbPicture<LIMIT_PICTURES_BY_USER) {
                            $temp = explode(".", $_FILES['upload']['name']);
                            $extension = end($temp);
                            if($name = $this->ph->insertPicture($userId, $extension)) {
                                if(savingUploadedFile($_FILES['upload'], $name))
                                        $this->helper->simpleRenderData(PICTURE_UPLOADED, false, 201, array($name));
                                else $this->helper->simpleRender(PICTURE_UPLOADED_ERROR, true, 200);
                            } else $this->helper->simpleRender(PICTURE_DATABASE_ERROR, false, 200);
                        } else $this->helper->simpleRender(TOO_MANY_PICTURES, true, 201);
                    } else $this->helper->simpleRender(PICTURE_DATABASE_ERROR, false, 201);
                } else $this->helper->simpleRender(FILE_INCORRECT, true, 200);
            } else $this->helper->simpleRender(FILE_MISSING, true, 400);
        });
    }
    
    /**
     * Delete a picture, matched by HTTP DELETE method
     */
    public function deletePicture() {
        $app = \Slim\Slim::getInstance();
        $app->delete('/deletePicture/:name', function($name) use ($app) {
            if($this->ph->deletePicture($name)) {
                if(deletePictureFromDisk($name)) $this->helper->simpleRender (PICTURE_DELETED, false, 200);
            }
            else $this->helper->simpleRender (PICTURE_DELETED_ERROR, false, 200);
            
        });
    }
}

