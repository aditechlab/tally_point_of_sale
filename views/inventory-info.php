<?php
include('../layouts/header.php');
require('../database/connection.php');
require('../models/StockItem.php');
require('../models/StockGroup.php');
require('../models/StockCategory.php');
require('../models/StockUnit.php');

$database = new Database();
$db = $database->connect();
$group = new StockGroup($db);
$item = new StockItem($db);
$category = new StockCategory($db);
$unit = new StockUnit($db);

$stock_groups = $group->all();
$stock_categories = $category->all();
$stock_items = $item->all();
$stock_units = $unit->all();


?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin transparent">
            <div class="row">
                <div class="col-md-3 mb-4 stretch-card transparent">
                    <div class="card card-tale">
                        <a href="view-group.php" style="text-decoration: none;">
                            <div class="card-body text-white">
                                <p class="mb-4">Stock Groups</p>
                                <p class="fs-30 mb-2"><?php echo count($stock_groups) ?></p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3 mb-4 stretch-card transparent">
                    <div class="card card-dark-blue">
                        <a href="stock-items.php" style="text-decoration: none;">
                            <div class="card-body text-white">
                                <p class="mb-4">Stock Items</p>
                                <p class="fs-30 mb-2"><?php echo count($stock_items) ?></p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 mb-4 stretch-card transparent">
                    <div class="card card-light-blue">
                        <a href="stock-categories.php" style="text-decoration: none;">
                            <div class="card-body text-white">
                                <p class="mb-4">Stock Categories</p>
                                <p class="fs-30 mb-2"><?php echo count($stock_categories) ?></p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3 mb-4 stretch-card transparent">
                    <div class="card card-light-danger">
                        <a href="stock-unit.php" style="text-decoration: none;">
                            <div class="card-body">
                                <p class="mb-4">Stock Units</p>
                                <p class="fs-30 mb-2"><?php echo count($stock_units) ?></p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include('../layouts/footer.php');
?>