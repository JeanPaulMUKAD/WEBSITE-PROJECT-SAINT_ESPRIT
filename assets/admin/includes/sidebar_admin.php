<nav id="sidebarMenu" class="sidebar" >
    <div class="sidebar-header">
        <div class="logo-container">
            <i class="fas fa-church"></i>
            <span>Paroisse S.E</span>
        </div>
        <small>Espace Administrateur</small>
    </div>
    
    <div class="user-profile">
        <div class="user-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="user-details">
            <div class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
            <div class="user-role">Administrateur</div>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                    <?php if(basename($_SERVER['PHP_SELF']) == 'dashboard.php'): ?>
                        <span class="badge">Nouveau</span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gestion_sacrements.php' ? 'active' : ''; ?>" href="gestion_sacrements.php">
                    <i class="fas fa-cross"></i>
                    <span>Gestion des Sacrements</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gestion_cev.php' ? 'active' : ''; ?>" href="gestion_cev.php">
                    <i class="fas fa-users"></i>
                    <span>Gestion des CEV</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'parametres.php' ? 'active' : ''; ?>" href="parametres.php">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
            </li>
        </ul>
    </div>
    
    <div class="sidebar-footer">
        <a href="../logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
        <div class="version">
            <small>Version 1.0.0</small>
        </div>
    </div>
</nav>

<style>
.sidebar {
    background: linear-gradient(180deg, #8B0000 0%, #A52A2A 100%);
    width: 280px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    overflow-y: auto;
    box-shadow: 5px 0 20px rgba(0,0,0,0.1);
    z-index: 1000;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 30px 25px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.logo-container {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}

.logo-container i {
    font-size: 30px;
    color: #FFD700;
}

.logo-container span {
    font-size: 1.3rem;
    font-weight: 600;
    color: white;
}

.sidebar-header small {
    color: rgba(255,255,255,0.7);
    font-size: 0.8rem;
    margin-left: 40px;
}

.user-profile {
    padding: 20px 25px;
    background: rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.user-avatar i {
    font-size: 45px;
    color: rgba(255,255,255,0.9);
}

.user-details {
    flex: 1;
}

.user-name {
    color: white;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 3px;
}

.user-role {
    color: rgba(255,255,255,0.7);
    font-size: 0.8rem;
}

.sidebar-menu {
    flex: 1;
    padding: 20px 0;
}

.sidebar-menu .nav-link {
    color: rgba(255,255,255,0.8);
    padding: 12px 25px;
    margin: 5px 15px;
    border-radius: 10px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
}

.sidebar-menu .nav-link i {
    width: 20px;
    font-size: 1.2rem;
}

.sidebar-menu .nav-link:hover {
    background: rgba(255,255,255,0.15);
    color: white;
    transform: translateX(5px);
}

.sidebar-menu .nav-link.active {
    background: rgba(255,255,255,0.2);
    color: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    border-left: 3px solid #FFD700;
}

.sidebar-menu .nav-link.active .badge {
    background: #FFD700;
    color: #8B0000;
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 12px;
    position: absolute;
    right: 15px;
}

.sidebar-footer {
    padding: 20px 25px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.logout-link {
    display: flex;
    align-items: center;
    gap: 12px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    padding: 10px;
    border-radius: 10px;
    transition: all 0.3s;
    margin-bottom: 10px;
}

.logout-link:hover {
    background: rgba(255,0,0,0.3);
    color: white;
    text-decoration: none;
}

.logout-link i {
    width: 20px;
}

.version {
    color: rgba(255,255,255,0.5);
    text-align: center;
    font-size: 0.75rem;
}

/* Scrollbar personnalisée */
.sidebar::-webkit-scrollbar {
    width: 5px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.1);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.3);
    border-radius: 5px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.5);
}
</style>