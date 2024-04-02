<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require('../database/connection.php');
require('../database/odbc_connection.php');
require('../models/StockItem.php');
$database = new Database();
$db = $database->connect();
$stock_item = new StockItem($db);

$dbconfig = new ODBCDatabase();
$odbc = $dbconfig->connect();
$tally_stock =  new StockItem($odbc);

function create()
{
    global $stock_item;
    global $tally_stock;

    try {

        $items = $tally_stock->loadData();
        $message = array(
            'type' => 'success',
            'message' => 'Stock categories created successfully!'
        );
        echo json_encode($message);
        exit;

        foreach($items as $item) {

            $data = array(
                'master_id' => $item['$_MASTERID'],
                'name' => '',
                'alias' => $item['$_Alias'],
                'alias1' => $item['$_Alias1'],
                'parent_master_id' => $item['$_ParentMasterID'],
                'parent' => $item['$Parent'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            );

            $check_item = $stock_item->fetch($item['$_MASTERID']);
            if($check_item->rowCount() > 0) {
                $items = $stock_item->update($data, $item['$_MASTERID']);
            } else {
                $items = $stock_item->add($data);
            }

            if($items) {
                    $_SESSION['message'] = '<div class="alert alert-success">Stock items created successfully</div>';
                    ?>
                    <script>
                        window.history.back();
                    </script>
                    <?php
            } else{
                $_SESSION['message']='<div class="alert alert-danger">Problem in stock items creation!</div>'; ?>
                <script>
                    window.history.back();
                </script>
                <?php
            }
        } 
    } catch (Exception $e) {
        $_SESSION['message'] = '<div class="alert alert-danger">'.$e->getMessage().'</div>'; 
        ?>
        <script>
            window.history.back();
        </script>
        <?php
    }
}

$f = $_GET['f'];
$f();