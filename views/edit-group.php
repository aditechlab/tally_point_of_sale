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
                    <div class="d-flex">
                        <p class="card-title">Edit Group</p>
                        <div style="margin-left: 180px">
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
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="alias_name">Alias</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="alias_name" id="alias_name" value="" placeholder="Enter alias"/>
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
    function displayAliases() {
        const aliasesList = document.getElementById('aliasTable');
        aliasesList.innerHTML = '';

        addedAliases.forEach((item, index) => {
            if (item.Id !== null) {
                console.log(item.Id)
                const listItem = document.createElement('ul');
                listItem.innerHTML = `

                <li>(${index + 1})<input type="hidden" name="alias_id[]" value="${item.Id}" id="alias_id_${index}">
                    <input type="text"  style="width: 100px;border: transparent;" name="alias[]" value="${item.alias1}" id="alias_v_${index}" data-index="${index}" readonly>
                    <span type="button" onclick="removeAlias(this)">x</span>
               </li>
            `;

                aliasesList.appendChild(listItem);

                const editInput = document.getElementById(`alias_v_${index}`);
                editInput.addEventListener('click', () => editAlias(editInput));
            }
        });
    }

    function editAlias(input) {
        const index = parseInt(input.getAttribute('data-index'));
        const aliasValue = addedAliases[index].alias1;
        document.getElementById('alias_name').value = aliasValue;
        document.getElementById('alias_name').setAttribute('data-index', index);
    }

    function addAlias() {
        const aliasInput = document.getElementById('alias_name').value.trim();
        const storedIndex = parseInt(document.getElementById('alias_name').getAttribute('data-index'));

        if (aliasInput !== '' && !addedAliases.includes(aliasInput)) {
            if (!isNaN(storedIndex) && storedIndex >= 0 && storedIndex < addedAliases.length) {
                // Update existing alias
                addedAliases[storedIndex].alias1 = aliasInput;
            } else {
                // Add a new alias
                const newAlias = { alias1: aliasInput };
                addedAliases.push(newAlias);
            }
            displayAliases(); // Refresh the displayed list
            document.getElementById('alias_name').value = "";
            document.getElementById('alias_name').removeAttribute('data-index'); // Clear stored index
        } else {
            alert("Duplicate Entry!");
        }
    }

    // function addAlias() {
    //     const aliasInput = document.getElementById('alias_name').value.trim();
    //     const storedIndex = parseInt(document.getElementById('alias_name').getAttribute('data-index'));
    //
    //     if (aliasInput !== '') {
    //         if (storedIndex !== -1) {
    //             // Update existing alias
    //             addedAliases[storedIndex].alias1 = aliasInput;
    //             displayAliases(); // Refresh the displayed list
    //         } else if (!addedAliases.includes(aliasInput)) {
    //             // Add a new alias
    //             const newAlias = { alias1: aliasInput };
    //             addedAliases.push(newAlias);
    //             displayAliases();
    //         } else {
    //             alert("Duplicate Entry!");
    //         }
    //
    //         document.getElementById('alias_name').value = "";
    //         document.getElementById('alias_name').removeAttribute('data-index'); // Clear stored index
    //     }
    // }


    window.onload = displayAliases;
    function removeAlias(button) {
        const listItem = button.closest('li');
        const aliasId = parseInt(listItem.querySelector('input[name="alias_id[]"]').value);

        if (aliasId !== 0) {
            $.ajax({
                url: '../controllers/StockGroupController.php?f=deleteAlias',
                method: 'POST',
                data: { alias_id: aliasId },
                success: function(response) {
                    if (response) {
                        listItem.remove();
                        alert("Alias removed successfully!");
                    } else {
                        alert("Failed to remove alias. Please try again.");
                    }
                },
                error: function() {
                    alert("An error occurred while removing the alias.");
                }
            });
        } else {
            listItem.remove();
        }
    }

    $(document).ready(function (){
        $('#group_name, #alias_name').on('change', function(){
            var field = $(this); // Get the current field
            var value = field.val(); // Get the entered value
            var fieldName = field.attr('name');
            console.log(fieldName)

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
                    console.log(response)
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
                url: '../controllers/StockGroupController.php?f=getParent',
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

    function  checkDuplicates(index){
        var field = document.getElementById(`alias_v_${index}`);
        var value = field.value.trim();
        var data = { alias: value, editedIndex: index };
        console.log(value)

        $.ajax({
            type: 'POST',
            url: '../controllers/StockGroupController.php?f=checkGroupAndAliasData',
            data: data,
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
