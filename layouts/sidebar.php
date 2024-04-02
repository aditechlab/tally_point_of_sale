<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <i class="icon-columns menu-icon"></i>
                <span class="menu-title">Masters</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" data-toggle="collapse" href="#account-info" aria-expanded="false" aria-controls="account-info">
                            <span class="menu-title">Account Info</span>
                            <i class="menu-arrow"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inventory-info.php">Inventory Info</a>
                    </li>
                </ul>
            </div>
            <div class="collapse" id="account-info">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="account-group.php">Groups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view-ledger.php">Ledgers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Currency</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Voucher Type</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>