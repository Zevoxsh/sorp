<?php

function getProfiles($limit = 10, $seed = null) {
    static $cachedProfiles = null;

    if ($cachedProfiles !== null && $seed === null) {
        return $cachedProfiles;
    }

    $apiProfiles = fetchAniListFemaleProfiles(max($limit, 30));
    if (!empty($apiProfiles)) {
        if ($seed !== null) {
            $apiProfiles = sortProfilesWithSeed($apiProfiles, $seed);
        }

        $selectedProfiles = array_slice($apiProfiles, 0, $limit);
        if ($seed === null) {
            $cachedProfiles = $selectedProfiles;
        }

        return $selectedProfiles;
    }

    $fallbackProfiles = getFallbackFemaleProfiles();
    if ($seed !== null) {
        $fallbackProfiles = sortProfilesWithSeed($fallbackProfiles, $seed);
    }

    $selectedProfiles = array_slice($fallbackProfiles, 0, $limit);
    if ($seed === null) {
        $cachedProfiles = $selectedProfiles;
    }

    return $selectedProfiles;
}

function fetchAniListFemaleProfiles($limit = 10) {
    $profiles = [];
    $query = 'query ($page: Int, $perPage: Int) { Page(page: $page, perPage: $perPage) { media(type: ANIME, sort: POPULARITY_DESC) { id title { romaji } coverImage { large } characters(role: MAIN) { nodes { id name { full } gender image { large } } } } } }';

    for ($page = 1; $page <= 10 && count($profiles) < $limit; $page++) {
        $payload = json_encode([
            'query' => $query,
            'variables' => [
                'page' => $page,
                'perPage' => 50,
            ],
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nAccept: application/json\r\nUser-Agent: SmashOrPass/1.0\r\n",
                'content' => $payload,
                'timeout' => 8,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents('https://graphql.anilist.co', false, $context);
        if ($response === false) {
            continue;
        }

        $decoded = json_decode($response, true);
        if (empty($decoded['data']['Page']['media']) || !is_array($decoded['data']['Page']['media'])) {
            continue;
        }

        foreach ($decoded['data']['Page']['media'] as $anime) {
            $coverImage = $anime['coverImage']['large'] ?? '';
            $animeTitle = $anime['title']['romaji'] ?? '';
            $characters = $anime['characters']['nodes'] ?? [];

            if ($coverImage === '' || $animeTitle === '' || !is_array($characters)) {
                continue;
            }

            foreach ($characters as $character) {
                $gender = $character['gender'] ?? '';
                $name = $character['name']['full'] ?? '';

                if (strcasecmp($gender, 'Female') === 0 && $name !== '') {
                    $profiles[] = [
                        'id' => (int) $anime['id'],
                        'name' => $name . ' - ' . $animeTitle,
                        'img' => $coverImage,
                    ];

                    if (count($profiles) >= $limit) {
                        break 3;
                    }
                }
            }
        }
    }

    return array_values($profiles);
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
        ['id' => 1001, 'name' => 'Faye Valentine', 'img' => 'https://placehold.co/600x600?text=Faye+Valentine'],
        ['id' => 1002, 'name' => 'Rukia Kuchiki', 'img' => 'https://placehold.co/600x600?text=Rukia+Kuchiki'],
        ['id' => 1003, 'name' => 'Orihime Inoue', 'img' => 'https://placehold.co/600x600?text=Orihime+Inoue'],
        ['id' => 1004, 'name' => 'Haruhi Fujioka', 'img' => 'https://placehold.co/600x600?text=Haruhi+Fujioka'],
        ['id' => 1005, 'name' => 'Renge Houshakuji', 'img' => 'https://placehold.co/600x600?text=Renge+Houshakuji'],
        ['id' => 1006, 'name' => 'Mikasa Ackerman', 'img' => 'https://placehold.co/600x600?text=Mikasa+Ackerman'],
        ['id' => 1007, 'name' => 'Saber', 'img' => 'https://placehold.co/600x600?text=Saber'],
        ['id' => 1008, 'name' => 'Asuka Langley Soryu', 'img' => 'https://placehold.co/600x600?text=Asuka+Langley+Soryu'],
        ['id' => 1009, 'name' => 'Rem', 'img' => 'https://placehold.co/600x600?text=Rem'],
        ['id' => 1010, 'name' => 'Zero Two', 'img' => 'https://placehold.co/600x600?text=Zero+Two'],
    ];
}
