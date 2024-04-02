<?php
session_start();
error_reporting(E_ALL & ~ E_NOTICE);
require ('../database/connection.php');
require ('../models/Ledger.php');
$database = new Database();
$db = $database->connect();
$ledger = new Ledger($db);



function createLedger()
{
    global $db;
    global $ledger;


    if (!empty($_POST['alias'])){
        foreach ($_POST['alias'] as $key => $values){
            $data = array(
                'master_id' => null,
                'name' => str_replace("'", "''", $_POST['ledger_name']),
                'alias' => $values,
                'parent' => $_POST['parent'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            );
            $name = $ledger->getGroupName();

            if($name == $_POST['ledger_name']) {
                $_SESSION['message']='<div class="alert alert-danger">Name already exists. Please choose a different name.</div>';
                exit;
            }
            $add_response = $ledger->insertLedger($data);
        }
    }

    if($add_response) {
        $_SESSION['message']='<div class="alert alert-success">Ledgers created successfully!</div>';?>
        <script>
            window.history.back();
        </script>
        <?php
    } else{
        $_SESSION['message']='<div class="alert alert-success">Problem in creating ledgers</div>';?>
        <script>
            window.history.back();
        </script>
        <?php
    }
}


function checkLedgerAndAlias(){
    global $ledger;

    if (isset($_POST['ledger_name']) || isset($_POST['alias_name'])){
        $alias = $_POST['alias_name'];
        $name = $_POST['ledger_name'];

        $data = $ledger->getAlias($alias, $name);
        if ($data) {
            echo json_encode(['exists' => true]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }
}

function checkGroupAndAliasDuplicates(){
    global $ledger;
    if (isset($_POST['ledger_name']) || isset($_POST['alias_name'])){
        $alias = $_POST['alias_name'];
        $name = $_POST['ledger_name'];
        $id = $_POST['group_id'];

        $data = $ledger->getAliasAndGroupName($alias, $name, $id);
        if ($data) {
            echo json_encode(['exists' => true]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }
}

function updateGroup()
{
    global $ledger;
    global $db;
    $master_id = $_POST['group_id'];
    $data = array(
        'master_id' => $master_id,
        'name' => str_replace("'", "''", $_POST['ledger_name']),
        'alias' => $_POST['alias_name'],
        'alterid' => 33,
        'parent_master_id' => 0,
        'parent' => $_POST['parent'],
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    );
    $add_response = $ledger->updateStock($data, $master_id);

    $alias = array();
    if(!empty($_POST['alias'])){
        foreach ($_POST['alias'] as $key => $val) {
            $alias = array(
                'master_id' => $master_id,
                'name' => str_replace("'", "''", $_POST['ledger_name']),
                'alias1' => $val,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            $ledger->updateAlias($alias, $master_id);
        }
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


$f = $_GET['f'];
$f();