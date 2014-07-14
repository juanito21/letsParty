<?php
require_once '../lib/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

require_once '../include/PassHash.php';
require_once '../include/Helper.php';
require_once '../include/DbConnect.php';
require_once '../controller/RegisterController.php';
require_once '../controller/ConnectController.php';
require_once '../controller/UserController.php';
require_once '../controller/PictureController.php';
require_once '../controller/InvitationController.php';
require_once '../controller/ContactController.php';
require_once '../middleware/OAuth2Auth.php';
require_once '../include/functions.php';
require_once '../config.php';
$userId = NULL;

$db = new DbConnect();
$db->connect();
$conn = $db->getCon();

$app = new \Slim\Slim(array(
    'mode' => 'development',
    'debug' => true
));

$helper = new Helper($app);

$app->add(new OAuth2Auth($helper, $conn));

new RegisterController($helper, $conn);
new ConnectController($helper, $conn);
new UserController($helper, $conn);
new PictureController($helper, $conn);
new InvitationController($helper, $conn);
new ContactController($helper, $conn);

$db->close();

$app->run();