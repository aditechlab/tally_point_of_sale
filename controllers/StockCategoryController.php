<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require('../database/connection.php');
require('../database/odbc_connection.php');
require('../models/StockCategory.php');
$database = new Database();
$db = $database->connect();
$stock_category = new StockCategory($db);

$dbconfig = new ODBCDatabase();
$odbc = $dbconfig->connect();
$tally_stock =  new StockCategory($odbc);

function create()
{
    global $stock_category;
    global $tally_stock;

    try {

        $categories = $tally_stock->loadData();
        $new_data = [];
        $update_response = false;

        foreach($categories as $category) {

            $data = array(
                'master_id' => $category['$_MASTERID'], 
                'name' => str_replace("'", "''", $category['$_Name']),
                'alias' => $category['$_Alias'],
                'alias1' => $category['$_Alias1'],
                'parent_master_id' => $category['$_ParentMasterID'],
                'parent' => $category['$Parent'],
                'alter_id' => $category['$Alterid'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            );

            $check_category = $stock_category->fetch($category['$_MASTERID']);

            if(is_array($check_category) && count($check_category) > 0) {
                if($check_category['alter_id'] != $category['$Alterid']) {
                    $update_response = $stock_category->update($data, $category['$_MASTERID']);
                }
            } else {
                $new_data[] = $data;
            }
        } 

        $add_response = $stock_category->add($new_data);

        if($update_response) {
            $message = array(
                'type' => 'success',
                'message' => 'Stock categories updated successfully!'
            ); 
            echo json_encode($message);
        } elseif($add_response) {
            $message = array(
                'type' => 'success',
                'message' => 'Stock categories created successfully!'
            );
            echo json_encode($message);
        } else{
            $message = array(
                'type' => 'warning',
                'message' => 'All data already present!'
            );
            echo json_encode($message); 
        }
    } catch (Exception $e) {
        $message = array(
            'type' => 'error',
            'message' => $e->getMessage()
        );
        echo json_encode($message); 

    }
}

$f = $_GET['f'];
$f();

