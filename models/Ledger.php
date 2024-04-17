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
            $query = "SELECT l.ledger_name as ledger_name, s.name as name, l.id as id FROM ledgers l inner join stock_groups s on l.parent=s.id group by l.id";
            $stmt  = $this->con->prepare($query);
            $stmt->execute();
            $Ledgers = array();
            while($rows = $stmt->fetch(PDO::FETCH_ASSOC)){
                $Ledgers[] = $rows;
            }
            return $Ledgers;

        }catch(PDOException $e){
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function fetch($master_id)
    {
        $query = "SELECT * FROM ledgers WHERE master_id=:masterid";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':masterid', $master_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function fetchMaxLedgerId($name)
    {
        $query = "SELECT max(id) as id FROM ledgers WHERE ledger_name=:name";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id'];
    }

    public function insertLedger($data)
    {
        try {
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
        }catch(PDOException $e){
            echo "Error: " . $e->getMessage();
            return false;
        }

    }


    public function updateStock($data, $master_id)
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
            $query = "UPDATE ledgers SET $update_query WHERE id='$master_id'";
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            return true;
        } else {
            return false;
        }
    }



    public function insertAlias($alias)
    {
        if(!empty($alias) && is_array($alias)){
            foreach($alias as $key => $value){
                if(is_null($value) || $value == '')
                    unset($alias[$key]);
            }

            $fields = implode(",", array_keys($alias));
            $values = implode("','", array_values($alias));
            $query = "INSERT INTO ledger_alias ($fields) VALUES ('$values')";
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            return true;

        }else{
            return false;
        }
    }

    public function updateAlias($data, $alias_id)
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
            $query = "UPDATE ledger_alias SET $update_query WHERE Id='$alias_id'";
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            return true;
        } else {
            return false;
        }
    }

    function removeAlias($alias_id)
    {
        if(!empty($alias_id)){
            $query = "DELETE FROM ledger_alias WHERE Id='$alias_id'";
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            return true;
        }else{
            return false;
        }
    }

    public function fetchLedgerName()
    {
        $query = "SELECT ledger_name FROM ledgers";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['ledger_name'];
    }





    public function getAlias($alias, $name)
    {

        $ledger_name = $_POST['ledger_name'];
        $alias_name = $_POST['alias_name'];

        $query = "SELECT * FROM ledgers s
              INNER JOIN ledger_alias g ON s.id = g.ledger_id
              WHERE (g.alias1 = :alias) OR s.ledger_name=:name";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':alias', $alias_name, PDO::PARAM_STR);
        $stmt->bindParam(':name', $ledger_name, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function getAliasAndGroupName($alias, $name, $id)
    {

        $ledger_name = $_POST['ledger_name'];
        $alias_name = $_POST['alias_name'];

//        if (strpos($ledger_name, '-') === 0 || strrpos($ledger_name, '-') === (strlen($ledger_name) - 1) ||
//            strpos($ledger_name, ';') === 0 || strrpos($ledger_name, ';') === (strlen($ledger_name) - 1)) {
//            return false;
//        }

        $query = "SELECT * FROM ledgers s
              INNER JOIN ledger_alias g ON s.id = g.ledger_id
              WHERE (g.alias1 = :alias) OR s.ledger_name=:name or s.id=:id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':alias', $alias_name, PDO::PARAM_STR);
        $stmt->bindParam(':name', $ledger_name, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function getAliasData($alias, $editedIndex = -1)
    {
        $alias_name = $alias;

        $query = "SELECT * FROM ledgers l
            INNER JOIN ledger_alias la ON l.id = la.ledger_id
            WHERE ((la.alias1 = :alias AND la.ledger_id <> :editedIndex) OR l.ledger_name=:name)";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':alias', $alias_name, PDO::PARAM_STR);
        $stmt->bindParam(':name', $alias_name, PDO::PARAM_STR);
        $stmt->bindParam(':editedIndex', $editedIndex, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function fetchStockGroups($group_id)
    {

        $query = "SELECT * FROM ledgers s LEFT JOIN ledger_alias g ON s.id = g.ledger_id WHERE s.id = :group_id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $row;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function fetchParent()
    {

        $query = "SELECT * FROM ledgers GROUP BY ledger_name";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    public function getGroupParent($parent)
    {
        $query = "SELECT parent FROM stock_groups  WHERE id =:name";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':name', $parent, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['parent'];
    }

    public function fetchGroup($id)
    {
        try {
            $query = "SELECT name from stock_groups where id=:id";
            $result = $this->con->prepare($query);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            $result->execute();
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            return $rows;
        }catch (PDOException $e){
            echo "Error: ". $e->getMessage();
        }
    }


}