<?php
session_start();
error_reporting(E_ALL & ~ E_NOTICE);
require ('../database/connection.php');
require ('../database/odbc_connection.php');
require ('../models/StockUnit.php');

$database = new Database();
$db = $database->connect();
$stockunits = new StockUnit($db);

$dbconfig = new ODBCDatabase();
$odbc = $dbconfig->connect();
$tally_stockunits = new StockUnit($odbc);

function create()
{
    global $stockunits;
    global $tally_stockunits;
    $response = $tally_stockunits->loadData();
    $update_response = false;
    $new_data = [];
    
    try {

        foreach($response as $key => $unit){
            
            $data = array(
                'master_id' => $unit['$_MASTERID'],
                'name' => str_replace("'", "''", $unit['$_Name']),
                'original_name' => $unit['$OriginalName'],
                'is_simple_unit' => $unit['$IsSimpleUnit'],
                'base_units' => $unit['$BaseUnits'],
                'additional_units' => $unit['$AdditionalUnits'],
                'conversion' => $unit['$Conversion'],
                'decimal_places' => $unit['$DecimalPlaces'],
                'alter_id' => $unit['$Alterid'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            );

            $check_unit = $stockunits->fetch($unit['$_MASTERID']);
            if(is_array($check_unit) && count($check_unit) > 0){
                if($check_unit['alter_id'] != $unit['$Alterid']) {
                    $update_response = $stockunits->update($data, $unit['$_MASTERID']);
                }
            }else {
                $new_data[] = $data;
            }
        }

        $add_response = $stockunits->add($new_data);

        if($update_response) {
            $message = array(
                'type' => 'success',
                'message' => 'Stock units updated successfully!'
            ); 
            echo json_encode($message);
        } elseif($add_response) {
            $message = array(
                'type' => 'success',
                'message' => 'Stock units created successfully!'
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