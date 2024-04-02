<?php
include('../layouts/header.php');
require('../database/connection.php');
require('../models/StockGroup.php');


$database = new Database();
$db = $database->connect();
$group = new StockGroup($db);

$group_id = $_GET['id'];


$details = $group->fetchStockGroups($group_id);
$parents = $group->fetchParent();
//echo json_encode($details);
//exit;




?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="card-title">Edit Group</p>
                        <div>
                            <button type="button" onclick="event.preventDefault(); window.history.back();" class="btn btn-secondary btn-sm btn-icon-text"><i class="ti-arrow-left btn-icon-prepend"></i> Back</button>
                            <a href="view-group.php" class="btn btn-primary btn-sm btn-icon-text"><i class="ti-list btn-icon-prepend"></i> Group Lists</a>
                        </div>
                    </div>
                    <h4 class="text-center">
                        <?php
                        if (isset($_SESSION['message'])) {
                            echo $_SESSION['message'];
                            unset($_SESSION['message']);
                        } ?>
                    </h4>
                    <form action="../controllers/StockGroupController.php?f=updateGroup" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="group_name">Group Name</label>
                                <input type="hidden" class="form-control" name="group_id" id="group_id" value="<?php echo $details[0]['id'] ?>" />
                                <input type="text" class="form-control" name="group_name" id="group_name" value="<?php echo $details[0]['name']; ?>" placeholder="Group Name" />
                            </div>

                            <div class="form-group col-md-6 col-lg-4">
                                <label for="alias_name">Alias</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="alias_name" id="alias_name" value="<?php echo $details[0]['alias']; ?>"placeholder="Enter alias"/>
                                    <div class="input-append px-lg-4">
                                        <button type="button" class="btn btn-info btn-sm" onclick="addAlias()">Add</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6 col-lg-4" id="table_alias">
                                <table class="table">
                                    <thead>
                                    <tr>

                                    </tr>
                                    </thead>
                                    <tbody id="aliasTable">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if(!empty($details[0]['alias1'])) {?>
                            <div id="aliases-container">
                                <label for="group_name">Aliases</label>
                                <?php foreach ($details as $alias) { ?>
                                    <div class="alias-container">
                                        <input type="text" class="custom-control" style="margin-right: 0.5rem" name="alias" id="alias" value="<?php echo $alias['alias1'] ?>" />
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>


                        <div class="row mt-4">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="group_name">Parent</label>
                                <select class="form-control" name="parent" id="parent" required>
                                    <option select disabled>Select parent ...</option>
                                    <option value="Primary">Primary</option>
                                    <?php foreach ($parents as $val){
                                        if($val['name'] == $details[0]['parent']){?>
                                            <option value="<?php echo $details[0]['parent'] ?>" selected><?php echo $details[0]['parent'] ?></option>
                                        <?php }else{ ?>
                                            <option value="<?php echo $val['name'] ?>"><?php echo $val['name'] ?></option>
                                        <?php }} ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group float-right">
                            <button type="reset" class="btn btn-warning">Reset</button>
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div <?php include('../layouts/footer.php'); ?>
<style>
    #table_alias {
        width: fit-content;
        height: 100px;
        overflow-y: auto;
        scroll-behavior: smooth;
    }
    #aliases-container {
        display: flex;
        flex-wrap: wrap;
    }

    .alias-container {
        display: inline-block;
        margin-right: 10px; /* Adjust margin as needed */
    }
    ul {
        list-style: none;
        line-height:1rem;
    }
    ul li {
        margin-bottom: 0.3rem !important;
    }
    ul li::before {
        margin-right: 0.5rem;
        color: #ff6f00;
    }
</style>
<script>
    let aliasCount = 0;
    let addedAliases = [];
    function addAlias(){
        const aliasInput = document.getElementById('alias_name').value.trim();
        if (aliasInput !== '' && !addedAliases.includes(aliasInput)) {
            aliasCount++;
            addedAliases.push(aliasInput);
            const tableBody = document.getElementById('aliasTable');
            const newRow = document.createElement('ul');
            // newRow.setAttribute(border, 'none');
            newRow.innerHTML = `
                <li>(${aliasCount}) <input type="text" style="width: 100px;border: none;" name="alias[]" id="alias" value="${aliasInput}">
                    <span type="button" onclick="removeAlias(this)">x</span>
                </li>
            `;
            tableBody.appendChild(newRow);
            document.getElementById('alias_name').value="";
        }else{
            alert("Duplicate Entry!");
        }
    }
    function removeAlias(button) {
        const row = button.closest('tr');
        row.remove();
    }
    $(document).ready(function (){
        $('#group_name, #alias_name').on('blur', function(){
            var field = $(this); // Get the current field
            var value = field.val(); // Get the entered value
            var fieldName = field.attr('name');

            $.ajax({
                type: 'POST',
                url: '../controllers/StockGroupController.php?f=checkGroupAndAliasDuplicates',
                data: {
                    group_name: value,
                    alias_name: value,
                    group_id: value
                },
                dataType: 'json',
                success: function(response) {
                    if (response.exists) {
                        alert('The entered value already exists as either ' + fieldName + ' or alias.');
                        $(field).val("");
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Error occurred while checking group and alias.');
                }
            });
        });
    });
</script>
