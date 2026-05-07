<?php
session_start();
require_once 'database/db.php';

global $pdo;
try {
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )');
} catch (PDOException $e) {
    error_log('Table creation error: ' . $e->getMessage());
}

// votes table
try {
    $pdo->exec('CREATE TABLE IF NOT EXISTS votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        profile_id INT NOT NULL,
        vote VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id),
        INDEX (profile_id)
    )');
} catch (PDOException $e) {
    error_log('Votes table creation error: ' . $e->getMessage());
}

function login($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM `users` WHERE `username` = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

function register($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM `users` WHERE `username` = :username');
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        return false;
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO `users` (`username`, `password`) VALUES (:username, :password)');
    return $stmt->execute(['username' => $username, 'password' => $hashedPassword]);
}

function start() {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

function getUser() {
    return $_SESSION['user'] ?? null;
}

function saveVote($userId, $profileId, $vote) {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO votes (user_id, profile_id, vote) VALUES (:user_id, :profile_id, :vote)');
    return $stmt->execute(['user_id' => $userId, 'profile_id' => $profileId, 'vote' => $vote]);
}

function getVotedProfileIds($userId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT profile_id FROM votes WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return array_map(function($r){ return (int)$r['profile_id']; }, $rows);
}

function clearVotes($userId) {
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM votes WHERE user_id = :user_id');
    return $stmt->execute(['user_id' => $userId]);
}
