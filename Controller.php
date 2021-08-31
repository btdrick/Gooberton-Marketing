<?php
require_once './model/Database.php';
require_once './model/Validator.php';
require_once './model/ProductTable.php';
require_once 'autoload.php';

class Controller
{
    private $action;
    private $db;
    private $twig;
    private $product_table;
    
    /**
     * Instantiates a new controller
     */
    public function __construct() {
        $loader = new Twig\Loader\FileSystemLoader('./view');
        $this->twig = new Twig\Environment($loader);
        $this->setupConnection();
        $this->connectToDatabase();
        $this->twig->addGlobal('session', $_SESSION);
        $this->product_table = new ProductTable($this->db);
        
        $this->action = $this->getAction();
    }
    
    /**
     * Initiates the processing of the current action
     */
    public function invoke() {
        switch($this->action) {
            case 'Show Login':
                $this->processShowLogin();
                break;
            case 'Login':
                $this->processLogin();
                break;
            case 'Show Registration':
                $this->processShowRegistration();
                break;
            case 'Register':
                $this->processRegistration();
                break;
            case 'Logout':
                $this->processLogout();
                break;
            case 'Show How It Works':
                $this->processShowHowItWorks();
                break;
            case 'Show Products':
                $this->processShowProducts();
                break;
            case 'Add Order':
                $this->processAddOrder();
                break;
            case 'View Orders':
                $this->processViewOrders();
                break;
            case 'Delete Order':
                $this->processDeleteOrder();
                break;
            case 'Home':
                $this->processShowHomePage();
                break;
            default:
                $this->processShowHomePage();
                break;
        }
    }
    
    /****************************************************************
     * Process Request
     ***************************************************************/
    private function processShowHomePage() {
        $template = $this->twig->load('home.twig');
        echo $template->render();
    }
    
    private function processShowHowItWorks() {
        $template = $this->twig->load('how_it_works.twig');
        echo $template->render();
    }
    
    private function processShowLogin() {
       $login_message = '';   
        $template = $this->twig->load('login.twig');
        echo $template->render(['login_message' => $login_message]);
    }
    
    private function processLogin() {
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');
        if ($this->db->isValidUserLogin($username, $password)) {
            $_SESSION['is_valid_user'] = true;
            $_SESSION['username'] = $username;
            
            $client = $this->db->getClientByUsername($_SESSION['username']);
            $client_name = $client['firstName'] . " " . $client['lastName'];
            $_SESSION['client_name'] = $client_name;
            header("Location: .?action=Show Tasks");
        } else {
            $login_message = 'Invalid username or password';
            $template = $this->twig->load('login.twig');
            echo $template->render(['username' => $username, 'password' => $password, 'login_message' => $login_message]);
        }
    }
    
    private function processShowRegistration() {
        $error_first_name = '';
        $error_last_name = '';
        $error_username = '';
        $error_password = '';
        
        if (isset($_SESSION['is_valid_user'])) {
            $template = $this->twig->load('home.twig');
            echo $template->render();
        } else {
            $template = $this->twig->load('registration.twig');
            echo $template->render(['error_first_name' => $error_first_name], ['error_last_name' => $error_last_name], 
                ['error_username' => $error_username, 'error_password' => $error_password]);
        }
    }
    
    private function processRegistration() {
        $first_name = filter_input(INPUT_POST, 'firstName');
        $last_name = filter_input(INPUT_POST, 'lastName');
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');
        
        
        $validator = new Validator($this->db);
        $error_first_name = $validator->validateName($first_name);
        $error_last_name = $validator->validateName($last_name);
        $error_username = $validator->validateUsername($username);
        $error_password = $validator->validatePassword($password);
        
        if (!empty($error_first_name) || !empty($error_last_name) || !empty($error_username) || !empty($error_password)) {
            $template = $this->twig->load('registration.twig');
            echo $template->render(['error_first_name' => $error_first_name], ['error_last_name' => $error_last_name], 
                    ['error_username' => $error_username, 'error_password' => $error_password],
                    );
        } else {
            $this->db->addUser($first_name, $last_name, $username, $password);
            $_SESSION['is_valid_user'] = true;
            $_SESSION['username'] = $username;
            header("Location: .?action=Show Tasks");
        }
    }
    
    private function processLogout() {
        $_SESSION = array();   // Clear all session data from memory
        session_destroy();     // Clean up the session ID
        $login_message = 'You have been logged out.';
        $template = $this->twig->load('login.twig');
        echo $template->render(['login_message' => $login_message]);
    }
    
    private function processShowProducts() {
        if (!isset($_SESSION['is_valid_user'])) {
            $login_message = 'Log in to view available products.';
            $template = $this->twig->load('login.twig');
            echo $template->render(['login_message' => $login_message]);
        } else {
            $products = $this->product_table->get_all_products();
            $template = $this->twig->load('products.twig');
            echo $template->render(['products' => $products]);
        }
    }
    
    private function processViewOrders() {
        $orders = $this->db->getOrdersByUsername($_SESSION['username']);
        $template = $this->twig->load('orders.twig');
        echo $template->render(['orders' => $orders]);
    }
    
    private function processAddOrder() {
        $product_code = filter_input(INPUT_POST, 'product_code');
        $this->db->addOrder($_SESSION['username'], $product_code);
        
        $orders = $this->db->getOrdersByUsername($_SESSION['username']);
        $template = $this->twig->load('orders.twig');
        echo $template->render(['orders' => $orders]);
    }
    
    private function processDeleteOrder() {
        $order_number = filter_input(INPUT_POST, 'order_number');
        $this->db->deleteOrder($order_number);
        
        $orders = $this->db->getOrdersByUsername($_SESSION['username']);
        $template = $this->twig->load('orders.twig');
        echo $template->render(['orders' => $orders]);
    }
    
    /**
     * Gets the action from $_GET or $_POST array
     * 
     * @return string the action to be processed
     */
    private function getAction() {
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($action === NULL) {
            $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($action === NULL) {
                $action = '';
            }
        }
        return $action;
    }
    
    /**
     * Ensures a secure connection and start session
     */
    private function setupConnection() {
        $https = filter_input(INPUT_SERVER, 'HTTPS');
        if (!$https) {
            $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
            $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
            $url = 'https://' . $host . $uri;
            header("Location: " . $url);
            exit();
        }
        session_start();
    }
    
    /**
     * Connects to the database
     */
    private function connectToDatabase() {
        $this->db = new Database();
        if (!$this->db->isConnected()) {
            $error_message = $this->db->getErrorMessage();
            $template = $this->twig->load('database_error.twig');
            echo $template->render(['error_message' => $error_message]);
            exit();
        }
    }
}