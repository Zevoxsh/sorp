<?php
require_once 'model.inc.php';
if (session_status() === PHP_SESSION_NONE) session_start();
logout();
header('Location: ../index.php');
exit;
?>