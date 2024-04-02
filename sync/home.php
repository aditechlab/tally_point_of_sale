<?php error_reporting(0); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Top Market</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="../assets/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="../assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../assets/css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="../assets/images/logo.png" />
</head>

<body>
    <div class="container-scroller">
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                    <a class="navbar-brand brand-logo mr-5" href="dashboard.php"><img src="../assets/images/logo.png" class="mr-2" alt="logo" /> Top Market</a>
                    <a class="navbar-brand brand-logo-mini" href="dashboard.php"><img src="../assets/images/logo.png" alt="logo" /></a>
                </div>
            </div>
        </nav>
        <div class="main-panel w-100">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-12 pt-5 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Sync Data</h4>
                                <div class="alert-box"></div>
                                <form class="forms-sample" id="my-form">
                                    <div class="form-group">
                                        <label for="inventory">Inventory</label>
                                        <select class="form-control form-control-lg" name="inventory" id="inventory">
                                            <option value="">Please select</option>
                                            <option value="Groups">Stock Groups</option>
                                            <option value="Items">Stock Items</option>
                                            <option value="Categories">Stock Categories</option>
                                            <option value="Units">Stock Units</option>
                                        </select>
                                        <span id="inventory_error" class="text-danger"></span>
                                    </div>
                                    <div class="template-demo">
                                        <div class="progress progress-lg">
                                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"><span id="percent">0%</span></div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary mr-2 mt-4 submit">Submit</button>
                                    <button class="btn btn-light mt-4" id="cancel">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="../assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="../assets/vendors/select2/select2.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="../assets/js/off-canvas.js"></script>
    <script src="../assets/js/hoverable-collapse.js"></script>
    <script src="../assets/js/template.js"></script>
    <script src="../assets/js/settings.js"></script>
    <script src="../assets/js/todolist.js"></script>
    <script src="../assets/js/select2.js"></script>
    <!-- endinject -->
    <script>
        $(function() {
            window.ajax_loading = false;
            $(document).hasAjaxRunning = function() {
                console.log('Loading');
                return window.ajax_loading;
            };
            $(document).ajaxStart(function() {
                console.log('Loading Start');
                window.ajax_loading = true;
            });
            $(document).ajaxStop(function() {
                console.log('Loading Not');
                window.ajax_loading = false;
            });
        });
        $(document).ready(function() {
            $('.template-demo').css('display', 'none');

            // File upload via Ajax
            $(".submit").on('click', function(e) {
                e.preventDefault();
                let inventor = $('#inventory').val();

                if (inventor == "") {
                    $('#inventory_error').text('Inventory is required');;
                    return false;
                }

                let url = "";

                if (inventor == "Groups") {
                    url = "../controllers/StockGroupController.php?f=insertStockGroup"
                } else if (inventor == "Items") {
                    url = "../controllers/StockItemController.php?f=create";
                } else if (inventor == "Categories") {
                    url = "../controllers/StockCategoryController.php?f=create";
                } else if (inventor == "Units") {
                    url = "../controllers/StockUnitController.php?f=create";
                }
                
                var totalDataSize = 638;
                var interval = (totalDataSize / 100) ;
                // get start time
                var startTime = new Date().getTime();
                var myform = $("#my-form")[0];
                var formData = new FormData(myform);

                var xhr = $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                // var percentComplete = ((evt.loaded / evt.total) * 100);
                                // $("#percent").html(Math.floor(percentComplete) + '%');
                                // $(".progress-bar").attr('aria-valuenow', percentComplete)
                                // $(".progress-bar").width(percentComplete + '%');
                                // $(".progress-bar").html(percentComplete + '%');
                                // console.log(percentComplete);
                                var percentage = 0;

                                var timer = setInterval(function(){
                                percentage = percentage + interval;
                                console.log(percentage);
                                progress_bar_process(percentage, timer);
                                }, 1000);
                            }
                        }, false);
                        xhr.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                //Do something with download progress
                                console.log(percentComplete+"D");
                            }
                        }, false);
                        return xhr;
                    },
                    type: 'POST',
                    url: url,
                    async: true,
                    contentType: false,
                    cache: false,
                    processData: false,
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        // add some preloader or perform any action before uploading
                        $(this).attr('disabled', 'disabled');
                        $('.template-demo').css('display', 'block');
                        $("#percent").html('0%');
                        $(".progress-bar").width('0%');
                    },
                    error: function(response) {
                        // if request not complete
                        console.log(response);
                        $(".alert-box").html(`${response}`)
                    },
                    success: function(response) {
                        // get response on successful uploading
                        // $("#percent").html('Synchronized');
                        console.log('done');
                        $(".alert-box").html(`<div class="alert alert-${response.type}">${response.message}</div>`)
                    }
                });
                // for cancel file transfer
                $('#cancel').on("click", () => {
                    xhr.abort().then(
                        $("#percent").html('Canceled'),
                        $(".progress-bar").width('0%')
                    )
                });

                function progress_bar_process(percentage, timer, response) {
                    $("#percent").html(Math.floor(percentage) + '%');
                    $('.progress-bar').css('width', percentage + '%');

                    if (percentage > 100) {
                        clearInterval(timer);
                        $('.progress-bar').css('width', '0%');
                        $('#save').attr('disabled', false);
                        $(".alert-box").html(`<div class="alert alert-${response.type}">${response.message}</div>`)
                    }
                }

            });
        });
    </script>
</body>

</html>