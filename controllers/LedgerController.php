<?php
session_start();
error_reporting(E_ALL & ~ E_NOTICE);
require ('../database/connection.php');
require ('../models/Ledger.php');
require ('../models/StockGroup.php');
$database = new Database();
$db = $database->connect();
$ledger_data = new Ledger($db);



function createLedger()
{
    global $ledger_data;

    $data = array(
        'ledger_name' => str_replace("'", "''", $_POST['ledger_name']),
//        'alias' => $_POST['alias_name'],
        'parent' => $_POST['parent'],
        'pay_by_bill' => $_POST['pay_by_bill'],
        'credit_period' => $_POST['credit_period'],
        'contact_name' => $_POST['contact_name'],
        'address' => $_POST['address'],
        'address1' => $_POST['address1'],
        'address2' => $_POST['address2'],
        'address3' => $_POST['address3'],
        'primary_phone_number' => $_POST['primary_phone_no'],
        'phone_number' => $_POST['phone_no'],
        'email' => $_POST['email'],
        'ccemail' => $_POST['ccemail'],
        'account_name' => $_POST['account_name'],
        'account_number' => $_POST['account_no'],
        'bank_code' => $_POST['bank_code'],
        'bank_name' => $_POST['bank_name'],
        'branch' => $_POST['branch'],
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => null,
    );
//    echo json_encode($data);
//    exit;



    $name = $ledger_data->fetchLedgerName();

    if($name == $_POST['ledger_name']) {
        $_SESSION['message']='<div class="alert alert-danger">Name already exists. Please choose a different name.</div>';
        exit;
    }
    $add_response = $ledger_data->insertLedger($data);

    $ledger_id = $ledger_data->fetchMaxLedgerId($_POST['ledger_name']);

    $alias = array();
    if(!empty($_POST['alias'])){
        foreach ($_POST['alias'] as $key => $val) {
            $alias = array(
                'ledger_id' => $ledger_id,
                'alias1' => $val,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            );
            $ledger_data->insertAlias($alias);
        }
    }

    if($add_response) {
        $_SESSION['message']='<div class="alert alert-success">Ledgers created successfully!</div>';?>
        <script>
            window.history.back();
        </script>
        <?php
    } else{
        $_SESSION['message']='<div class="alert alert-success">Problem in creating Ledgers</div>';?>
        <script>
            window.history.back();
        </script>
        <?php
    }
}

function updateLedger()
{
    global $ledger_data;
    global $db;
    $master_id = $_POST['group_id'];
    $data = array(
        'ledger_name' => str_replace("'", "''", $_POST['ledger_name']),
        'parent' => $_POST['parent'],
        'pay_by_bill' => $_POST['pay_by_bill'],
        'credit_period' => $_POST['credit_period'],
        'contact_name' => $_POST['contact_name'],
        'address' => $_POST['address'],
        'address1' => $_POST['address1'],
        'address2' => $_POST['address2'],
        'address3' => $_POST['address3'],
        'primary_phone_number' => $_POST['primary_phone_no'],
        'phone_number' => $_POST['phone_no'],
        'email' => $_POST['email'],
        'ccemail' => $_POST['ccemail'],
        'account_name' => $_POST['account_name'],
        'account_number' => $_POST['account_no'],
        'bank_code' => $_POST['bank_code'],
        'bank_name' => $_POST['bank_name'],
        'branch' => $_POST['branch'],
        'updated_at' => date('Y-m-d H:i:s'),
    );
//    echo json_encode($data);
//    exit;


    $add_response = $ledger_data->updateStock($data, $master_id);
    $existingAliases = array();
    $sql = "SELECT Id, alias1 FROM ledger_alias WHERE ledger_id = ?";
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
                        'ledger_id' => $master_id,
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
                'ledger_id' => $master_id,
                'alias1' => $val,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
        }
    }

//    echo json_encode($aliasToInsert);
//    exit;

    foreach ($aliasToUpdate as $data) {
        $ledger_data->updateAlias($data, $data['id']); // Use ID for update
    }

    // Insert new aliases
    foreach ($aliasToInsert as $alias) {
        $ledger_data->insertAlias($alias);
    }






    if($add_response) {
        $_SESSION['message']='<div class="alert alert-success">Ledgers updated successfully!</div>';?>
        <script>
            window.location.href = '../views/view-ledger.php';
        </script>
        <?php
    } else{
        $_SESSION['message']='<div class="alert alert-success">Problem in updating Ledgers</div>';?>
        <script>
            window.history.back();
        </script>
        <?php
    }
}


function checkGroupName(){
    global $ledger_data;
    $name = $_POST['ledger_name'];
    if (isset($name)){
        $groupname = $ledger_data->getGroupName($name);
        echo json_encode($groupname);
    }
}

function checkGroupAndAlias(){
    global $ledger_data;

    if (isset($_POST['ledger_name']) || isset($_POST['alias_name'])){
        $alias = $_POST['alias_name'];
        $name = $_POST['ledger_name'];

        $data = $ledger_data->getAlias($alias, $name);
        if ($data) {
            echo json_encode(['exists' => true]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }
}

function checkGroupAndAliasDuplicates(){
    global $ledger_data;
    if (isset($_POST['ledger_name']) || isset($_POST['alias_name'])){
        $alias = $_POST['alias_name'];
        $name = $_POST['ledger_name'];
        $id = $_POST['group_id'];

        $data = $ledger_data->getAliasAndGroupName($alias, $name, $id);
        if ($data) {
            echo json_encode(['exists' => true]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }
}
function checkLedgerAliasData(){
    global $ledger_data;
    if (isset($_POST['alias'])){
        $alias = $_POST['alias'];
        $editedIndex = isset($data['editedIndex']) ? $data['editedIndex'] : -1;

        $data = $ledger_data->getAliasData($alias,$editedIndex);
        if ($data) {
            echo json_encode(['exists' => true]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }
}



function getParent()
{
    global $db;
    global $ledger_data;
    $name = $_POST['parent'];
    if (isset($name)){
        $groupname = $ledger_data->getGroupParent($name);
        echo json_encode($groupname);
    }

}

function viewGroups()
{
    global $ledger_data;
    $id = $_POST['parent'];
    if(isset($id)){
        $group = $ledger_data->fetchGroup($id);
        echo json_encode($group);
    }
}

function deleteAlias()
{
    global $ledger_data;
    $alias_id = $_POST['alias_id'];
    if (isset($alias_id)){
        $groupname = $ledger_data->removeAlias($alias_id);
        echo json_encode($groupname);
    }
}


$f = $_GET['f'];
$f();