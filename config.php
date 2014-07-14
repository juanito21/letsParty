<?php

/* DATABASE CONSTANTS */
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_NAME', 'letsparty');
define('DB_SYS', 'mysql');

/* USERS TABLE CONSTANTS */
define('T_USERS', 'users');
define('U_ID', 'iduser');
define('U_MAIL', 'mail');
define('U_NAME', 'name');
define('U_SEX', 'sex');
define('U_DESC', 'description');
define('U_STATUS', 'status');
define('U_ACTIVE', 'active');
define('U_LAST_CONN', 'last_connection');
define('U_LAST_LOC', 'last_localisation');
define('U_CREATED_AT', 'created_at');
define('U_PASS_HASH', 'password_hash');
define('U_API_KEY', 'api_key');
define('U_PASS', 'password');

/* PICTURES TABLE CONSTANTS */
define('T_PICTURES', 'pictures');
define('P_ID', 'idpicture');
define('P_NAME', 'name');
define('P_USER', 'iduser');

/* INVITATIONS TABLE CONSTANTS */
define('T_INVITATIONS', 'invitations');
define('I_SENDER', 'sender');
define('I_RECEIVER', 'receiver');
define('I_STATUS', 'status');
define('I_UPDATEAT', 'update_at');

/* CONTACTS TABLE CONSTANTS */
define('T_CONTACTS', 'contacts');
define('C_SENDER', 'sender');
define('C_RECEIVER', 'receiver');

/* WHODELETEDWHO TABLE CONSTANTS */
define('T_WHODELWHO', 'whodeletedwho');
define('W_SENDER', 'sender');
define('W_RECEIVER', 'receiver');


/* MESSAGES */
// REGISTER
define('REGISTER_SUCCESS', 'Registered with success !');
define('REGISTER_ERROR', 'An error was detected while registering...');
define('USER_ALREADY_EXISTED', 'This mail is already used...');
define('USER_EXISTS', 'This mail is already used...');
define('USER_NOT_EXISTS', 'This user does not exist !');

// CONNECTION
define('USER_NOT_CONNECTED', 'You are not connected...');
define('USER_ALREADY_CONNECTED', 'You are already connected...');
define('USER_ALREADY_DISCONNECTED', 'You are already disconnected...');
define('CONNECT_SUCCESS', 'You are connected !');
define('CONNECT_ERROR', 'An error was detected while connecting...');
define('DISCONNECT_SUCCESS', 'You are disconnected !');
define('DISCONNECT_ERROR', 'An error was detected while connecting...');
define('WRONG_LOG', 'Your password or your mail is wrong !');

// PICTURE
define('PICTURE_DATABASE_ERROR', 'An error was detected between the picture and the database');
define('PICTURE_UPLOADED', 'Your picture was uploaded with success !');
define('PICTURE_UPLOADED_ERROR', 'An error was detected while saving your picture...');
define('PICTURE_DELETED', 'Picture deleted !');
define('PICTURE_DELETED_ERROR', 'An error was detected while deleting your picture...');
define('FILE_MISSING', 'The file is missing in the HTTP POST request');
define('FILE_INCORRECT', 'The file is incorrect (size/format)');
define('TOO_MANY_PICTURES', 'You have too many pictures !');

// INVITATION
define('SEND_INVITATION_SUCCESS', 'Invitation sent with success !');
define('SEND_INVITATION_ERROR', 'An error was detected while sending invitation...');
define('INVITATION_YOURSELF_ERROR', 'You cannot invite yourself...');
define('REJECT_YOURSELF_ERROR', 'You cannot reject yourself...');
define('ACCEPT_YOURSELF_ERROR', 'You cannot accept yourself...');
define('INVATION_USER_NOT_ACTIVE', 'This person does not want to have party tonight...');
define('INVITATION_ALREADY_REJECTED', 'You have to wait to resend an invitation rejected...');
define('INVITATION_ALREADY_SENT', 'You have already sent an invitation to this user...');
define('INVITATION_DOES_NOT_EXIST', 'This invitation does not exist...');
define('REJECT_INVITATION_ERROR', 'An error was detected while rejecting this invitation...');
define('REJECT_INVITATION_SUCCESS', 'Invitation rejected !');
define('ACCEPT_INVITATION_ERROR', 'An error was detected while accepting this invitation...');
define('ACCEPT_INVITATION_SUCCESS', 'Invitation accepted !');
define('CANCEL_INVITATION_SUCCESS', 'Invitation canceled !');
define('CANCEL_INVITATION_ERROR', 'An error was detected while cancelling this invitation...');
define('GET_INVITATIONS_SUCCESS', 'Invitations loaded with success !');
define('GET_INVITATIONS_ERROR', 'An error was detected while getting your invitations...');
define('GET_SENDER_ERROR', 'An error was detected while getting sender informations...');

// CONTACT
define('CONTACT_DOES_NOT_EXIST', 'This contact does not exit !');
define('DELETE_CONTACT_YOURSELF', 'You cannot delete yourself as contact...');
define('DELETE_CONTACT_ERROR', 'An error was detected while deleting contact...');
define('DELETE_CONTACT_SUCCESS', 'Contact deleted !');

// API
define('API_KEY_MISSING', 'The Api Key is missing !');
define('INVALID_API_KEY', 'Your Api Key is invalid...');

// GETTER
define('GET_USER_SUCCESS', 'User loaded with success !');
define('GET_USER_ERROR', 'An error was detected while getting user...');

// SETTER
define('SET_USER_SUCCESS', 'User set with success !');
define('SET_USER_ERROR', 'An error was detected while setting user...');


/* CODE CONSTANTS */
define('DEFAULT_USER_STATUS', 0);
define('CONNECTED_STATUS', 1);
define('DISCONNECTED_STATUS', 0);
define('DEFAULT_USER_ACTIVE', 0);
define('USER_ACTIVE', 1);
define('USER_NOT_ACTIVE', 0);
define('INVITATION_PENDING', 0);
define('INVITATION_REJECTED', 1);
define('INVITATION_ACCEPTED', 2);


/* INT CONSTANTS */
define('LIMIT_PICTURES_BY_USER', 5);
define('MAX_SIZE_PICTURE', 20000000);
define('RETRY_INVITATION', 1);

/* PATH CONSTATS */
define('PATH_IMG', 'img');
define ('PATH_ROOT', realpath(dirname(__FILE__)));