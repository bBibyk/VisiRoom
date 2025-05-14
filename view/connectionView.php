<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - VisiRoom</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/registration.css">
</head>
    <body>
        <!-- Barre de navigation -->
        <?php require_once 'view/header.php'; ?>

        <div class="login-container">
            <h1>Connexion</h1>
            <p>Profitez d'une analyse gratuite de votre site avec :</p>
            <ul>
                <li>✓ Analyse de vos sites web</li>
                <li>✓ Repérage des erreurs</li>
                <li>✓ Conseils personnalisés</li>
                <li>✓ Suivi de vos analyses</li>
            </ul>

            <!-- Affichage des erreurs -->
            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p class="error"> <?php echo htmlspecialchars($error); ?> </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="/Visiboost/Visiboost/getUser" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Valider</button>
            </form>

            <p>Vous n'avez pas encore de compte ? <a href="registration">Inscription</a></p>
        </div>
    </body>
</html>
