<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisiRoom</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/analysis.css">
    <link rel="stylesheet" href="css/loading.css">
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
</head>
<body>
<div id="page">
<!--Header-->
<?php require_once 'view/header.php'; ?>

<div class="intro-message">
    <h1>Prêt à Booster votre Référencement ?</h1>
    <p><span class="highlight">Rentrez le lien de votre site et suivez nos conseils</span></p>
</div>


<!-- ===== ÉCRAN D'ACCUEIL DIVISÉ ===== -->
<div class="split-screen">
    <div class="split left">
        <form action="/Visiboost/Visiboost/addAnalysisHtml" method="POST">
            <h2>Analyse Rapide</h2>
            <p>Entrez simplement l'URL de votre site pour une analyse immédiate.</p>
            <input type="text" name="domainName" placeholder="Adresse de votre site (obligatoire) ..." />
            <button id="htmlButton" type="submit">Lancer l'analyse</button>
        </form>
    </div>

    <div class="split right">
        <h2>Analyse Ciblée</h2>
        <p>Spécifiez une page et une requête cible pour une analyse personnalisée.</p>
        <form action="/Visiboost/Visiboost/addAnalysisSearch" method="POST" class="target-form">
            <input type="text" name="domainName" placeholder="URL de la page à analyser" required />
            <input type="text" name="sentence" placeholder="Requête cible (ex: agence SEO Toulouse)" required />
            <button id="searchButton" type="submit">Lancer l'analyse ciblée</button>
        </form>
    </div>
</div>

<!-- Section Avantages -->
<div class="advantages-section">
    <h2>Pourquoi choisir un abonnement VisiBoost ?</h2>
    <ul class="features-list">
        <li> Analyse illimitée de votre site web</li>
        <li> Détection des erreurs SEO et recommandations</li>
        <li> Conseils personnalisés pour booster votre référencement</li>
        <li> Suivi des performances SEO en temps réel</li>
        <li> Accès à nos experts pour des analyses approfondies</li>
    </ul>
    <button class="subscribe-button" onclick="window.location.href='subscribe'">S'abonner</button>
</div>

<!-- Section Connexion/Inscription -->
<div class="auth-section">
    <h2>Accédez à votre compte</h2>
    <p>Connectez-vous pour suivre vos analyses ou inscrivez-vous gratuitement !</p>
    <div class="auth-buttons">
        <a href="connection" class="login-btn">Connexion</a>
        <a href="registration" class="signup-btn">Inscription</a>
    </div>
</div>
</div>

<div id="container-loading">
        <div id="loader" class="loader"></div>
        <p id="message" class="message">Analyse en cours</p>
</div>

<script src="js/loading.js"></script>
</body>
</html>
