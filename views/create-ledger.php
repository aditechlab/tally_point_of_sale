<?php
include('../layouts/header.php');
require('../database/connection.php');
require('../models/Ledger.php');
require('../models/StockGroup.php');

$database = new Database();
$db = $database->connect();
$ledger = new Ledger($db);

$group = new StockGroup($db);

$parents = $group->fetchParent();




?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="card-title">Create Ledger</p>
                        <div class="">
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
                    <form id="addGroup" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="ledger_name">Ledger Name</label>
                                <input type="text" class="form-control" name="ledger_name" id="ledger_name" placeholder="Ledger Name" required="required"/>
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Contact Name</label>
                                <input type="text" class="form-control" name="contact_name" id="contact_name" placeholder="Enter contact name">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Address</label>
                                <input type="text" class="form-control" name="address" id="address" placeholder="Enter address">
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="alias_name">Alias</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="alias_name" id="alias_name" placeholder="Enter alias"/>
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
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Address1</label>
                                <input type="text" class="form-control" name="address1" id="address1" placeholder="Enter address">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Address2</label>
                                <input type="text" class="form-control" name="address2" id="address2" placeholder="Enter address">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="parent">Parent</label>
                                <select class="form-control" name="parent" id="parent" required>
                                    <option value="">Select parent ...</option>
                                    <?php foreach ($parents as $val){ ?>
                                        <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                    <?php } ?>
                                </select>
                                <label><span class="badge badge-info" id="parent_label"></span> </label>
                            </div>

                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Address3</label>
                                <input type="text" class="form-control" name="address3" id="address3" placeholder="Enter address">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Primary Phone No</label>
                                <input type="text" class="form-control" name="primary_phone_no" id="primary_phone_no" placeholder="Enter address">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Phone No</label>
                                <input type="text" class="form-control" name="phone_no" id="phone_no" placeholder="Enter address">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">Email Address</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Enter address">
                            </div>
                            <div class="form-group col-md-6 col-lg-4 contact_details">
                                <label for="parent">CC Email</label>
                                <input type="email" class="form-control" name="ccemail" id="ccemail" placeholder="Enter address">
                            </div>
                        </div>
                        <div class="row sundry" id="sundry" style="display: none;">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="parent">Pay by Bill</label>
                                <select class="form-control" name="pay_by_bill" id="pay_by_bill">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="parent">Default credit period(Days)</label>
                                <input type="number" class="form-control" name="credit_period" id="credit_period" placeholder="Enter credit period in days">
                            </div>
                        </div>
                        <div class="bank-details" style="display: none">
                            <h3 class="text-center">Bank Account Details</h3>
                            <div class="row">
                                <div class="form-group col-md-6 col-lg-4">
                                    <label for="parent">A/c Holder name</label>
                                    <input type="text" class="form-control" name="account_name" id="account_name" placeholder="Enter Account holder name">
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label for="parent">A/c No</label>
                                    <input type="text" class="form-control" name="account_no" id="account_no" placeholder="Enter Account number">
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label for="parent">Bank Code</label>
                                    <input type="text" class="form-control" name="bank_code" id="bank_code" placeholder="Enter Bank code">
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label for="parent">Bank Name</label>
                                    <select class="form-control" name="bank_name" id="bank_name">
                                        <option value="">List of Banks</option>
                                        <option value="ABC">ABC</option>
                                        <option value="DTB">DTB</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label for="parent">Bank Branch</label>
                                    <input type="text" class="form-control" name="branch" id="branch" placeholder="Enter branch">
                                </div>
                            </div>
                        </div>
                        <div class="form-group float-right">
                            <button type="reset" class="btn btn-warning">Reset</button>
                            <button type="submit" id="submit" class="btn btn-success">Save</button>
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
    .contact_details {
        display: none;
    }

</style>
<script>
    $(document).ready( function () {
        /*-------------    Submit  data using javascript	   --------------------*/
        $("#submit").click(function(){
            var a = $("span").hasClass("invalid");

            if(a == true){
                swal({
                    text: "Please Enter Valid Inputs",
                    icon: "warning",
                    dangerMode: true,
                });
                return false;
            }
            else{

                document.getElementById('addGroup').action ="../controllers/LedgerController.php?f=createLedger";

            }
        });
    });
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
            alert("Duplicate entry!");
        }
    }
    function removeAlias(button) {
        const row = button.closest('li');
        row.remove();
    }
    $(document).ready(function(){
        $('#ledger_name, #alias_name').on('blur', function(){
            var field = $(this);
            var value = field.val();
            var fieldName = field.attr('name');

            $.ajax({
                type: 'POST',
                url: '../controllers/LedgerController.php?f=checkGroupAndAlias',
                data: {
                    ledger_name: value,
                    alias_name: value
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

</script>
