<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" type="image/png" href="asset/logo.png">
</head>
<body>
    <div class="main-wrapper">
        <h1>Inscription</h1>
        <div class="content-box">
            <?php
            require_once 'config/model.inc.php';
            if (isLoggedIn()) {
                echo '<p>Vous êtes déjà connecté en tant que <strong>' . htmlspecialchars(getUser()['username']) . '</strong>.</p>';
                echo '<p><a href="index.php"><i class="material-icons">arrow_back</i> Retour à l\'accueil</a></p>';
            } else {
                if (isset($_GET['error'])) {
                    echo '<p class="error-message">' . htmlspecialchars($_GET['error']) . '</p>';
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = $_POST['username'];
                $password = $_POST['password'];

                if (register($username, $password)) {
                    header('Location: login.php');
                    exit;
                } else {
                    header('Location: register.php?error=Nom d\'utilisateur déjà utilisé.');
                    exit;
                }
            }
            ?>
            <form action="register.php" method="post">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" required placeholder="Choisissez un pseudo">
                
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required placeholder="Créez un mot de passe sécurisé">
                
                <input type="submit" value="S'inscrire">
            </form>
            <p class="login-link">Vous avez déjà un compte ? <a href="login.php">Se connecter ici</a></p>
        </div>
    </div>
</body>
</html>