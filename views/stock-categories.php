<?php
error_reporting(0);
include('../layouts/header.php');
require('../database/connection.php');
require('../models/StockCategory.php');
$database = new Database();
$db = $database->connect();
$categories = new StockCategory($db);

$stock_categories = $categories->all();

?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="card-title">Stock Categories</p>
                        <div>
                            <button type="button" onclick="event.preventDefault(); window.history.back();" class="btn btn-secondary btn-sm btn-icon-text"><i class="ti-arrow-left btn-icon-prepend"></i> Back</button>
                            <a href="../controllers/StockCategoryController.php?f=create" class="btn btn-primary btn-sm btn-icon-text"><i class="ti-reload btn-icon-prepend"></i> Refresh</a>
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
                                <table id="example1" class="display expandable-table w-100" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Sr No</th>
                                            <th>Name</th>
                                            <th>Alias</th>
                                            <th>Alias1</th>
                                            <th>Parent</th>
                                            <th>Number of Month To Delivery</th>
                                            <th>Minimum Closing %</th>
                                            <th>Cost Center</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($stock_categories as $key=>$category) { ?>
                                            <tr>
                                                <td><?php echo $key + 1 ?></td>
                                                <td><?php echo $category['name'] ?></td>
                                                <td><?php echo $category['alias'] ?></td>
                                                <td><?php echo $category['alias1'] ?></td>
                                                <td><?php echo $category['parent'] ?></td>
                                                <td><?php echo $category['number_of_month'] ?></td>
                                                <td><?php echo $category['minimum_closing_percent'] ?></td>
                                                <td><?php echo $category['cost_center'] ?></td>
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