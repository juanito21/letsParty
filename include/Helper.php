<?php

/**
 * Provides functions utilities with Slim
 *
 * @author Jean
 */
class Helper {
    
    var $app;
    
    /**
     * The constructor
     * @param Slim $myApp
     */
    public function __construct($myApp) {
        $this->app = $myApp;
    }
    
    /**
     * Check if a mail is valid or not
     * @param string $email
     */
    public function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response["error"] = true;
            $response["message"] = 'Email address is not valid ' . $email;
            $this->render(400, $response);
            $this->app->stop();
        }
    }
    
    /**
     * Construct an array filled with params received by GET/POST method
     * @param array $array
     * @param string $method
     * @return array
     */
    public function constructParamsArray($array, $method) {
        $res = array();
        foreach($array as $param) {
            if($method == 'post') $res[$param] = $this->app->request->post($param);
            if($method == 'put') $res[$param] = $this->app->request->put($param);
        } return $res;
    }
  
    /**
     * Verify if the required params are in the request params
     * @param array $required_fields
     */
    function verifyRequiredParams($required_fields) {
        $error = false;
        $error_fields = "";
        $request_params = $_REQUEST;
        // Handling PUT request params
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            parse_str($this->app->request()->getBody(), $request_params);
        }
        foreach($required_fields as $field) {
            if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
                $error = true;
                $error_fields .= $field . ', ';
            }
        }
        if($error) {
            // Required field(s) are missing or empty
            // echo error json and stop the app
            $response = array();
            $response["error"] = true;
            $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
            $this->render(400, $response);
            $this->app->stop();
        }
    }
    
    /**
     * Render a json response with an HTTP status code
     * @param int $status_code
     * @param array $response
     */
    function render($status_code, $response) {
        // Http response code
        $this->app->status($status_code);
        // setting response content type to json
        $this->app->contentType('application/json'); 
        echo json_encode($response);
    }
    
    /**
     * Simple render, for simple response
     * @param string $message
     * @param string $error
     * @param int $code
     * @return void
     */
    function simpleRender($message, $error, $code) {
        return $this->render($code, array('message'=>$message,'error'=>$error));
    }
    
    /**
     * Simple render, for simple response with data
     * @param string $message
     * @param string $error
     * @param int $code
     * @param array $data
     * @return void
     */
    function simpleRenderData($message, $error, $code, $data) {
        $infos =  array('message'=>$message,'error'=>$error);
        $allData = array_merge($infos, $data);
        return $this->render($code, $allData);
    }
 
}