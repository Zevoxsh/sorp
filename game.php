<?php
require_once 'function.inc.php';
require_once 'profiles.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
$profiles = getProfiles();
// Find first profile not yet voted by this user (DB)
$user = getUser();
$votedIds = getVotedProfileIds($user['id']);
$first = null;
foreach ($profiles as $p) {
    if (!in_array($p['id'], $votedIds)) { $first = $p; break; }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smash or Pass</title>
    <style>
        body { font-family: Arial, sans-serif; text-align:center; }
        .card { display:inline-block; border:1px solid #ccc; padding:16px; border-radius:8px; }
        img { width:300px; height:300px; object-fit:cover; border-radius:8px; }
        .buttons { margin-top:12px; }
        button { padding:10px 20px; margin:0 8px; font-size:16px; }
    </style>
</head>
<body>
    <h1>Smash or Pass</h1>
    <div id="game-area">
        <?php if ($first): ?>
        <div class="card" data-id="<?= $first['id'] ?>">
            <img src="<?= htmlspecialchars($first['img']) ?>" alt="<?= htmlspecialchars($first['name']) ?>">
            <h2><?= htmlspecialchars($first['name']) ?></h2>
            <div class="buttons">
                <button id="smash">Smash</button>
                <button id="pass">Pass</button>
            </div>
        </div>
        <?php else: ?>
        <p>Vous avez voté sur tous les profils. <a href="reset_votes.php">Recommencer</a></p>
        <?php endif; ?>
    </div>

    <p><a href="index.php">Retour</a></p>

    <script>
    async function sendVote(id, action) {
        const resp = await fetch('vote.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id, action: action})
        });
        return resp.json();
    }

    document.addEventListener('click', async (e) => {
        if (e.target.id === 'smash' || e.target.id === 'pass') {
            const card = e.target.closest('.card');
            const id = card.getAttribute('data-id');
            const action = e.target.id === 'smash' ? 'smash' : 'pass';
            e.target.disabled = true;
            const data = await sendVote(parseInt(id,10), action);
            if (data.next) {
                document.getElementById('game-area').innerHTML = `
                    <div class="card" data-id="${data.next.id}">
                        <img src="${data.next.img}" alt="${data.next.name}">
                        <h2>${data.next.name}</h2>
                        <div class="buttons">
                            <button id="smash">Smash</button>
                            <button id="pass">Pass</button>
                        </div>
                    </div>`;
            } else {
                document.getElementById('game-area').innerHTML = '<p>Vous avez voté sur tous les profils. <a href="reset_votes.php">Recommencer</a></p>';
            }
        }
    });
    </script>
</body>
</html>
