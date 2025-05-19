<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <base href='/Visiboost/Visiboost/'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse SEO - VisiRoom</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/result.css">
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
</head>
<body>

<!--Header-->
<?php require_once 'view/header.php'; ?>

<div class="analysis-container">
    <h1>Retour des Analyses</h1>

    <div class="analysis-section">
        <h2>Analyse</h2>
        <?php echo '<p>'.$result.'</p>' ?>
    </div>


    <?php if($analysis->getAnalysisType()->getLabel() == 'html'){
            echo '<div class="analysis-section">
                    <h2>3 - Modifications à faire :</h2>
                    <ul>
                        <li>Corriger les balises mal structurées.</li>
                        <li>Optimiser les images pour un chargement plus rapide.</li>
                        <li>Améliorer l’accessibilité et le référencement.</li>
                    </ul>
                </div>';
        }
    ?>

    <div class="advantages-section">
        <h2>Avantages de l'Abonnement</h2>
        <ul class="features-list">
            <li>Analyse de vos sites web - Illimité</li>
            <li>Repérage des erreurs</li>
            <li>Conseils personnalisés</li>
            <li>Suivi de vos analyses</li>
            <li>Analyse concurrentielle</li>
            <li>Analyse par nos experts</li>
            <li>Application des conseils par nos experts</li>
        </ul>
        <button class="subscribe-button">S’abonner</button>
    </div>
</div>

</body>
</html>
