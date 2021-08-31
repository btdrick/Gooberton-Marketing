<?php
class Validator {
    private $db;
    
    /**
     * Instantiates a new validator
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Checks if the specified username is a valid new username
     * 
     * @param string $username
     * @return string an error message if the username is invalid; en empty
     *          string otherwise
     */
    public function validateUsername($username) {
        $error_username = '';
        if (empty($username)) {
            $error_username = 'Enter a username.';
        } elseif (strlen($username) > 20) {
            $error_username = 'Can have at most 20 characters.';
        } elseif ($this->db->isValidUser($username)) {
            $error_username = 'The username exists already.';
        }
        return $error_username;
    }
    
    /**
     * Checks if the specified password is valid
     * 
     * @param type $password
     * @return string an error message if the password is invalid; en empty
     *          string otherwise
     */
    public function validatePassword($password) {
        $error_password = '';
        if (empty($password)) {
            $error_password = 'Enter a password';
        } elseif (strlen($password) < 8) {
            $error_password = 'Must consist of at least 8 characters';
        } elseif (!preg_match('/[[:digit:]]/', $password) ||
            !preg_match('/[[:lower:]]/', $password) ||
            !preg_match('/[[:upper:]]/', $password)) {
                $error_password = 'Must contain a number, an uppercase and a lowercase letter';
        }
        return $error_password;
    }
    
    public function validateName($name) {
        $error_name = '';
        if (empty($name)) {
            $error_name = 'Name cannot be blank.';
        } elseif (strlen($name) > 20) {
            $error_name = 'Can have at most 20 characters.';
        }
        return $error_name;
    }
}
?>