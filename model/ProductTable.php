<?php
class ProductTable {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    function get_all_products() {
        $query = 'SELECT * FROM products
                  ORDER BY productCode';
        $statement = $this->db->getDB()->prepare($query);
        $statement->execute();
        $products = $statement->fetchAll();
        $statement->closeCursor();
        return $products;
    }
    
    function get_product_by_name($product_name) {
        $query = 'SELECT * FROM products
                  WHERE name = :product_name';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':product_name', $product_name);
        $statement->execute();
        $product = $statement->fetch();
        $statement->closeCursor();
        return $product;
    }
}
?>