<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
<?php
require_once 'function.inc.php';
?>
    <h1>Inscription</h1>
    <?php
    if (isLoggedIn()) {
        echo '<p>Vous êtes déjà connecté en tant que ' . getUser()['username'] . '.</p>';
        echo '<a href="index.php">Retour à l\'accueil</a>';
    } else {
        if (isset($_GET['error'])) {
            echo '<p style="color:red;">' . htmlspecialchars($_GET['error']) . '</p>';
        }
    }
    require_once 'function.inc.php';

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
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="submit" value="S'inscrire">
    </form>

</body>
</html>