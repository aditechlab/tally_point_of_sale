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

    public function loadData()
    {
        try{

            $query = "SELECT * FROM StockGroupDataBase";
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

    public function fetch($master_id)
    {
        $query = "SELECT * FROM ledgers WHERE master_id=:masterid";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':masterid', $master_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function insertStock($data)
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

    public function getGroupName()
    {
        $query = "SELECT name FROM ledgers";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['name'];
    }

    public function getId($name)
    {
        $query = "SELECT max(id) as id FROM ledgers";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id'];
    }


//    public function getParentName()
//    {
//        $query = "SELECT parent FROM group_categories";
//        $stmt = $this->con->prepare($query);
//        $stmt->execute();
//        $row = $stmt->fetch(PDO::FETCH_ASSOC);
//        return $row['parent'];
//    }

//    public function getAlias($alias, $name)
//    {
//
//        $query = "SELECT * FROM ledgers s
//              INNER JOIN ledger_alias g ON s.master_id = g.master_id AND s.name=g.name
//              WHERE s.alias = :alias OR g.alias1 = :alias OR s.name=:name";
//        $stmt = $this->con->prepare($query);
//        $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
//        $stmt->bindParam(':alias1', $alias, PDO::PARAM_STR);
//        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
//        $stmt->execute();
//        $row = $stmt->fetch(PDO::FETCH_ASSOC);
//        return $row;
//    }
    public function getAlias($alias, $name)
    {

        $ledger_name = $_POST['ledger_name'];
        $alias_name = $_POST['alias_name'];

        $query = "SELECT * FROM ledgers s
              INNER JOIN ledger_alias g ON s.id = g.ledger_id
              WHERE (g.alias1 = :alias) OR s.name=:name";
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

        $query = "SELECT * FROM ledgers s
              INNER JOIN ledger_alias g ON s.id = g.ledger_id
              WHERE (g.alias1 = :alias) OR s.name=:name AND s.id=:id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':alias', $alias_name, PDO::PARAM_STR);
        $stmt->bindParam(':name', $ledger_name, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function getAliasData($alias)
    {
        $alias_name = $_POST['alias'];

        $query = "SELECT * FROM ledgers l
              INNER JOIN ledger_alias la ON l.id = la.ledger_id
              WHERE (la.alias1 = :alias) OR l.name=:name";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':alias', $alias_name, PDO::PARAM_STR);
        $stmt->bindParam(':name', $alias_name, PDO::PARAM_STR);
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

        $query = "SELECT * FROM ledgers GROUP BY name";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
    public function fetchParentData()
    {

        $query = "SELECT * FROM group_categories GROUP BY parent";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    public function getGroupParent($parent)
    {
        $query = "SELECT * FROM ledgers  WHERE name =:name";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':name', $parent, PDO::PARAM_STR);
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

            $this->batchInsert('ledgers', array_values($data), $columns);

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
            $query = "UPDATE ledgers SET $update_query WHERE master_id='$master_id'";
            // exit;
            $stmt = $this->con->prepare($query);
            $stmt->execute();
            return true;
        } else {
            return false;
        }
    }

}