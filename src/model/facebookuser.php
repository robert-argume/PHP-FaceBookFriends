<?php
class FacebookUser{
 
    // database connection and table name
    private $conn;
    private $table_name = "facebookuser";
 
    // object properties
    public $id;
    public $userid;
    public $accesstoken;
    public $name;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read all facebook users
    function read(){
    
        // select all query
        $query = "SELECT
                    p.id, p.userid, p.accesstoken, p.name 
                FROM
                    " . $this->table_name . " p";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    // To get one specific facebook user
    function readOne(){
    
        // query to read single record
        $query = "SELECT
                        p.id, p.userid, p.accesstoken, p.name 
                FROM
                    " . $this->table_name . " p
                WHERE
                    p.userid = ?
                LIMIT
                    0,1";
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
    
        // bind user by userid, so we can operate on it
        $stmt->bindParam(1, $this->userid);
    
        // execute query
        $stmt->execute();
    
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // set values to object properties
        $this->id = $row['id'];
        $this->userid = $row['userid'];
        $this->accesstoken = $row['accesstoken'];
        $this->name = $row['name'];
    }
}