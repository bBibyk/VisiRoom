<!DOCTYPE html>
<html lang="fr">
<head>
    <base href="/Visiboost/Visiboost/"/> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="css/registration.css">
</head>
<body>
    <div class="form-container">
        <h2>Inscription</h2>

        <!-- Affichage des erreurs -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/Visiboost/Visiboost/index.php?controller=user&action=add" method="POST">
            <div class="form-group">
                <label for="firstname">Prénom :</label>
                <input type="text" id="firstname" name="firstname" placeholder="Entrez votre prénom" value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="surname">Nom :</label>
                <input type="text" id="surname" name="surname" placeholder="Entrez votre nom" value="<?php echo htmlspecialchars($_POST['surname'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" placeholder="Entrez votre email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
            </div>
            <button type="submit">S'inscrire</button>
        </form>
    </div>
</body>
</html>
