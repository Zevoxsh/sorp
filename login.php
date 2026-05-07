<?php
require_once 'config/model.inc.php';

// If form submitted, attempt to log in
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = login($username, $password);
    if ($user) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Identifiants invalides.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" type="image/png" href="asset/logo.png">
</head>
<body>
    <div class="main-wrapper">
        <h1>Connexion</h1>
        <div class="content-box">
            <?php if (!empty($error)) echo '<p class="error-message">' . htmlspecialchars($error) . '</p>'; ?>
            <form action="login.php" method="post">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" required placeholder="Entrez votre pseudo">
                
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required placeholder="Entrez votre mot de passe">
                
                <input type="submit" value="Se connecter">
            </form>
            <p class="signup-link">Vous n'avez pas de compte ? <a href="register.php">S'inscrire ici</a></p>
        </div>
    </div>
</body>
</html>