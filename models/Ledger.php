<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once('BaseModel.php');

class Ledger extends BaseModel
{
    public $con = '';

    function __construct($db)
    {
        $this->con = $db;
    }


    public function all()
    {
        try{
            $query = "SELECT * FROM ledgers";
            $stmt  = $this->con->prepare($query);
            $stmt->execute();
            $StockGroups = array();
            while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
                $StockGroups[] = $rows;
            }
            return $StockGroups;

        }catch(PDOException $e){
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function fetch($id)
    {
        $query = "SELECT * FROM ledgers WHERE id=:id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function insertLedger($data)
    {
        if(!empty($data) && is_array($data)){
            foreach($data as $key => $value){
                if(is_null($value) || $value == '')
                    unset($data[$key]);
            }

            $fields = implode(",", array_keys($data));
            $values = implode("','", array_values($data));
            $query = "INSERT INTO ledgers($fields) VALUES ('$values')";
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            return true;

        }else{
            return false;
        }
    }

    public function updateLedger($data, $master_id)
    {
        if (!empty($data) && is_array($data)) {
            $update_query = '';
            $total_data = count($data);

            foreach ($data as $columns => $values) {
                $update_query .= "$columns = '$values'";
                if($total_data > 1)
                {
                    $update_query .= ",";
                    $total_data--;
                }
            }
            //	update query
            $update_query=rtrim($update_query,',');
            $query = "UPDATE stock_groups SET $update_query WHERE master_id='$master_id'";
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            return true;
        } else {
            return false;
        }
    }


    public function getGroupName()
    {
        $query = "SELECT name FROM stock_groups";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['name'];
    }
    
    public function getAlias($alias, $name)
    {

        $ledger_name = $_POST['ledger_name'];
        $alias_name = $_POST['alias_name'];

        $query = "SELECT * FROM ledgers WHERE (alias = :alias) OR name=:name";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':alias', $alias_name, PDO::PARAM_STR);
        $stmt->bindParam(':name', $ledger_name, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function getAliasAndGroupName($alias, $name, $id)
    {

        $group_name = $_POST['group_name'];
        $alias_name = $_POST['alias_name'];

        $query = "SELECT * FROM stock_groups s
              INNER JOIN group_alias g ON s.id = g.stock_group_id AND s.name=g.name
              WHERE (g.alias1 = :alias) OR s.name=:name AND s.id=:id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':alias', $alias_name, PDO::PARAM_STR);
        $stmt->bindParam(':name', $group_name, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function fetchStockGroups($stock_group_id)
    {

        $query = "SELECT * FROM stock_groups s
              JOIN group_alias g ON s.id = g.stock_group_id
              WHERE s.id = :stock_group_id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':stock_group_id', $stock_group_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    public function fetchParent()
    {

        $query = "SELECT * FROM stock_groups GROUP BY name";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
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
            'alterid',
            'created_at',
            'updated_at'
        ];

        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_null($value) || $value == '')
                    unset($data[$key]);
            }

            $this->batchInsert('stock_groups', array_values($data), $columns);

            return true;
        } else {
            return false;
        }
    }

    public function update($StockGroupDetails, $master_id)
    {
        if (!empty($StockGroupDetails) && is_array($StockGroupDetails)) {
            $update_query = '';
            $total_data = count($StockGroupDetails);

            foreach ($StockGroupDetails as $columns => $values) {
                $update_query .= "$columns = '$values'";
                if($total_data > 1)
                {
                    $update_query .= ",";
                    $total_data--;
                }
            }
            //	update query
            $update_query=rtrim($update_query,',');
            $query = "UPDATE stock_groups SET $update_query WHERE master_id='$master_id'";
            // exit;
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            return true;
        } else {
            return false;
        }
    }

}