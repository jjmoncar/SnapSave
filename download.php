<?php
// Prevenir cualquier salida accidental antes de enviar el stream
while (ob_get_level()) {
    @ob_end_clean();
}

@set_time_limit(0);

$url = $_GET['url'] ?? '';
$rawFilename = $_GET['filename'] ?? '';
$isImage = isset($_GET['isImage']) && $_GET['isImage'] !== 'false';

if (!$url) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    die("Error 400: URL parameter is required.");
}

if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $url)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    die("Error 400: Invalid URL format.");
}

// Validación SSRF: Verificar Host e IP
$parsedUrl = parse_url($url);
$host = strtolower($parsedUrl['host'] ?? '');

if (!$host) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    die("Error 400: Invalid host in URL.");
}

// Bloqueo explícito de hosts internos / loopback
$blockedHosts = ['localhost', '127.0.0.1', '::1', '0.0.0.0'];
if (in_array($host, $blockedHosts, true) || str_ends_with($host, '.local') || str_ends_with($host, '.internal')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    die("Error 403: Access to local network resources is forbidden.");
}

// Resolver IPs del host y verificar rangos privados/reservados
$resolvedIps = @gethostbynamel($host);
if ($resolvedIps === false || empty($resolvedIps)) {
    // Si gethostbynamel falla, intentar gethostbyname simple
    $singleIp = @gethostbyname($host);
    if ($singleIp && $singleIp !== $host) {
        $resolvedIps = [$singleIp];
    }
}

if (!empty($resolvedIps)) {
    foreach ($resolvedIps as $checkIp) {
        if (filter_var($checkIp, FILTER_VALIDATE_IP)) {
            if (!filter_var($checkIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                http_response_code(403);
                header('Content-Type: text/plain; charset=utf-8');
                die("Error 403: Access to private or reserved IP space is forbidden.");
            }
        }
    }
}

// Sanitizar nombre de archivo contra Header Injection y Path Traversal
$defaultExt = $isImage ? '.jpg' : '.mp4';
$cleanFilename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $rawFilename);
$cleanFilename = trim($cleanFilename, "._ \t\n\r");
if (strlen($cleanFilename) > 80) {
    $cleanFilename = substr($cleanFilename, 0, 80);
}

if (!$cleanFilename) {
    $randomStr = bin2hex(random_bytes(6));
    $cleanFilename = "SnapSave_" . $randomStr . $defaultExt;
} else {
    if (!preg_match('/\.(mp4|mov|avi|mkv|webm|jpg|jpeg|png|webp|gif)$/i', $cleanFilename)) {
        $cleanFilename .= $defaultExt;
    }
}

// Preparar cabeceras para la petición cURL
$requestHeaders = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
    'Accept: */*',
    'Accept-Language: en-US,en;q=0.9,es;q=0.8'
];

// Reenviar Range header si el cliente lo envía (soporte de seek y pause/resume)
if (!empty($_SERVER['HTTP_RANGE'])) {
    $requestHeaders[] = 'Range: ' . $_SERVER['HTTP_RANGE'];
}

$headersSent = false;
$upstreamStatus = 200;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 5,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 300,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_HTTPHEADER => $requestHeaders,
    CURLOPT_HEADERFUNCTION => function ($curl, $headerLine) use (&$headersSent, &$upstreamStatus, $isImage, $cleanFilename) {
        $len = strlen($headerLine);
        $trimmed = trim($headerLine);

        if (preg_match('/^HTTP\/[12]\.?\d?\s+(\d+)/i', $trimmed, $matches)) {
            $upstreamStatus = (int)$matches[1];
            // No enviar cabeceras definitivas si es una redirección intermedia
            if ($upstreamStatus >= 300 && $upstreamStatus < 400) {
                return $len;
            }
        }

        // Si el upstream devolvió error (4xx o 5xx)
        if ($upstreamStatus >= 400) {
            if (!$headersSent) {
                http_response_code($upstreamStatus);
                header('Content-Type: text/plain; charset=utf-8');
                $headersSent = true;
            }
            return $len;
        }

        // Al recibir la primera cabecera del cuerpo final
        if (!$headersSent) {
            http_response_code($upstreamStatus === 206 ? 206 : 200);
            
            if (!$isImage) {
                header('Content-Disposition: attachment; filename="' . $cleanFilename . '"');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: no-cache, no-store, must-revalidate');
                header('Pragma: no-cache');
                header('Expires: 0');
            } else {
                header('Cache-Control: public, max-age=86400');
            }
            $headersSent = true;
        }

        // Reenviar cabeceras relevantes del CDN
        if (stripos($trimmed, 'Content-Type:') === 0) {
            header($trimmed);
        } elseif (stripos($trimmed, 'Content-Length:') === 0) {
            header($trimmed);
        } elseif (stripos($trimmed, 'Content-Range:') === 0) {
            header($trimmed);
        } elseif (stripos($trimmed, 'Accept-Ranges:') === 0) {
            header($trimmed);
        }

        return $len;
    },
    CURLOPT_WRITEFUNCTION => function ($curl, $data) use (&$headersSent, &$upstreamStatus, $isImage, $cleanFilename) {
        if ($upstreamStatus >= 400) {
            echo "Error upstream ($upstreamStatus): Unable to retrieve media stream.";
            return strlen($data);
        }

        if (!$headersSent) {
            http_response_code(200);
            header("Content-Type: " . ($isImage ? "image/jpeg" : "video/mp4"));
            if (!$isImage) {
                header('Content-Disposition: attachment; filename="' . $cleanFilename . '"');
            }
            $headersSent = true;
        }

        echo $data;
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
        return strlen($data);
    }
]);

curl_exec($ch);
$curlErr = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    error_log("SnapSave Download cURL Error: " . $curlErr);
}
exit;


