<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - VisiBoost</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
</head>
<body>

<!--Header-->
<?php require_once 'view/header.php'; ?>

<div class="dashboard-container">
    <h1>Tableau de bord des analyses</h1>
    <table class="analysis-table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Page analysée</th>
            <th>Type d’analyse</th>
            <th>Voir</th>
        </tr>
        </thead>
        <tbody>
            <?php
                foreach($listAnalysis as $analysis){
                    $url = urlencode($analysis->getDate()).'/'.$analysis->getWebsite()->getId().'/'.$analysis->getAnalysisType()->getId();
                    echo '<tr><td>'.$analysis->getDate().'</td><td>'.$analysis->getWebsite()->getDomainName().'</td><td>'.$analysis->getAnalysisType()->getLabel().'</td><td><a href="getAnalysis/'.$url.'">Voir</a></td></tr>';
                }
            ?>

        </tbody>
    </table>
</div>

</body>
</html>