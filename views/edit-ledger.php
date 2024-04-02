<?php
include('../layouts/header.php');
require('../database/connection.php');
require('../models/Ledger.php');

$database = new Database();
$db = $database->connect();
$group = new Ledger($db);

$parents = $group->fetchParent();
$details = $group->fetch($_GET['id']);
echo json_encode($details);
exit;


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
                                <input type="text" class="form-control" name="group_name" id="group_name" value="<?php echo $details[0]['name'] ?>" placeholder="Group Name" />
                            </div>

                            <div class="form-group col-md-6 col-lg-4">
                                <label for="group_name">Aliases</label>
                                <?php foreach ($details as $alias) { ?>
                                    <input type="text" class="custom-control" name="alias1" id="alias1" value="<?php echo $details['alias1'] ?>" />
                                <?php } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="group_name">Parent</label>
                                <select class="form-control" name="parent" id="parent">
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
</style>
<script>
    let aliasCount = 0;
    function addAlias(){
        const aliasInput = document.getElementById('alias_name').value.trim();
        if (aliasInput !== '') {
            aliasCount++;
            const tableBody = document.getElementById('aliasTable');
            const newRow = document.createElement('tr');
            // newRow.setAttribute(border, 'none');
            newRow.innerHTML = `
                <td><input type="text" name="alias[]" id="alias" value="${aliasInput}"></td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeAlias(this)"><i class="mdi mdi-delete"></i></button>
                </td>
            `;
            tableBody.appendChild(newRow);
            document.getElementById('alias_name').value="";
        }
    }
    function removeAlias(button) {
        const row = button.closest('tr');
        row.remove();
    }
    $(document).ready(function (){
        $('#group_name, #alias1').on('blur', function(){
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
