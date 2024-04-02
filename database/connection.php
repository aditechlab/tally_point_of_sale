<?php

class Database {
    
    public $conn = '';

    public function __construct()
    {
        $this->connect();
    }

    public function connect(){

        try{
            
            $host = "localhost";
            $dbname = "tally_pos";
            $username = "root";
            $password = "";


            $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }catch(PDOException $e){
            die("Connection failed: " . $e->getMessage());
        }

        return $this->conn;
    }
}