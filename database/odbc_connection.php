<?php

class ODBCDatabase {
    
    public $conn = '';

    public function __construct()
    {
        $this->connect();
    }

    public function connect(){

        try{
            
            $dsn = 'Driver={Tally ODBC Driver64};Server=localhost;Database=topmarket;Port=9000';
            $username = 'root';
            $password = '';


            $this->conn = odbc_connect($dsn, $username, $password);

        }catch(PDOException $e){
            die("Connection failed: " . odbc_errormsg());
        }

        return $this->conn;
    }
}