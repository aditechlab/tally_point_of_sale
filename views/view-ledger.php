<?php
include('../layouts/header.php');
require('../database/connection.php');
require('../models/Ledger.php');

$database = new Database();
$db = $database->connect();

$stocks = new Ledger($db);
$ledgers = $stocks->all();


?>


    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <p class="card-title">List of Ledgers</p>
                            <div>
                                <button type="button" onclick="event.preventDefault(); window.history.back();" class="btn btn-secondary btn-sm btn-icon-text"><i class="ti-arrow-left btn-icon-prepend"></i> Back</button>
                                <!--                            <a href="../controllers/StockGroupController.php?f=insertStockGroup" class="btn btn-primary btn-sm btn-icon-text"><i class="ti-reload btn-icon-prepend"></i> Refresh</a>-->
                                <a href="create-ledger.php" class="btn btn-primary btn-sm btn-icon-text"><i class="ti-reload btn-icon-prepend"></i> Create</a>
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
                                <th>Name</th>
                                <th>Parent</th>
                                <th>Action</th>
                                </thead>
                                <tbody>
                                <?php $i = 1;
                                foreach ($ledgers as $key => $values) { ?>
                                    <tr>
                                        <td><?php echo $i++ ?></td>
                                        <td><?php echo $values['name'] ?></td>
                                        <td><?php echo $values['parent'] ?></td>
                                        <td>
                                            <a href="edit-ledger.php?id=<?php echo $values['id'];?>" class="btn btn-info btn-sm"><i class="ti-pencil-alt btn-icon-prepend"></i>Alter </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div <?php include('../layouts/footer.php'); ?><?php
