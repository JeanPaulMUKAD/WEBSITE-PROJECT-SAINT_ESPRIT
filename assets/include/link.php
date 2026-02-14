<!-- header.php - à inclure dans vos pages -->
<header class="">
    <nav class="navbar navbar-expand-lg">
        <div class="container"> 
            <a class="navbar-brand" href="index.php?search=acceuil">
                <h2>PAROISSE<em class="auto-type"> S.E</em></h2>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" 
                    data-target="#navbarResponsive" aria-controls="navbarResponsive" 
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item <?php if (isset ($_GET["search"]) and $_GET["search"] == 'acceuil') {echo 'active';} elseif (isset ($_GET["search"]) and $_GET["search"] == 'NULL'){echo 'active';}?> ">
                        <a class="nav-link" href="index.php?search=acceuil">ACCEUIL
                            <span class="sr-only">(current)</span>
                        </a>
                    </li> 
                    <li class="nav-item <?php if (isset ($_GET["search"]) and $_GET["search"] == 'about'){echo 'active';}?> ">
                        <a class="nav-link" href="index.php?search=about">A PROPOS</a>
                    </li>
                    <li class="nav-item <?php if (isset ($_GET["search"]) and $_GET["search"] == 'actuality'){echo 'active';}?> ">
                        <a class="nav-link" href="index.php?search=actuality">ACTUALITES</a>
                    </li>
                    <li class="nav-item <?php if (isset ($_GET["search"]) and $_GET["search"] == 'move'){echo 'active';}?> ">
                        <a class="nav-link" href="index.php?search=move">ANNEE PASTORALE</a>
                    </li>
                    <li class="nav-item <?php if (isset ($_GET["search"]) and $_GET["search"] == 'contact'){echo 'active';}?> ">
                        <a class="nav-link" href="index.php?search=contact">CONTACT</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light ml-2 px-3" href="assets/admin/connexion/login.php" style="border: 1px solid #fff; border-radius: 5px;">
                            <i class="fas fa-lock"></i> ADMIN
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>