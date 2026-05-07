<?php
require_once 'model.inc.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
$user = getUser();
clearVotes($user['id']);
$_SESSION['profile_seed'] = bin2hex(random_bytes(8));
header('Location: game.php');
exit;
?>