<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="sidebar-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gestion_sacrements.php' ? 'active' : ''; ?>" href="gestion_sacrements.php">
                    <i class="fas fa-cross mr-2"></i>
                    Gestion des Sacrements
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gestion_cev.php' ? 'active' : ''; ?>" href="gestion_cev.php">
                    <i class="fas fa-users mr-2"></i>
                    Gestion des CEV
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'parametres.php' ? 'active' : ''; ?>" href="parametres.php">
                    <i class="fas fa-cog mr-2"></i>
                    Paramètres
                </a>
            </li>
        </ul>
    </div>
</nav>