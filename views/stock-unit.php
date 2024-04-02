<?php
include('../layouts/header.php');
require('../database/connection.php');
require('../models/StockUnit.php');

$database = new Database();
$db = $database->connect();

$stocks = new StockUnit($db);
$StockUnits = $stocks->all();


?>


<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="card-title">Stock Units</p>
                        <div>
                            <button type="button" onclick="event.preventDefault(); window.history.back();" class="btn btn-secondary btn-sm btn-icon-text"><i class="ti-arrow-left btn-icon-prepend"></i> Back</button>
                            <a href="../controllers/StockUnitController.php?f=create" class="btn btn-primary btn-sm btn-icon-text"><i class="ti-reload btn-icon-prepend"></i> Refresh</a>
                        </div>
                    </div>
                    <h4 class="text-center">
                        <?php
                        if (isset($_SESSION['message'])) {
                            echo $_SESSION['message'];
                            unset($_SESSION['message']);
                        } ?>
                    </h4>
                    <div class="table-responsive">
                        <table class="display expandable-table w-100" id="example1">
                            <thead>
                                <th>Sr No</th>
                                <th>Original Name</th>
                                <th>Name</th>
                                <th>Conversion</th>
                                <th>Is Simple Unit</th>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                foreach ($StockUnits as $key => $values) { ?>
                                    <tr>
                                        <td><?php echo $i++ ?></td>
                                        <td><?php echo $values['original_name'] ?></td>
                                        <td><?php echo $values['name'] ?></td>
                                        <td><?php echo $values['conversion'] ?></td>
                                        <td><?php echo $values['is_simple_unit'] ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div <?php include('../layouts/footer.php'); ?>