<?php
require_once 'model.inc.php';
require_once 'profiles.php';
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data || !isset($data['id']) || !isset($data['action'])) {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user = getUser();
$userId = (int)$user['id'];
$id = (int)$data['id'];
$action = $data['action'] === 'smash' ? 'smash' : 'pass';
$profilesSeed = $_SESSION['profile_seed'] ?? null;
if ($profilesSeed === null) {
    $profilesSeed = bin2hex(random_bytes(8));
    $_SESSION['profile_seed'] = $profilesSeed;
}

// save vote in database
saveVote($userId, $id, $action);

// determine next profile based on DB votes for this user
$profiles = getProfiles(25, $profilesSeed);
$votedIds = getVotedProfileIds($userId);
$next = null;
foreach ($profiles as $p) {
    if (!in_array($p['id'], $votedIds)) { $next = $p; break; }
}

echo json_encode(['next' => $next]);
?>