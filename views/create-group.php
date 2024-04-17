<?php
include('../layouts/header.php');
require('../database/connection.php');
require('../models/StockGroup.php');


$database = new Database();
$db = $database->connect();
$group = new StockGroup($db);


$parents = $group->fetchParent();




?>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="card-title">Create Group</p>
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
                        <form id="addGroup" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-md-6 col-lg-4">
                                    <label for="group_name">Group Name</label>
                                    <input type="text" class="form-control" name="group_name" id="group_name" placeholder="Group Name" required="required"/>
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
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 col-lg-4">
                                    <label for="group_name">Parent</label>
                                    <select class="form-control" name="parent" id="parent" required>
                                        <option value="">Select parent ...</option>
                                        <option value="Primary">Primary</option>
                                        <?php foreach ($parents as $val){ ?>
                                            <option value="<?php echo $val['name'] ?>"><?php echo $val['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
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

                document.getElementById('addGroup').action ="../controllers/StockGroupController.php?f=createGroup";

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
        $('#group_name, #alias_name').on('blur', function(){
            var field = $(this); // Get the current field
            var value = field.val(); // Get the entered value
            var fieldName = field.attr('name');

            $.ajax({
                type: 'POST',
                url: '../controllers/StockGroupController.php?f=validateGroupAndAlias',
                data: {
                    group_name: value,
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
