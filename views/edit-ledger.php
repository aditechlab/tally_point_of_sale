<?php
include('../layouts/header.php');
require('../database/connection.php');
require('../models/Ledger.php');
require('../models/StockGroup.php');


$database = new Database();
$db = $database->connect();
$ledger = new Ledger($db);

$group_id = $_GET['id'];



$details = $ledger->fetchStockGroups($group_id);

$group = new StockGroup($db);
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
                        <p class="card-title">Edit Ledger</p>
                        <div>
                            <button type="button" onclick="event.preventDefault(); window.history.back();" class="btn btn-secondary btn-sm btn-icon-text"><i class="ti-arrow-left btn-icon-prepend"></i> Back</button>
                            <a href="view-ledger.php" class="btn btn-primary btn-sm btn-icon-text"><i class="ti-list btn-icon-prepend"></i> Ledger Lists</a>
                        </div>
                    </div>
                    <h4 class="text-center">
                        <?php
                        if (isset($_SESSION['message'])) {
                            echo $_SESSION['message'];
                            unset($_SESSION['message']);
                        } ?>
                    </h4>
                    <form action="../controllers/LedgerController.php?f=updateLedger" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="ledger_name">Group Name</label>
                                <input type="hidden" class="form-control" name="group_id" id="group_id" value="<?php echo $details[0]['id'] ?>" />
                                <input type="text" class="form-control" name="ledger_name" id="ledger_name" value="<?php echo $details[0]['ledger_name']; ?>" placeholder="Group Name" />
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
                                <label for="ledger_name">Parent</label>
                                <select class="form-control" name="parent" id="parent" required>
                                    <option select disabled>Select parent ...</option>
                                    <?php foreach ($parents as $val){
                                        if($val['id'] == $details[0]['parent']){?>
                                            <option value="<?php echo $val['id'] ?>" selected><?php echo $val['name'] ?></option>
                                        <?php }else{ ?>
                                            <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                        <?php }} ?>

                                </select>
                                <label><span class="badge badge-info" id="parent_label"></span> </label>
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Contact Name</label>
                                <input type="text" class="form-control" name="contact_name" id="contact_name" value="<?php echo $details[0]['contact_name']; ?>" placeholder="Enter contact name">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Address</label>
                                <input type="text" class="form-control" name="address" id="address" value="<?php echo $details[0]['address']; ?>" placeholder="Enter address">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Address1</label>
                                <input type="text" class="form-control" name="address1" id="address1" value="<?php echo $details[0]['address1']; ?>" placeholder="Enter address">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Address2</label>
                                <input type="text" class="form-control" name="address2" id="address2" value="<?php echo $details[0]['address2']; ?>" placeholder="Enter address">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Address3</label>
                                <input type="text" class="form-control" name="address3" id="address3" value="<?php echo $details[0]['address3']; ?>" placeholder="Enter address">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Primary Phone No</label>
                                <input type="text" class="form-control" name="primary_phone_no" id="primary_phone_no" value="<?php echo $details[0]['primary_phone_number']; ?>" placeholder="Enter address">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Phone No</label>
                                <input type="text" class="form-control" name="phone_no" id="phone_no" value="<?php echo $details[0]['phone_number']; ?>" placeholder="Enter address">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Email Address</label>
                                <input type="email" class="form-control" name="email" id="email" value="<?php echo $details[0]['email']; ?>" placeholder="Enter address">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">CC Email</label>
                                <input type="email" class="form-control" name="ccemail" id="ccemail" value="<?php echo $details[0]['ccemail']; ?>" placeholder="Enter address">
                            </div>
                        </div>
                        <div class="row sundry" id="sundry" style="display: none">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="parent">Pay by Bill</label>
                                <select class="form-control" name="pay_by_bill" id="pay_by_bill">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="parent">Default credit period(Days)</label>
                                <input type="number" class="form-control" name="credit_period" id="credit_period" <?php echo $details[0]['credit_period']; ?> placeholder="Enter credit period in days">
                            </div>
                        </div>
                        <div class="bank-details" style="display: none">
                            <h3 class="text-center">Bank Account Details</h3>
                            <div class="row">
                                <div class="form-group col-md-6 col-lg-4">
                                    <label for="parent">A/c Holder name</label>
                                    <input type="text" class="form-control" name="account_name" id="account_name" value="<?php echo $details[0]['account_name']; ?>"  placeholder="Enter Account holder name">
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label for="parent">A/c No</label>
                                    <input type="text" class="form-control" name="account_no" id="account_no" value="<?php echo $details[0]['account_number']; ?>" placeholder="Enter Account number">
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label for="parent">Bank Code</label>
                                    <input type="text" class="form-control" name="bank_code" id="bank_code" value="<?php echo $details[0]['bank_code']; ?>" placeholder="Enter Bank code">
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label for="parent">Bank Name</label>
                                    <select class="form-control" name="bank_name" id="bank_name">
                                        <option value="">List of Banks</option>
                                        <option value="ABC" <?php echo ("ABC" == $details[0]['bank_name']) ? 'selected' : ''?>>ABC</option>
                                        <option value="DTB" <?php echo ("DTB" == $details[0]['bank_name']) ? 'selected' : ''?>>DTB</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label for="parent">Bank Branch</label>
                                    <input type="text" class="form-control" name="branch" id="branch" value="<?php echo $details[0]['branch']; ?>" placeholder="Enter branch">
                                </div>
                            </div>
                        </div>
                        <div class="form-group float-right">
                            <button type="reset" class="btn btn-warning">Reset</button>
                            <button type="submit" class="btn btn-success">Update</button>
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
    .contact_details {
        display: none;
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
            if(item.Id !== null) {
                const listItem = document.createElement('ul');
                listItem.innerHTML = `

                    <li>(${index + 1})<input type="hidden" name="alias_id[]" value="${item.Id}" id="alias_id_${index}">
                        <input type="text"  style="width: 100px;border: transparent;" name="alias[]" value="${item.alias1}" id="alias_v_${index}" data-index="${index}" readonly >
                        <span type="button" onclick="removeAlias(this)">x</span>
                   </li>
                `;

                aliasesList.appendChild(listItem);
            }
            const editInput = document.getElementById(`alias_v_${index}`);
            editInput.addEventListener('click', () => editAlias(editInput));

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

        if (aliasInput !== '' && !addedAliases.some(alias => alias.alias1.toLowerCase() === aliasInput)) { // Case-insensitive duplicate check
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

    window.onload = displayAliases;
    function removeAlias(button) {
        const listItem = button.closest('li');
        const aliasId = parseInt(listItem.querySelector('input[name="alias_id[]"]').value);

        if (aliasId !== 0) {
            $.ajax({
                url: '../controllers/LedgerController.php?f=deleteAlias',
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
        $('#ledger_name, #alias_name').on('change', function(){
            var field = $(this); // Get the current field
            var value = field.val() //current field value
            var fieldName = field.attr('name');
            console.log(value)

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
            var group = $(this).val()
            $.ajax({
                type: 'POST',
                url: '../controllers/LedgerController.php?f=getParent',
                data: {
                    parent: group,
                },
                dataType: 'json',
                success: function(response) {
                    if(response){
                        $('#parent_label').text(response)
                    }
                }
            });
        });

        $('#ledger_name').on('change', function(){
            var field = $(this);
            var value = field.val();
            $("#contact_name").val(value)
        });

        $("#parent").on('change', function (){
            let parent = $(this).val();
            console.log(parent)
            $.ajax({
                type: 'POST',
                url: '../controllers/LedgerController.php?f=viewGroups',
                data: {
                    parent: parent,
                },
                dataType: 'json',
                success: function(data) {
                    if(data && data.length > 0){
                        let groups = data.map(item => item.name); // Extract group names
                        console.log(groups)
                        let sundry = ["Sundry Creditors", "Sundry Debtors", "Branch / Divisions"];
                        let banks = ["Bank Accounts","Bank ODC A/c"]
                        if(groups.some(group => sundry.includes(group))){
                            $("#sundry").show();
                            $(".bank-details").hide();
                            $(".contact_details").hide();
                        } else if(groups.some(group => banks.includes(group))){
                            $(".bank-details").show();
                            $("#sundry").hide();
                        }else if(!groups.some(group => banks.includes(group) && sundry.includes(group))){
                            $(".contact_details").show();
                            $("#sundry").hide();
                            $(".bank-details").hide();
                        }
                        else {
                            $("#sundry").hide();
                            $(".bank-details").hide();
                            $(".contact_details").hide();
                        }
                        $('#parent_label').text(groups.parent)
                    } else {
                        $("#sundry").hide();
                        $(".bank-details").hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Error occurred while checking parents');
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
            url: '../controllers/LedgerController.php?f=checkLedgerAliasData',
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
