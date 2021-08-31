<?php
class Database {
    private $db;
    private $error_message;
    
    /**
     * Instantiates a new database object that connects
     * to the database
     */
    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=gooberton_marketing';
        $username = 'mgr_brandino';
        $password = 'aw3s0me';
        $this->error_message = '';
        try {
            $this->db = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            $this->error_message = $e->getMessage();
        }
    }
    
    /**
     * Checks the connection to the database
     *
     * @return boolean - true if a connection to the database has been established
     */
    public function isConnected() {
        return ($this->db != Null);
    }
    
    /**
     * Returns the database object 
     * 
     * @return the database object
     */
    public function getDB(){
        return $this->db;
    }
    
    /**
     * Returns the error message
     * 
     * @return string - the error message
     */
    public function getErrorMessage() {
        return $this->error_message;
    }
    
    /**
     * Checks if the specified username is in this database
     * 
     * @param string $username
     * @return boolean - true if username is in this database
     */
    public function isValidUser($username) {
        $query = 'SELECT * FROM clients
              WHERE username = :username';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        return !($row === false);
    }
    
    /**
     * Adds the specified user to the table users
     * 
     * @param type $first_name
     * @param type $last_name
     * @param type $username
     * @param type $password
     */
    public function addUser($first_name, $last_name, $username, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = 'INSERT INTO clients (firstName, lastName, username, password)
              VALUES (:first_name, :last_name, :username, :password)';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':first_name', $first_name);
        $statement->bindValue(':last_name', $last_name);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $hash);
        $statement->execute();
        $statement->closeCursor();
    }
    
    /**
     * Checks the login credentials
     * 
     * @param type $username
     * @param type $password
     * @return boolen - true if the specified password is valid for the 
     *              specified username
     */
    public function isValidUserLogin($username, $password) {
        $query = 'SELECT password FROM clients
              WHERE username = :username';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        $hash = $row['password'];
        return password_verify($password, $hash);
    }
    
    public function getClientByUsername($username) {
        $query = 'SELECT * FROM clients
                WHERE username = :username';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $client = $statement->fetch();
        $statement->closeCursor();
        return $client;
    }
    
    public function getOrdersByUsername($username) {
        $query = 'SELECT * FROM orders
                WHERE username = :username';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $orders = $statement->fetchAll();
        $statement->closeCursor();
        return $orders;
    }
    
    public function addOrder($username, $code) {
        $date = date('Y-m-d H:i:s');
        $query = 'INSERT INTO orders (username, productCode, registrationDate)
              VALUES (:username, :code, :date)';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':code', $code);
        $statement->bindValue(':date', $date);
        $statement->execute();
        $statement->closeCursor();
    }
    
    public function deleteOrder($order_number) {
        $query = 'DELETE FROM orders
                  WHERE orderNumber = :order_number';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':order_number', $order_number);
        $statement->execute();
        $statement->closeCursor();
    }
}
?>