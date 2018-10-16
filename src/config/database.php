<?php
class Database{
    // specify your own database credentials

    // Using parameters for local MySQL database
    // private $host = "localhost";
    // private $db_name = "PullPrototype";
    // private $username = "root";
    // private $password = "123";
    // public $conn;
    
    // Using parameters from JAWSDB_URL external database for Heroku
    private $host = "u28rhuskh0x5paau.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
    private $db_name = "sz5wc58ptown98vx";
    private $username = "k7y4qtya1q9jjbp5";
    private $password = "po9tod5cb2buvzni";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>