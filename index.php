<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smash Or Pass</title>
</head>
<body>
    <h1>Bienvenue sur Smash Or Pass</h1>
    <?php
    require_once 'function.inc.php';
    if (isLoggedIn()) {
        echo '<p>Vous êtes connecté en tant que ' . getUser()['username'] . '.</p>';
            echo '<a href="game.php">Jouer Smash or Pass</a> | ';
            echo '<a href="logout.php">Se déconnecter</a>';
    } else {
        echo '<a href="register.php">S\'inscrire</a> | <a href="login.php">Se connecter</a>';
    }
    ?>
    
</body>
</html>