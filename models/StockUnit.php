<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once('BaseModel.php');

class StockUnit extends BaseModel
{
    public $con = '';
    
    public function __construct($db)
    {
        $this->con = $db;
    }
    
    public function loadData()
    {
        try{
            $query = "SELECT * FROM StockUnitsDataBase";
            $result = odbc_exec($this->con, $query);
            $params = array();
            while($rows = odbc_fetch_array($result)){
                $params[] = $rows;
            }
            return $params;
            
        }catch(Exception $e){
            echo "Error: " . $e->odbc_errormsg();
            return false; 
        }
    }

    public function all()
    {
        $query = "SELECT * FROM stock_units";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $list = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = $row;
        }
        return $list;
    }

    public function fetch($master_id) 
    {
        $query = "SELECT * FROM stock_units WHERE master_id=:masterid";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':masterid', $master_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function add($data)
    {
        $columns = [
            'master_id',
            'name',
            'original_name',
            'is_simple_unit',
            'base_units',
            'additional_units',
            'conversion',
            'decimal_places',
            'alter_id',
            'created_at',
            'updated_at'
        ];

        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_null($value) || $value == '')
                    unset($data[$key]);
            }

            $this->batchInsert('stock_units', array_values($data), $columns);

            return true;
        } else {
            return false;
        }
    }
    
    public function update($params, $master_id)
    {
        if(!empty($params) && is_array($params)){
            $update_query = '';
            $total_data = count($params);
            
            foreach($params as $columns => $values){
                $update_query .= "$columns = '$values' ";
                
                if($total_data > 1){
                    $update_query .= ", ";
                    $total_data--;
                }
            }
            
            $update_query=rtrim($update_query,',');
            $query = "UPDATE stock_units SET $update_query WHERE master_id='$master_id'";
            $stmt = $this->con->prepare($query);
            $stmt->execute();

            return true;
        } else {
            return false;
        }
    }
}