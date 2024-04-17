<?php
session_start();
error_reporting(E_ALL & ~ E_NOTICE);
require ('../database/connection.php');
require ('../database/odbc_connection.php');
require ('../models/StockGroup.php');
$database = new Database();
$db = $database->connect();
$stockgroup = new StockGroup($db);


function createGroup()
{
    global $db;
    global $stockgroup;


    $master = generateUniqueMasterID();

    $parents = array(
        'parent' => $_POST['parent'],
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => null,
    );

    $result = $stockgroup->insertParent($parents);
    if($result){
        $parent_id = $stockgroup->getParentId($_POST['parent']);
    }


    $data = array(
        'master_id' => $master,
        'name' => str_replace("'", "''", $_POST['group_name']),
        'alias' => $_POST['alias_name'],
        'alias1' => "",
        'alterid' => 33,
        'parent_master_id' => $parent_id,
        'parent' => $_POST['parent'],
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => null,
    );
//    echo json_encode($data);
//    exit;



    $name = $stockgroup->getGroupName();

    if($name == $_POST['group_name']) {
        $_SESSION['message']='<div class="alert alert-danger">Name already exists. Please choose a different name.</div>';
        exit;
    }
    $add_response = $stockgroup->insertStock($data);




    $stock_id = $stockgroup->getId($_POST['group_name']);


    $alias = array();
    if(!empty($_POST['alias'])){
        foreach ($_POST['alias'] as $key => $val) {
            $alias = array(
                'stock_group_id' => $stock_id,
                'alias1' => $val,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            );
            $alias_response = $stockgroup->insertAlias($alias);
        }
    }

    if($add_response && $alias_response) {
        $_SESSION['message']='<div class="alert alert-success">Stock groups created successfully!</div>';?>
        <script>
            window.history.back();
        </script>
        <?php
    } else{
        $_SESSION['message']='<div class="alert alert-success">Problem in creating stock groups</div>';?>
        <script>
            window.history.back();
        </script>
        <?php
    }
}

function generateUniqueMasterID($length = 5) {
    $unique_id = uniqid();

    // Add a random component to ensure uniqueness
    $random_component = bin2hex(random_bytes(2));
    $master_id = substr($unique_id, 0, $length - strlen($random_component)) . $random_component;
    return $master_id;
}

function checkGroupName(){
    global $stockgroup;
    $name = $_POST['group_name'];
    if (isset($name)){
        $groupname = $stockgroup->getGroupName($name);
        echo json_encode($groupname);
    }
}

function checkGroupAndAlias(){
    global $stockgroup;

    if (isset($_POST['group_name']) || isset($_POST['alias_name'])){
        $alias = $_POST['alias_name'];
        $name = $_POST['group_name'];

        $data = $stockgroup->getAlias($alias, $name);
        if ($data) {
            echo json_encode(['exists' => true]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }
}

function checkGroupAndAliasDuplicates(){
    global $stockgroup;
    if (isset($_POST['group_name']) || isset($_POST['alias_name'])){
        $alias = $_POST['alias_name'];
        $name = $_POST['group_name'];
        $id = $_POST['group_id'];

        $data = $stockgroup->getAliasAndGroupName($alias, $name, $id);
        if ($data) {
            echo json_encode(['exists' => true]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }
}
function checkGroupAndAliasData(){
    global $stockgroup;
    if (isset($_POST['alias'])){
        $alias = $_POST['alias'];

        $data = $stockgroup->getAliasData($alias);
        if ($data) {
            echo json_encode(['exists' => true]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }
}

function updateGroup()
{
    global $stockgroup;
    global $db;
    $master_id = $_POST['group_id'];
    $data = array(
        'master_id' => null,
        'name' => str_replace("'", "''", $_POST['group_name']),
        'alias' => $_POST['alias_name'],
        'alterid' => 33,
        'parent_master_id' => 0,
        'parent' => $_POST['parent'],
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    );
//    echo json_encode($data);
//    exit;
//    $query = "SELECT * FROM stock_groups WHERE parent = :parent";
//    $stmt = $db->prepare($query);
//    $stmt->bindParam(':parent', $_POST['group_name'], PDO::PARAM_STR);
//    $stmt->execute();
//    $checkParent = $stmt->fetchAll(PDO::FETCH_ASSOC);
//    $count = $checkParent->rowCount();
//    if ($count > 0){
//        $query = "UPDATE stock_groups set parent='".$_POST['group_name']."'";
//    }

    $add_response = $stockgroup->updateStock($data, $master_id);
    $existingAliases = array();
    $sql = "SELECT Id, alias1 FROM group_alias WHERE stock_group_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$master_id]);
    $existingAliases = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $submittedAliases = $_POST['alias'];
    $submittedAliasIds = $_POST['alias_id'];

    $aliasToUpdate = array();
    $aliasToInsert = array();

    foreach ($submittedAliases as $key => $val) {
        $found = false; // Flag to indicate if existing alias found

        foreach ($existingAliases as $existingAlias) {
            if ($existingAlias['Id'] == $submittedAliasIds[$key]) { // Compare IDs
                $found = true;

                // Update existing alias if different
                if ($existingAlias['alias1'] != $val) {
                    $aliasToUpdate[] = array(
                        'id' => $existingAlias['Id'], // Use ID for update
                        'stock_group_id' => $master_id,
                        'alias1' => $val,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    );
                }
                break;
            }
        }

        if (!$found) {
            $aliasToInsert[] = array(
                'stock_group_id' => $master_id,
                'alias1' => $val,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
        }
    }

//    echo json_encode($aliasToInsert);
//    exit;

    foreach ($aliasToUpdate as $data) {
        $stockgroup->updateAlias($data, $data['id']); // Use ID for update
    }

    // Insert new aliases
    foreach ($aliasToInsert as $alias) {
        $stockgroup->insertAlias($alias);
    }






    if($add_response) {
        $_SESSION['message']='<div class="alert alert-success">Stock groups updated successfully!</div>';?>
        <script>
            window.history.back();
        </script>
        <?php
    } else{
        $_SESSION['message']='<div class="alert alert-success">Problem in updating stock groups</div>';?>
        <script>
            window.history.back();
        </script>
        <?php
    }
}

function getParent()
{
    global $db;
    global $stockgroup;
    $name = $_POST['parent'];
    if (isset($name)){
        $groupname = $stockgroup->getGroupParent($name);
        echo json_encode($groupname);
    }

}


$f = $_GET['f'];
$f();