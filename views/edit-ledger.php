<?php
include('../layouts/header.php');
require('../database/connection.php');
require('../models/Ledger.php');


$database = new Database();
$db = $database->connect();
$group = new Ledger($db);

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
                    <div class="d-flex">
                        <p class="card-title">Edit Ledger</p>
                        <div style="margin-left: 180px">
                            <button type="button" onclick="event.preventDefault(); window.history.back();" class="btn btn-secondary btn-sm btn-icon-text"><i class="ti-arrow-left btn-icon-prepend"></i> Back</button>
                            <a href="view-ledger.php" class="btn btn-primary btn-sm btn-icon-text"><i class="ti-list btn-icon-prepend"></i> Group Lists</a>
                        </div>
                    </div>
                    <h4 class="text-center">
                        <?php
                        if (isset($_SESSION['message'])) {
                            echo $_SESSION['message'];
                            unset($_SESSION['message']);
                        } ?>
                    </h4>
                    <form action="../controllers/LedgerController.php?f=updateGroup" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="ledger_name">Group Name</label>
                                <input type="hidden" class="form-control" name="group_id" id="group_id" value="<?php echo $details[0]['id'] ?>" />
                                <input type="text" class="form-control" name="ledger_name" id="ledger_name" value="<?php echo $details[0]['name']; ?>" placeholder="Group Name" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="alias_name">Alias</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="alias_name" id="alias_name" value="<?php echo $details[0]['alias']; ?>"placeholder="Enter alias"/>
                                </div>
                            </div>
                            <div class="input-append mt-4 py-2">
                                <button type="button" class="btn btn-info btn-sm" onclick="addAlias()">Add</button>
                            </div>
                        </div>
                        <div class="row">
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
                        <div class="row mt-4">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="ledger_name">Parent</label>
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
                                <label><span class="badge badge-info" id="parent_label"></span> </label>
                            </div>
                        </div>
                        <div class="form-group">
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
        height: 150px;
        overflow-y: auto;
        scroll-behavior: smooth;
        /*border: 1px solid;*/
        border-radius: 20px;
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
        line-height: 0.1 !important;
    }
    ul li::before {
        margin-right: 0.5rem;
        color: #ff6f00;
    }
    li > input {
        height: 1rem;
    }
</style>
<script>

    let aliasCount = <?php echo count($details); ?>;
    let addedAliases = <?php echo json_encode(array_values($details)); ?>;
    console.log(addedAliases);
    function displayAliases() {
        const aliasesList = document.getElementById('aliasTable');
        aliasesList.innerHTML = '';
        addedAliases.forEach((item, index) => {
            const listItem = document.createElement('ul');
            listItem.innerHTML = `
                <li>(${index + 1}) <input type="hidden" style="width: 100px;border: transparent;" name="alias_id[]" id="alias_id" value="${item.Id}">
                <input type="text" style="width: 100px;border: transparent;" name="alias[]" id="alias_v" onblur="checkDuplicates()" value="${item.alias1}">
                     <span type="button" onclick="removeAlias(this)">x</span>
                </li>
            `;
            aliasesList.appendChild(listItem);
        });
    }
    function addAlias(){
        const aliasInput = document.getElementById('alias_name').value.trim();
        if (aliasInput !== '' && !addedAliases.includes(aliasInput)) {
            const newAlias = { alias1: aliasInput };
            addedAliases.push(newAlias);
            displayAliases(); // Call to update the displayed list
            document.getElementById('alias_name').value="";
        } else {
            alert("Duplicate Entry!");
        }
    }

    window.onload = displayAliases;
    function removeAlias(button) {
        const row = button.closest('li');
        row.remove();
    }

    $(document).ready(function (){
        $('#ledger_name, #alias_name').on('blur', function(){
            var field = $(this); // Get the current field
            var value = field.val(); // Get the entered value
            var fieldName = field.attr('name');
            console.log(fieldName)

            $.ajax({
                type: 'POST',
                url: '../controllers/LedgerController.php?f=checkGroupAndAliasDuplicates',
                data: {
                    ledger_name: value,
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

        $("#parent").on('change', function (){
            var group = $(this).val();
            console.log(group)
            $.ajax({
                type: 'POST',
                url: '../controllers/LedgerController.php?f=getParent',
                data: {
                    parent: group,
                },
                dataType: 'json',
                success: function(response) {
                    if(response){
                        response.forEach((item, index) => {
                            $('#parent_label').text(item.parent)
                        });
                    }
                }
            });
        });
    });

    function  checkDuplicates(){
        var field = $("#alias_v");
        var value = field.val();
        var fieldName = field.attr('name');
        console.log(fieldName)

        $.ajax({
            type: 'POST',
            url: '../controllers/LedgerController.php?f=checkLedgerAliasData',
            data: {
                alias: value,
            },
            dataType: 'json',
            success: function(response) {
                if (response.exists) {
                    alert('Alias entered already exists');
                    $(field).val("");
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('Error occurred while checking group and alias.');
            }
        });
    }
</script>
