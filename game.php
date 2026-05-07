<?php
require_once 'config/model.inc.php';
require_once 'profiles.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
$profilesSeed = bin2hex(random_bytes(8));

$profiles = getProfiles(25, $profilesSeed);
// Find first profile not yet voted by this user (DB)
$user = getUser();
$votedIds = getVotedProfileIds($user['id']);
$remainingProfiles = [];
foreach ($profiles as $p) {
    if (!in_array($p['id'], $votedIds)) {
        $remainingProfiles[] = $p;
    }
}
$first = $remainingProfiles[0] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <title>Smash or Pass</title>
    <link rel="icon" type="image/png" href="asset/logo.png">
</head>
<body>
    <div class="main-wrapper">
        <a href="index.php"><img src="asset/logo.png" alt="Smash Or Pass Logo" class="logo"></a>
        <div id="game-area">
            <?php if ($first): ?>
            <div class="card" data-id="<?= $first['id'] ?>">
                <img src="<?= htmlspecialchars($first['img']) ?>" alt="<?= htmlspecialchars($first['name']) ?>">
                <h2><?= htmlspecialchars($first['name']) ?></h2>
                <div class="buttons">
                    <button id="smash"><i class="material-icons">favorite</i> Smash</button>
                    <button id="pass"><i class="material-icons">close</i> Pass</button>
                </div>
            </div>
            <?php else: ?>
            <div class="content-box">
                <p><i class="material-icons check-icon">check_circle</i> Vous avez voté sur tous les profils !</p>
                <p><a href="config/reset_votes.php" class="btn-primary"><i class="material-icons">refresh</i> Recommencer</a></p>
            </div>
            <?php endif; ?>
        </div>

        <p class="return-link"><a href="index.php"><i class="material-icons">arrow_back</i> Retour à l'accueil</a></p>
    </div>

    <script>
    const profiles = <?= json_encode(array_values($remainingProfiles), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    let currentIndex = 0;

    async function sendVote(id, action) {
        try {
            const resp = await fetch('config/vote.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id: id, action: action})
            });
            return await resp.json();
        } catch (error) {
            return { error: error.message };
        }
    }

    function renderProfile(profile) {
        document.getElementById('game-area').innerHTML = `
        
            <div class="card" data-id="${profile.id}">
                <img src="${profile.img}" alt="${profile.name}">
                <h2>${profile.name}</h2>
                <div class="buttons">
                    <button id="smash"><i class="material-icons">favorite</i> Smash</button>
                    <button id="pass"><i class="material-icons">close</i> Pass</button>
                </div>
            </div>`;
    }

    function renderEnd() {
        document.getElementById('game-area').innerHTML = '<p>Vous avez voté sur tous les profils. <a href="config/reset_votes.php">Recommencer</a></p>';
    }

    document.addEventListener('click', async (e) => {
        if (e.target.id === 'smash' || e.target.id === 'pass') {
            const card = e.target.closest('.card');
            const id = card.getAttribute('data-id');
            const action = e.target.id === 'smash' ? 'smash' : 'pass';
            e.target.disabled = true;
            await sendVote(parseInt(id,10), action);
            currentIndex += 1;
            if (profiles[currentIndex]) {
                renderProfile(profiles[currentIndex]);
            } else {
                renderEnd();
            }
        }
    });
    </script>
</body>
</html>
