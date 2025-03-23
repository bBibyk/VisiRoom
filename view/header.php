<!-- Barre de navigation -->
<nav class="navbar">
    <div class="left-menu">
        <div class="logo-container">
            <img src="img/logo.png" alt="Logo VisiBoost" class="logo-img">
            <div class="logo">VisiBoost</div>
        </div>
        <ul class="nav-links">
            <li><a href="analysis">Accueil</a></li>
            <li><a href="aboutus">À propos de nous</a></li>
            <li><a href="subscribe">S’abonner</a></li>
            <li><a href="contact">Contact</a></li>
            <?php if(isset($_SESSION["email"]) && !empty($_SESSION["email"])){
                        echo '<li><a href="">Déconnexion</a></li>';
                    }else{  
                        echo '<li><a href="connection">Authentification</a></li>';
                    }
            ?>
        </ul>
    </div>
</nav>
