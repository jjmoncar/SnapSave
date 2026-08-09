<?php
header('Content-Type: application/json; charset=utf-8');

// Configuración de RapidAPI Keys y Hosts
$RAPIDAPI_KEY = "d374ccf789mshf6309148d5d9351p1935e0jsnd3b3dc8d455e";
$SMVD_HOST = "social-media-video-downloader.p.rapidapi.com";
$YT_HOST = "yt-api.p.rapidapi.com";
$TWITTER_HOST = "twitter-x-video-downloader-api.p.rapidapi.com";

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
$apiUrl = '';
$apiHost = '';

// Detección por Plataforma
if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
    $platform = 'youtube';
    $videoId = '';
    
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches)) {
        $videoId = $matches[1];
    }
    
    if (!$videoId) {
        echo json_encode(['status' => false, 'error' => 'Could not extract valid YouTube video ID from the provided link.']);
        exit;
    }
    
    $apiHost = $YT_HOST;
    $apiUrl = "https://$YT_HOST/dl?id=" . urlencode($videoId);
} elseif (strpos($url, 'twitter.com') !== false || strpos($url, 'x.com') !== false) {
    $platform = 'twitter';
    $cleanUrl = strtok($url, '?');
    $apiHost = $TWITTER_HOST;
    $apiUrl = "https://$TWITTER_HOST/download?link=" . urlencode($cleanUrl) . "&url=" . urlencode($cleanUrl);
} elseif (strpos($url, 'instagram.com') !== false || strpos($url, 'instagr.am') !== false) {
    $platform = 'instagram';
    $shortcode = '';
    
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
    
    $apiHost = $SMVD_HOST;
    $apiUrl = "https://$SMVD_HOST/instagram/v3/media/post/details?shortcode=" . urlencode($shortcode) . "&renderableFormats=720p,highres";
} elseif (strpos($url, 'tiktok.com') !== false) {
    $platform = 'tiktok';
    $cleanUrl = strtok($url, '?');
    $apiHost = $SMVD_HOST;
    $apiUrl = "https://$SMVD_HOST/tiktok/v3/post/details?url=" . urlencode($cleanUrl);
} elseif (strpos($url, 'facebook.com') !== false || strpos($url, 'fb.watch') !== false || strpos($url, 'fb.gg') !== false) {
    $platform = 'facebook';
    $cleanUrl = strtok($url, '?');
    $apiHost = $SMVD_HOST;
    $apiUrl = "https://$SMVD_HOST/facebook/v3/post/details?url=" . urlencode($cleanUrl);
} else {
    echo json_encode(['status' => false, 'error' => 'Unsupported platform. Please provide a Twitter (X), Instagram, TikTok, Facebook, or YouTube URL.']);
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
if (isset($data['success']) && $data['success'] === false) {
    $errMsg = $data['message'] ?? ($data['error'] ?? 'API request failed');
    echo json_encode(['status' => false, 'error' => 'API Error: ' . $errMsg]);
    exit;
}

if (isset($data['status']) && $data['status'] === false) {
    $errMsg = $data['message'] ?? ($data['error'] ?? 'API status false');
    echo json_encode(['status' => false, 'error' => 'API Error: ' . $errMsg]);
    exit;
}

if (isset($data['message']) && strtolower($data['message']) !== 'success' && strtolower($data['message']) !== 'ok' && !isset($data['data']) && !isset($data['medias']) && !isset($data['videos']) && !isset($data['contents']) && !isset($data['result']) && !isset($data['url']) && !isset($data['formats'])) {
    echo json_encode(['status' => false, 'error' => 'API Error: ' . $data['message']]);
    exit;
}

$rawVideo = null;
$rawThumb = null;

if ($platform === 'youtube') {
    // Procesamiento especializado para yt-api (ytjar)
    if (!empty($data['formats'])) {
        foreach ($data['formats'] as $fmt) {
            if (isset($fmt['url']) && strpos($fmt['mimeType'] ?? '', 'video/mp4') !== false) {
                $rawVideo = $fmt['url'];
                break;
            }
        }
        if (!$rawVideo && isset($data['formats'][0]['url'])) {
            $rawVideo = $data['formats'][0]['url'];
        }
    } elseif (!empty($data['adaptiveFormats'])) {
        foreach ($data['adaptiveFormats'] as $fmt) {
            if (isset($fmt['url']) && strpos($fmt['mimeType'] ?? '', 'video/mp4') !== false) {
                $rawVideo = $fmt['url'];
                break;
            }
        }
    }
    
    if (isset($data['thumbnail']) && is_array($data['thumbnail'])) {
        $lastThumb = end($data['thumbnail']);
        $rawThumb = $lastThumb['url'] ?? null;
    }
} elseif ($platform === 'twitter') {
    // Parseo para Twitter / X (twitter-x-video-downloader-api / EaseApi)
    $tData = $data['data'] ?? $data;
    
    if (!empty($tData['videos'])) {
        foreach ($tData['videos'] as $v) {
            if (!empty($v['url'])) {
                $rawVideo = $v['url'];
                break;
            }
        }
    }
    
    if (!$rawVideo && !empty($tData['medias'])) {
        foreach ($tData['medias'] as $m) {
            if (!empty($m['url'])) {
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
    } elseif (!empty($data['contents'][0]['url'])) {
        $rawVideo = $data['contents'][0]['url'];
        $rawThumb = $data['contents'][0]['thumbnailUrl'] ?? ($data['metadata']['thumbnailUrl'] ?? null);
    } elseif (!empty($data['result'])) {
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
    } elseif (!empty($data['medias'][0]['url'])) {
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
    }
}

if ($rawVideo) {
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


