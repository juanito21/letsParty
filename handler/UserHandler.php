<?php

require_once '../include/PassHash.php';
require_once 'Handler.php';

/**
 * Class to handle user with database
 */
class UserHandler extends Handler {
	
    // constructor
    function __construct($myConn) {
        parent::__construct($myConn);
    }
    
    /**
     * Create an user into the database
     * @param string $name
     * @param string $password
     * @param string $mail
     * @param int $sex
     * @param string $desc
     * @return boolean
     */
    function createUser($name, $password, $mail, $sex, $desc) {
        $sql = "INSERT INTO " . T_USERS . " (".U_MAIL.",".U_NAME.",".U_SEX.",".U_DESC.",".U_STATUS.",".U_ACTIVE.",".U_CREATED_AT.",".U_PASS_HASH.",".U_API_KEY.")"
                . "VALUES (?,?,?,?,?,?,now(),?,?)";
        $passHash = PassHash::hash($password);
        $apiKey = generateApiKey();
        while($this->isApiKeyExist($apiKey)) $apiKey = generateApiKey();
        $status = DEFAULT_USER_STATUS;
        $active = DEFAULT_USER_ACTIVE;
        $params = array($mail, $name, $sex, $desc, $status, $active, $passHash, $apiKey);
        return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Check if the user's mail exists in the database
     * @param string $mail
     * @return boolean
     */
    function isUserExistByMail($mail) {
        $sql = "SELECT COUNT(".U_ID.") FROM " . T_USERS . " WHERE " . U_MAIL . " = ?";
        $params = array($mail);
        return (parent::preparedQueryCount($sql, $params)>0);
    }
    
    /**
     * Check if the user's mail exists in the database
     * @param string $mail
     * @return boolean
     */
    function isUserExistById($id) {
        $sql = "SELECT COUNT(".U_ID.") FROM " . T_USERS . " WHERE " . U_ID . " = ?";
        $params = array($id);
        return (parent::preparedQueryCount($sql, $params)>0);
    }
    
    /**
     * Get user's infos from the mail's user
     * @param string $mail
     * @return array
     */
    function getUserByMail($mail) {
        $sql = "SELECT * FROM " . T_USERS . " WHERE " . U_MAIL . " = ?";
        $params = array($mail);
        return parent::preparedQuery($sql, $params, PDO::FETCH_ASSOC);
    }
    
    /**
     * Get user's infos from the mail's user
     * @param string $mail
     * @return array
     */
    function getUserById($id) {
        $sql = "SELECT * FROM " . T_USERS . " WHERE " . U_ID . " = ?";
        $params = array($id);
        return parent::preparedQuery($sql, $params, PDO::FETCH_ASSOC);
    }
    
    /**
     * Get user's infos from the api key's user
     * @param string $apiKey
     * @return array
     */
    function getUserByApiKey($apiKey) {
        $sql = "SELECT * FROM " . T_USERS . " WHERE " . U_API_KEY . " = ?";
        $params = array($apiKey);
        return parent::preparedQuery($sql, $params, PDO::FETCH_ASSOC);
    } 
    
    /**
     * Set the status of the user (connect/disconnect)
     * @param string $mail
     * @param int $status
     * @return boolean
     */
    function setStatusUser($mail, $status) {
        $sql = "UPDATE " . T_USERS . " SET " . U_STATUS . " = ? WHERE " . U_ID . " = ?";
        $params = array($status, $mail);
        return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Check if the user is connected with the user's mail
     * @param string $mail
     * @return boolean
     */
    function isUserConnectedById($id) {
        $sql = "SELECT COUNT(".U_ID.") FROM " . T_USERS . ""
                . " WHERE " . U_ID . " = ?"
                . " AND " . U_STATUS . " = ?";
        $status = CONNECTED_STATUS;
        $params = array($id, $status);
        return (parent::preparedQueryCount($sql, $params)>0);
    }
    
    /**
     * Check if the user is connected with the user's id
     * @param string $mail
     * @return boolean
     */
    function isUserConnectedByMail($mail) {
        $sql = "SELECT COUNT(".U_ID.") FROM " . T_USERS . ""
                . " WHERE " . U_MAIL . " = ?"
                . " AND " . U_STATUS . " = ?";
        $status = CONNECTED_STATUS;
        $params = array($mail, $status);
        return (parent::preparedQueryCount($sql, $params)>0);
    }
    
    /**
     * Check if the login is accepted
     * @param string $mail
     * @param string $password
     * @return the array user information or false else
     */
    public function checkLogin($mail, $password) {
        $sql = "SELECT * FROM " . T_USERS . " WHERE " . U_MAIL . " = ?";
        $params = array($mail);
        $user = parent::preparedQuery($sql, $params, PDO::FETCH_ASSOC);
        if($user) {
            if(count($user)>0) {
                if(PassHash::check_password($user[U_PASS_HASH], $password)) return $user;
                else return false;
            } else return false;
        } else return false;
    }
        
    /**
     * Check if an API key is valid or not
     * @param string $api_key
     * @return boolean
     */
    public function isApiKeyExist($apiKey) {
        $sql = "SELECT COUNT(" . U_ID . ") FROM " . T_USERS . " WHERE " . U_API_KEY . " = ?";
        $params = array($apiKey);
        return (parent::preparedQueryCount($sql, $params)>0);
    }
    
    /**
     * Update the last connection field user
     * @param string $mail
     * @return boolean
     */
    public function updateLastConnection($mail) {
        $sql = "UPDATE " . T_USERS . " SET " . U_LAST_CONN . " = now() WHERE " . U_MAIL . " = ?";
        $params = array($mail);
        return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Set user description
     * @param int $id
     * @param string $desc
     * @return boolean
     */
    public function setUserDesc($id, $desc) {
        $sql = "UPDATE " . T_USERS . " SET " . U_DESC . " = ? WHERE " . U_ID . " = ?";
        $params = array($desc, $id);
        return parent::preparedUpdate($sql, $params);
    }
    
    /**
     * Check if a user is active or not
     * @param int $id
     * @return boolean
     */
    public function isUserActive($id) {
        $sql = "SELECT COUNT(" . U_ID . ") FROM " . T_USERS . " WHERE " . U_ID . " = ? AND " . U_ACTIVE . " = ?";
        $status = USER_NOT_ACTIVE;
        $params = array($id, $status);
        return (parent::preparedQueryCount($sql, $params)>0);
    }

}