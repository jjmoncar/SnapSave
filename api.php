<?php
header('Content-Type: application/json; charset=utf-8');

// Configuración de RapidAPI
$RAPIDAPI_KEY = "d374ccf789mshf6309148d5d9351p1935e0jsnd3b3dc8d455e";
$RAPIDAPI_HOST = "social-media-video-downloader.p.rapidapi.com";

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
$url = trim($input['url'] ?? '');

if (!$url) {
    echo json_encode(['status' => false, 'error' => 'URL is required']);
    exit;
}

if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $url)) {
    echo json_encode(['status' => false, 'error' => 'Invalid URL provided. Please paste a valid link.']);
    exit;
}

$platform = '';
$endpoint = '';

// Detección y extracción mejorada por Plataforma
if (strpos($url, 'instagram.com') !== false || strpos($url, 'instagr.am') !== false) {
    $platform = 'instagram';
    $shortcode = '';
    
    // Extracción de shortcode mediante Expresión Regular robusta
    if (preg_match('/(?:p|reel|reels|tv)\/([A-Za-z0-9_-]+)/i', $url, $matches)) {
        $shortcode = $matches[1];
    } else {
        $cleanUrl = strtok($url, '?');
        $urlParts = array_values(array_filter(explode('/', $cleanUrl)));
        $shortcode = end($urlParts) ?: '';
    }

    if (!$shortcode) {
        echo json_encode(['status' => false, 'error' => 'Could not extract valid Instagram post shortcode.']);
        exit;
    }
    
    $endpoint = "/instagram/v3/media/post/details?shortcode=" . urlencode($shortcode) . "&renderableFormats=720p,highres";
} elseif (strpos($url, 'tiktok.com') !== false) {
    $platform = 'tiktok';
    $cleanUrl = strtok($url, '?');
    $endpoint = "/tiktok/v3/post/details?url=" . urlencode($cleanUrl);
} elseif (strpos($url, 'facebook.com') !== false || strpos($url, 'fb.watch') !== false || strpos($url, 'fb.gg') !== false) {
    $platform = 'facebook';
    $cleanUrl = strtok($url, '?');
    $endpoint = "/facebook/v3/post/details?url=" . urlencode($cleanUrl);
} elseif (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
    $platform = 'youtube';
    $endpoint = "/youtube/v3/post/details?url=" . urlencode($url);
} else {
    echo json_encode(['status' => false, 'error' => 'Unsupported platform. Please provide an Instagram, TikTok, Facebook, or YouTube URL.']);
    exit;
}

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://$RAPIDAPI_HOST$endpoint",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 25,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: $RAPIDAPI_HOST",
        "X-RapidAPI-Key: $RAPIDAPI_KEY",
        "Accept: application/json"
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(['status' => false, 'error' => 'Connection error: ' . $err]);
    exit;
}

$data = json_decode($response, true);

if (!$data) {
    echo json_encode(['status' => false, 'error' => 'Invalid API response format.', 'http_code' => $httpCode]);
    exit;
}

// Si la API devuelve error explícito
if (isset($data['message']) && !isset($data['contents']) && !isset($data['result']) && !isset($data['url'])) {
    echo json_encode(['status' => false, 'error' => 'API Error: ' . $data['message']]);
    exit;
}

// Mapeo unificado multiesquema para la extracción de video y miniatura
$rawVideo = null;
$rawThumb = null;

// Esquema 1: v3 contents -> videos
if (!empty($data['contents'][0]['videos'][0]['url'])) {
    $rawVideo = $data['contents'][0]['videos'][0]['url'];
    $rawThumb = $data['metadata']['thumbnailUrl'] ?? ($data['contents'][0]['thumbnailUrl'] ?? null);
}
// Esquema 2: v3 contents -> direct url
elseif (!empty($data['contents'][0]['url'])) {
    $rawVideo = $data['contents'][0]['url'];
    $rawThumb = $data['contents'][0]['thumbnailUrl'] ?? ($data['metadata']['thumbnailUrl'] ?? null);
}
// Esquema 3: result array / string
elseif (!empty($data['result'])) {
    if (is_array($data['result'])) {
        $firstResult = $data['result'][0] ?? null;
        if (is_array($firstResult)) {
            $rawVideo = $firstResult['url'] ?? ($firstResult['video'] ?? null);
            $rawThumb = $firstResult['thumb'] ?? ($firstResult['thumbnail'] ?? null);
        } elseif (is_string($firstResult)) {
            $rawVideo = $firstResult;
        }
    } elseif (is_string($data['result'])) {
        $rawVideo = $data['result'];
    }
}
// Esquema 4: medias / links directos
elseif (!empty($data['medias'][0]['url'])) {
    $rawVideo = $data['medias'][0]['url'];
    $rawThumb = $data['medias'][0]['thumbnail'] ?? null;
} elseif (!empty($data['links'][0]['url'])) {
    $rawVideo = $data['links'][0]['url'];
} elseif (!empty($data['url'])) {
    $rawVideo = $data['url'];
    $rawThumb = $data['thumbnailUrl'] ?? ($data['thumb'] ?? null);
} elseif (!empty($data['data']['video_url'])) {
    $rawVideo = $data['data']['video_url'];
    $rawThumb = $data['data']['cover'] ?? null;
} elseif (!empty($data['videos'][0]['url'])) {
    $rawVideo = $data['videos'][0]['url'];
    $rawThumb = $data['thumbnail'] ?? ($data['thumb'] ?? null);
} elseif (!empty($data['formats'][0]['url'])) {
    $rawVideo = $data['formats'][0]['url'];
    $rawThumb = $data['thumbnail'] ?? null;
} elseif (!empty($data['download_url'])) {
    $rawVideo = $data['download_url'];
    $rawThumb = $data['thumbnail'] ?? null;
}

if ($rawVideo) {
    // Generar un nombre aleatorio seguro para la descarga
    $randomName = "SnapSave_" . substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
    $videoProxy = "download.php?url=" . urlencode($rawVideo) . "&filename=" . $randomName . ".mp4";
    $thumbProxy = $rawThumb ? "download.php?url=" . urlencode($rawThumb) . "&isImage=true" : null;
    
    echo json_encode([
        'status' => true,
        'platform' => $platform,
        'result' => [[
            'url' => $videoProxy,
            'thumb' => $thumbProxy,
            'raw_video' => $rawVideo,
            'type' => 'video/mp4'
        ]]
    ]);
} else {
    $errorMsg = $data['error'] ?? ($data['message'] ?? 'Could not find a downloadable video stream for this link.');
    echo json_encode([
        'status' => false,
        'error' => $errorMsg,
        'debug' => 'Response received but no compatible video URL detected.'
    ]);
}

