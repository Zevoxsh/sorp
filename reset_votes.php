<?php
require_once 'function.inc.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
$user = getUser();
clearVotes($user['id']);
header('Location: game.php');
exit;
