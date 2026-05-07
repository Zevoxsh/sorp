<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smash Or Pass</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" type="image/png" href="asset/logo.png">
</head>
<body>
    <div class="main-wrapper">
        <div class="logo-container">
            <img src="asset/logo.png" alt="Smash Or Pass Logo" class="logo">
        </div>
        <div class="content-box">
            <?php
            require_once 'config/model.inc.php';
            if (isLoggedIn()) {
                echo '<p>Vous êtes connecté en tant que <strong>' . htmlspecialchars(getUser()['username']) . '</strong>.</p>';
                echo '<div class="actions-links">';
                echo '<a href="game.php" class="btn-primary"><i class="material-icons">sports_esports</i> Jouer Smash or Pass</a>';
                echo '<a href="config/logout.php" class="btn-secondary"><i class="material-icons">logout</i> Se déconnecter</a>';
                echo '</div>';
            } else {
                echo '<p>Veuillez vous connecter pour commencer !</p>';
                echo '<div class="actions-links">';
                echo '<a href="register.php" class="btn-primary"><i class="material-icons">person_add</i> S\'inscrire</a>';
                echo '<a href="login.php" class="btn-primary"><i class="material-icons">login</i> Se connecter</a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>