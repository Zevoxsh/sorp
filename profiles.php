<?php
function getProfiles($limit = 25, $seed = null) {
    static $cachedProfiles = null;

    if ($cachedProfiles !== null && $seed === null) {
        return $cachedProfiles;
    }

    $profiles = loadAnimeProfilesFromFolder();
    if ($seed !== null) {
        $profiles = sortProfilesWithSeed($profiles, $seed);
    }

    $selectedProfiles = array_slice($profiles, 0, $limit);
    if ($seed === null) {
        $cachedProfiles = $selectedProfiles;
    }

    return $selectedProfiles;
}

function loadAnimeProfilesFromFolder() {
    $profiles = [];
    $animeFolder = __DIR__ . DIRECTORY_SEPARATOR . 'anime';
    $files = array_merge(
        glob($animeFolder . DIRECTORY_SEPARATOR . '*.png') ?: [],
        glob($animeFolder . DIRECTORY_SEPARATOR . '*.jpg') ?: [],
        glob($animeFolder . DIRECTORY_SEPARATOR . '*.jpeg') ?: [],
        glob($animeFolder . DIRECTORY_SEPARATOR . '*.webp') ?: []
    );

    if (!is_array($files) || empty($files)) {
        return getFallbackFemaleProfiles();
    }

    sort($files, SORT_NATURAL | SORT_FLAG_CASE);

    foreach ($files as $filePath) {
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);
        $displayName = ucwords(str_replace(['-', '_'], ' ', $fileName));

        $profiles[] = [
            'id' => abs(crc32($fileName)),
            'name' => $displayName,
            'img' => 'anime/' . basename($filePath),
        ];
    }

    return $profiles;
}

function sortProfilesWithSeed(array $profiles, $seed) {
    usort($profiles, function ($left, $right) use ($seed) {
        $leftHash = hash('sha256', $seed . ':' . $left['id']);
        $rightHash = hash('sha256', $seed . ':' . $right['id']);

        return strcmp($leftHash, $rightHash);
    });

    return $profiles;
}

function getFallbackFemaleProfiles() {
    return [
        ['id' => 1001, 'name' => 'Akane', 'img' => 'anime/akane.svg'],
        ['id' => 1002, 'name' => 'Yuna', 'img' => 'anime/yuna.svg'],
        ['id' => 1003, 'name' => 'Mika', 'img' => 'anime/mika.svg'],
        ['id' => 1004, 'name' => 'Rin', 'img' => 'anime/rin.svg'],
        ['id' => 1005, 'name' => 'Aya', 'img' => 'anime/aya.svg'],
        ['id' => 1006, 'name' => 'Sora', 'img' => 'anime/sora.svg'],
        ['id' => 1007, 'name' => 'Mei', 'img' => 'anime/mei.svg'],
        ['id' => 1008, 'name' => 'Nami', 'img' => 'anime/nami.svg'],
        ['id' => 1009, 'name' => 'Reina', 'img' => 'anime/reina.svg'],
        ['id' => 1010, 'name' => 'Momo', 'img' => 'anime/momo.svg'],
        ['id' => 1011, 'name' => 'Hina', 'img' => 'anime/hina.svg'],
        ['id' => 1012, 'name' => 'Yui', 'img' => 'anime/yui.svg'],
        ['id' => 1013, 'name' => 'Lila', 'img' => 'anime/lila.svg'],
        ['id' => 1014, 'name' => 'Suzu', 'img' => 'anime/suzu.svg'],
        ['id' => 1015, 'name' => 'Nozomi', 'img' => 'anime/nozomi.svg'],
    ];
}
?>