<?php
error_reporting(0);
include('../layouts/header.php');
require('../database/connection.php');
require('../models/StockItem.php');
$database = new Database();
$db = $database->connect();
$item = new StockItem($db);

$stock_items = $item->all();

?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="card-title">Stock Items</p>
                        <div>
                            <button type="button" onclick="event.preventDefault(); window.history.back();" class="btn btn-secondary btn-sm btn-icon-text"><i class="ti-arrow-left btn-icon-prepend"></i> Back</button>
                            <a href="../controllers/StockItemController.php?f=create" class="btn btn-primary btn-sm btn-icon-text"><i class="ti-reload btn-icon-prepend"></i> Refresh</a>
                        </div>
                    </div>
                    <h4 class="text-center">
                        <?php
                        if (isset($_SESSION['message'])) {
                            echo $_SESSION['message'];
                            unset($_SESSION['message']);
                        } ?>
                    </h4>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="example1" class="display expandable-table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Item Name</th>
                                            <th>Alias Name</th>
                                            <th>Part No.</th>
                                            <th>Description</th>
                                            <th>Remark</th>
                                            <th>Parent</th>
                                            <th>Category</th>
                                            <th>Brand</th>
                                            <th>Weight</th>
                                            <th>Voluam</th>
                                            <th>Unit</th>
                                            <th>Alt. Unit</th>
                                            <th>Conversion</th>
                                            <th>Denominator</th>
                                            <th>Batch Y/N</th>
                                            <th>Mfg Date</th>
                                            <th>Expiry</th>
                                            <th>Godown</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($stock_items as $key=>$item) { ?>
                                            <tr>
                                                <td><?php echo $key + 1 ?></td>
                                                <td><?php echo $item['master_id'] ?></td>
                                                <td><?php echo $item['name'] ?></td>
                                                <td><?php echo $item['alias'] ?></td>
                                                <td><?php echo $item['alias1'] ?></td>
                                                <td><?php echo $item['parent_master_id'] ?></td>
                                                <td><?php echo $item['parent'] ?></td>
                                                <td><?php echo $item['number_of_month'] ?></td>
                                                <td><?php echo $item['minimum_closing_percent'] ?></td>
                                                <td><?php echo $item['cost_center'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php
include('../layouts/footer.php');
?>