<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once('BaseModel.php');

class StockItem extends BaseModel
{
    public $con = '';

    function __construct($db)
    {
        $this->con = $db;
    }

    public function loadData()
    {
        try{
            $query = "SELECT * FROM StockItem";
            $result = odbc_exec($this->con, $query);
            $list = array();
            while ($row = odbc_fetch_array($result)){
                $list[] = $row;
            }
            return $list;
        }catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return false; 
        }
    }

    public function all()
    {
        $query = "SELECT * FROM stock_items";
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
        $query = "SELECT * FROM stock_items WHERE master_id=:masterid";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':masterid', $master_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function add($data)
    {
        $columns = [
            'master_id',
            'name',
            'alias',
            'alias1',
            'parent_master_id',
            'parent',
            'alter_id',
            'created_at',
            'updated_at'
        ];

        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_null($value) || $value == '')
                    unset($data[$key]);
            }

            $this->batchInsert('stock_items', array_values($data), $columns);

            return true;
        } else {
            return false;
        }
    }

    public function update($data, $master_id)
    {
        if (!empty($data) && is_array($data)) {
            $update_query = '';
            $tol_data = count($data);

            foreach ($data as $columns => $values) {
                if ($values == "" || $values == "null") //if value is nul then not add in string
                {
                    $tol_data--;
                } else {
                    if ($tol_data > 1) {
                        $update_query .= "$columns";
                        $update_query .= "=";
                        $update_query .= "'";
                        $update_query .= $values;
                        $update_query .= "',";
                        $tol_data--;
                    } else {
                        $update_query .= "$columns";
                        $update_query .= "=";
                        $update_query .= "'";
                        $update_query .= $values;
                        $update_query .= "'";
                    }
                }
            }
            //	update query
            $update_query=rtrim($update_query,',');
            $query = "UPDATE stock_items SET $update_query WHERE master_id=$master_id";
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            return true;
        } else {
            return false;
        }
    }
}