<?php
include('../layouts/header.php');
require('../database/connection.php');
require('../models/Ledger.php');

$database = new Database();
$db = $database->connect();
$group = new Ledger($db);


$parents = $group->fetchParent();




?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="card-title">Create Ledger</p>
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
                    <form id="addLedger" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="group_name">Ledger Name </label>
                                <input type="text" class="form-control" name="ledger_name" id="ledger_name" placeholder="Ledger Name" required="required"/>
                            </div>
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="alias_name">Alias</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="alias_name" id="alias_name" placeholder="Enter alias"/>
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
                        <div class="row">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="group_name">Parent</label>
                                <select class="form-control" name="parent" id="parent" required>
                                    <option value="">Select parent ...</option>
                                    <?php foreach ($parents as $val){ ?>
                                        <option value="<?php echo $val['name'] ?>"><?php echo $val['name'] ?></option>
                                    <?php } ?>
                                </select>
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
        height: 100px;
        overflow-y: auto;
        scroll-behavior: smooth;
    }
    ul {
        list-style: none;
    }
    ul li {
        line-height: 0.2;
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

                document.getElementById('addLedger').action ="../controllers/LedgerController.php?f=createLedger";

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
                <li><input type="text" style="width: 100px;border: none;line-height: 0.2;" name="alias[]" id="alias" value="${aliasInput}">
                    <span type="button" class="" onclick="removeAlias(this)">x</span>
                </li>

            `;
            tableBody.appendChild(newRow);
            document.getElementById('alias_name').value="";
        }else{
            alert("Duplicates entry!");
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
                url: '../controllers/LedgerController.php?f=checkLedgerAndAlias',
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
    });

</script>
