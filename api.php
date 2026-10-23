<?php
// Headers CORS y JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Configuración de RapidAPI Keys y Hosts (permite variable de entorno)
$RAPIDAPI_KEY = getenv('RAPIDAPI_KEY') ?: "d374ccf789mshf6309148d5d9351p1935e0jsnd3b3dc8d455e";
$SMVD_HOST = "social-media-video-downloader.p.rapidapi.com";
$YT_HOST = "yt-api.p.rapidapi.com";
$TWITTER_HOST = "twitter-x-video-downloader-api.p.rapidapi.com";

// Leer input desde JSON body, $_POST, $_GET o CLI
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    $input = [];
}

$url = trim($input['url'] ?? ($_POST['url'] ?? ($_GET['url'] ?? ($argv[1] ?? ''))));

// Limpieza básica de caracteres no deseados en la URL
$url = trim($url, " \t\n\r\0\x0B<>\"'`");

if (!$url) {
    echo json_encode([
        'status' => false,
        'error' => 'URL is required. Please paste a link to analyze.'
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $url)) {
    echo json_encode([
        'status' => false,
        'error' => 'Invalid URL provided. Please paste a valid web link starting with http:// or https://'
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

$platform = '';
$apiUrl = '';
$apiHost = '';
$parsedTitle = null;

// Detección por Plataforma
if (preg_match('/(?:youtube\.com|youtu\.be)/i', $url)) {
    $platform = 'youtube';
    $videoId = '';
    
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts|live)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches)) {
        $videoId = $matches[1];
    }
    
    if (!$videoId) {
        echo json_encode([
            'status' => false,
            'error' => 'Could not extract a valid YouTube video ID from the provided link.'
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $apiHost = $YT_HOST;
    $apiUrl = "https://$YT_HOST/dl?id=" . urlencode($videoId);
} elseif (preg_match('/(?:twitter\.com|x\.com|fixupx\.com|vxtwitter\.com)/i', $url)) {
    $platform = 'twitter';
    $cleanUrl = strtok($url, '?');
    $apiHost = $TWITTER_HOST;
    $apiUrl = "https://$TWITTER_HOST/download?link=" . urlencode($cleanUrl) . "&url=" . urlencode($cleanUrl);
} elseif (preg_match('/(?:instagram\.com|instagr\.am)/i', $url)) {
    $platform = 'instagram';
    $shortcode = '';
    
    if (preg_match('/(?:p|reel|reels|tv|share\/reel|share\/p)\/([A-Za-z0-9_-]+)/i', $url, $matches)) {
        $shortcode = $matches[1];
    } else {
        $cleanUrl = strtok($url, '?');
        $urlParts = array_values(array_filter(explode('/', $cleanUrl)));
        $shortcode = end($urlParts) ?: '';
    }

    if (!$shortcode) {
        echo json_encode([
            'status' => false,
            'error' => 'Could not extract valid Instagram post shortcode.'
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $apiHost = $SMVD_HOST;
    $apiUrl = "https://$SMVD_HOST/instagram/v3/media/post/details?shortcode=" . urlencode($shortcode) . "&renderableFormats=720p,highres";
} elseif (preg_match('/tiktok\.com/i', $url)) {
    $platform = 'tiktok';
    $cleanUrl = strtok($url, '?');
    $apiHost = $SMVD_HOST;
    $apiUrl = "https://$SMVD_HOST/tiktok/v3/post/details?url=" . urlencode($cleanUrl);
} elseif (preg_match('/(?:facebook\.com|fb\.watch|fb\.gg)/i', $url)) {
    $platform = 'facebook';
    $cleanUrl = strtok($url, '?');
    $apiHost = $SMVD_HOST;
    $apiUrl = "https://$SMVD_HOST/facebook/v3/post/details?url=" . urlencode($cleanUrl);
} else {
    echo json_encode([
        'status' => false,
        'error' => 'Unsupported platform. Please provide a Twitter (X), Instagram, TikTok, Facebook, or YouTube URL.'
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 25,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: $apiHost",
        "X-RapidAPI-Key: $RAPIDAPI_KEY",
        "Accept: application/json",
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) SnapSave/2.0"
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode([
        'status' => false,
        'error' => 'Connection error: ' . $err
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode($response, true);

if (!$data) {
    echo json_encode([
        'status' => false,
        'error' => 'Invalid API response format.',
        'http_code' => $httpCode
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

// Si la API devuelve error explícito
if (isset($data['success']) && $data['success'] === false) {
    $errMsg = $data['message'] ?? ($data['error'] ?? 'API request failed');
    echo json_encode(['status' => false, 'error' => 'API Error: ' . $errMsg]);
    exit;
}

if (isset($data['status']) && ($data['status'] === false || $data['status'] === 'fail')) {
    $errMsg = $data['message'] ?? ($data['error'] ?? 'API status false');
    echo json_encode(['status' => false, 'error' => 'API Error: ' . $errMsg]);
    exit;
}

if (isset($data['error']) && is_array($data['error']) && isset($data['error']['message'])) {
    echo json_encode(['status' => false, 'error' => $data['error']['message']]);
    exit;
}

$rawVideo = null;
$rawThumb = null;
$qualityLabel = 'HD (MP4)';

if ($platform === 'youtube') {
    $parsedTitle = $data['title'] ?? null;
    
    // Buscar el formato MP4 con mejor calidad con audio incluido
    if (!empty($data['formats'])) {
        foreach ($data['formats'] as $fmt) {
            if (!empty($fmt['url']) && strpos($fmt['mimeType'] ?? '', 'video/mp4') !== false) {
                $rawVideo = $fmt['url'];
                $qualityLabel = $fmt['qualityLabel'] ?? 'HD (MP4)';
                break;
            }
        }
        if (!$rawVideo && isset($data['formats'][0]['url'])) {
            $rawVideo = $data['formats'][0]['url'];
            $qualityLabel = $data['formats'][0]['qualityLabel'] ?? 'HD (MP4)';
        }
    } elseif (!empty($data['adaptiveFormats'])) {
        foreach ($data['adaptiveFormats'] as $fmt) {
            if (!empty($fmt['url']) && strpos($fmt['mimeType'] ?? '', 'video/mp4') !== false) {
                $rawVideo = $fmt['url'];
                $qualityLabel = $fmt['qualityLabel'] ?? 'HD (MP4)';
                break;
            }
        }
    }
    
    if (isset($data['thumbnail']) && is_array($data['thumbnail'])) {
        $lastThumb = end($data['thumbnail']);
        $rawThumb = $lastThumb['url'] ?? null;
    }
} elseif ($platform === 'twitter') {
    $tData = $data['data'] ?? $data;
    $parsedTitle = $tData['text'] ?? ($tData['title'] ?? ($tData['description'] ?? null));
    
    // Seleccionar el video de mayor resolución/bitrate si hay varios
    if (!empty($tData['videos'])) {
        $bestBitrate = -1;
        foreach ($tData['videos'] as $v) {
            if (!empty($v['url'])) {
                $bitrate = $v['bitrate'] ?? 0;
                if ($bitrate >= $bestBitrate) {
                    $bestBitrate = $bitrate;
                    $rawVideo = $v['url'];
                    if (!empty($v['quality'])) {
                        $qualityLabel = $v['quality'];
                    }
                }
            }
        }
    }
    
    if (!$rawVideo && !empty($tData['medias'])) {
        foreach ($tData['medias'] as $m) {
            if (!empty($m['url']) && ($m['type'] ?? '') !== 'photo') {
                $rawVideo = $m['url'];
                break;
            }
        }
    }
    
    if (!$rawVideo) {
        $rawVideo = $tData['video_url'] ?? ($tData['video'] ?? ($tData['url'] ?? null));
    }
    
    $rawThumb = $tData['thumbnail'] ?? ($tData['cover'] ?? ($tData['thumb'] ?? ($data['thumbnail'] ?? null)));
    if (is_array($rawThumb)) {
        $rawThumb = $rawThumb['url'] ?? (end($rawThumb)['url'] ?? null);
    }
} else {
    // Parseo multiesquema para Instagram, TikTok, Facebook
    if (!empty($data['contents'][0]['videos'][0]['url'])) {
        $rawVideo = $data['contents'][0]['videos'][0]['url'];
        $rawThumb = $data['metadata']['thumbnailUrl'] ?? ($data['contents'][0]['thumbnailUrl'] ?? null);
        $parsedTitle = $data['metadata']['caption'] ?? ($data['metadata']['title'] ?? null);
    } elseif (!empty($data['contents'][0]['url'])) {
        $rawVideo = $data['contents'][0]['url'];
        $rawThumb = $data['contents'][0]['thumbnailUrl'] ?? ($data['metadata']['thumbnailUrl'] ?? null);
        $parsedTitle = $data['metadata']['caption'] ?? null;
    } elseif (!empty($data['result'])) {
        if (is_array($data['result'])) {
            $firstResult = $data['result'][0] ?? null;
            if (is_array($firstResult)) {
                $rawVideo = $firstResult['url'] ?? ($firstResult['video'] ?? null);
                $rawThumb = $firstResult['thumb'] ?? ($firstResult['thumbnail'] ?? null);
                $parsedTitle = $firstResult['title'] ?? null;
            } elseif (is_string($firstResult)) {
                $rawVideo = $firstResult;
            }
        } elseif (is_string($data['result'])) {
            $rawVideo = $data['result'];
        }
    } elseif (!empty($data['medias'][0]['url'])) {
        $rawVideo = $data['medias'][0]['url'];
        $rawThumb = $data['medias'][0]['thumbnail'] ?? null;
        $parsedTitle = $data['medias'][0]['title'] ?? null;
    } elseif (!empty($data['links'][0]['url'])) {
        $rawVideo = $data['links'][0]['url'];
    } elseif (!empty($data['url'])) {
        $rawVideo = $data['url'];
        $rawThumb = $data['thumbnailUrl'] ?? ($data['thumb'] ?? null);
    } elseif (!empty($data['data']['video_url'])) {
        $rawVideo = $data['data']['video_url'];
        $rawThumb = $data['data']['cover'] ?? null;
        $parsedTitle = $data['data']['title'] ?? null;
    }
}

if ($rawVideo) {
    $randomSuffix = bin2hex(random_bytes(5));
    $safePlatform = preg_replace('/[^a-z0-9]/i', '', $platform ?: 'video');
    $cleanFilename = "SnapSave_{$safePlatform}_{$randomSuffix}.mp4";
    
    $videoProxy = "download.php?url=" . urlencode($rawVideo) . "&filename=" . urlencode($cleanFilename);
    $thumbProxy = $rawThumb ? "download.php?url=" . urlencode($rawThumb) . "&isImage=true" : null;
    
    echo json_encode([
        'status' => true,
        'platform' => $platform,
        'title' => $parsedTitle,
        'quality' => $qualityLabel,
        'result' => [[
            'url' => $videoProxy,
            'thumb' => $thumbProxy,
            'raw_video' => $rawVideo,
            'filename' => $cleanFilename,
            'quality' => $qualityLabel,
            'type' => 'video/mp4'
        ]]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} else {
    $errorMsg = $data['error'] ?? ($data['message'] ?? 'Could not find a downloadable video stream for this link. It may be private or unavailable.');
    echo json_encode([
        'status' => false,
        'error' => is_string($errorMsg) ? $errorMsg : 'Video stream unavailable.',
        'platform' => $platform
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}



