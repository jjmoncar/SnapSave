<?php
header('Content-Type: application/json');

// Configuración de RapidAPI
$RAPIDAPI_KEY = "d374ccf789mshf6309148d5d9351p1935e0jsnd3b3dc8d455e";
$RAPIDAPI_HOST = "social-media-video-downloader.p.rapidapi.com";

$input = json_decode(file_get_contents('php://input'), true);
$url = $input['url'] ?? '';

if (!$url) {
    echo json_encode(['error' => 'URL is required']);
    exit;
}

$platform = '';
$endpoint = '';

// Detección de Plataforma
if (strpos($url, 'instagram.com') !== false) {
    $platform = 'instagram';
    $urlParts = explode('/', explode('?', $url)[0]);
    $shortcode = '';
    // Buscar el shortcode después de /p/, /reel/ o /reels/
    foreach ($urlParts as $index => $part) {
        if ($index > 0 && ($urlParts[$index-1] === 'p' || $urlParts[$index-1] === 'reel' || $urlParts[$index-1] === 'reels')) {
            $shortcode = $part;
            break;
        }
    }
    if (!$shortcode) $shortcode = end($urlParts) ?: prev($urlParts);
    $endpoint = "/instagram/v3/media/post/details?shortcode=$shortcode&renderableFormats=720p,highres";
} elseif (strpos($url, 'tiktok.com') !== false) {
    $platform = 'tiktok';
    $endpoint = "/tiktok/v3/post/details?url=" . urlencode(explode('?', $url)[0]);
} elseif (strpos($url, 'facebook.com') !== false || strpos($url, 'fb.watch') !== false) {
    $platform = 'facebook';
    $endpoint = "/facebook/v3/post/details?url=" . urlencode(explode('?', $url)[0]);
} else {
    echo json_encode(['error' => 'Plataforma no soportada aún.']);
    exit;
}

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://$RAPIDAPI_HOST$endpoint",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: $RAPIDAPI_HOST",
        "X-RapidAPI-Key: $RAPIDAPI_KEY"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(['error' => 'cURL Error: ' . $err]);
} else {
    $data = json_decode($response, true);
    
    // Mapeo unificado para v3
    $contents = $data['contents'][0] ?? null;
    $rawVideo = $contents['videos'][0]['url'] ?? null;
    $rawThumb = $data['metadata']['thumbnailUrl'] ?? ($contents['thumbnailUrl'] ?? null);

    if ($rawVideo) {
        $videoProxy = "download.php?url=" . urlencode($rawVideo) . "&filename=SnapSave_$platform.mp4";
        $thumbProxy = $rawThumb ? "download.php?url=" . urlencode($rawThumb) . "&isImage=true" : null;
        
        echo json_encode([
            'status' => true,
            'result' => [['url' => $videoProxy, 'thumb' => $thumbProxy, 'type' => 'video/mp4']]
        ]);
    } else {
        echo json_encode([
            'error' => 'No se pudo encontrar un link de descarga.',
            'debug' => 'La API respondió pero no se detectó un video compatible.'
        ]);
    }
}
