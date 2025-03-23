<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - VisiBoost</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/registration.css">
</head>
    <body>
        <!--Header-->
        <?php require_once 'view/header.php'; ?>

        <div class="login-container">
            <h1>Inscription</h1>
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

            <form action="/Visiboost/Visiboost/addUser" method="POST">
                <input type="text" name="firstname" placeholder="Prénom" value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>" required>
                <input type="text" name="surname" placeholder="Nom" value="<?php echo htmlspecialchars($_POST['surname'] ?? ''); ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <input type="password" name="passwordComfirmation" placeholder="Comfirmer mot de passe" required>
                <button type="submit">Valider</button>
            </form>
            <p>Déjà un compte ? <a href="#">Se connecter</a></p>
        </div>
    </body>
</html>
