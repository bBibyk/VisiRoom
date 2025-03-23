<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - VisiBoost</title>
    <link rel="stylesheet" href="css/contact.css">
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
</head>
<body>

<!-- Barre de navigation -->
<?php require_once 'view/header.php'; ?>

<!-- Contenu principal -->
<div class="contact-container">
    <h1>Contactez-nous</h1>
    <p>Si vous avez des questions, des suggestions ou des préoccupations, n'hésitez pas à nous contacter. Nous sommes là pour vous aider !</p>

    <!-- Formulaire de contact -->
    <form action="#" method="POST" class="contact-form">
        <input type="text" name="name" placeholder="Votre nom" required>
        <input type="email" name="email" placeholder="Votre adresse e-mail" required>
        <textarea name="message" placeholder="Votre message" rows="5" required></textarea>
        <button type="submit">Envoyer le message</button>
    </form>
</div>

<!-- Pied de page -->
<footer class="footer">
    <p>© 2025 VisiBoost - Tous droits réservés.</p>
</footer>

</body>
</html>
