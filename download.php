<?php
// Prevenir cualquier salida accidental antes de enviar el stream
while (ob_get_level()) {
    @ob_end_clean();
}

@set_time_limit(0);

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
header('Access-Control-Expose-Headers: Content-Length, Content-Range, Accept-Ranges');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$url = $_GET['url'] ?? '';
$rawFilename = $_GET['filename'] ?? '';
$isImage = isset($_GET['isImage']) && $_GET['isImage'] !== 'false';
$isInline = !empty($_GET['inline']) || $isImage;

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
$port = isset($parsedUrl['port']) ? (int)$parsedUrl['port'] : null;

if (!$host) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    die("Error 400: Invalid host in URL.");
}

// Restricción de puertos para prevenir port scanning interno
if ($port !== null && !in_array($port, [80, 443, 8080, 8443], true)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    die("Error 403: Forbidden port specified.");
}

// Función auxiliar para validar si un host o IP es seguro (no interno/privado)
function isHostSafe(string $checkHost): bool {
    $blocked = [
        'localhost', '127.0.0.1', '::1', '0.0.0.0',
        '169.254.169.254', 'metadata.google.internal',
        'instance-data'
    ];
    if (in_array($checkHost, $blocked, true)) {
        return false;
    }
    if (str_ends_with($checkHost, '.local') || str_ends_with($checkHost, '.internal') || str_ends_with($checkHost, '.localhost')) {
        return false;
    }
    
    // Resolver IPs del host y verificar rangos privados y reservados
    $resolvedIps = @gethostbynamel($checkHost);
    if ($resolvedIps === false || empty($resolvedIps)) {
        $singleIp = @gethostbyname($checkHost);
        if ($singleIp && $singleIp !== $checkHost) {
            $resolvedIps = [$singleIp];
        }
    }
    
    if (!empty($resolvedIps)) {
        foreach ($resolvedIps as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return false;
                }
            }
        }
    }
    return true;
}

if (!isHostSafe($host)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    die("Error 403: Access to private or reserved network resources is forbidden.");
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
    $cleanFilename = "OmniClip_" . $randomStr . $defaultExt;
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

// Reenviar Range header si el cliente lo envía (soporte de seek y pause/resume en reproductor HTML5)
if (!empty($_SERVER['HTTP_RANGE'])) {
    $requestHeaders[] = 'Range: ' . $_SERVER['HTTP_RANGE'];
}

$headersSent = false;
$upstreamStatus = 200;
$isHeadRequest = (($_SERVER['REQUEST_METHOD'] ?? '') === 'HEAD');

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
    CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
    CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
    CURLOPT_NOBODY => $isHeadRequest,
    CURLOPT_HTTPHEADER => $requestHeaders,
    CURLOPT_HEADERFUNCTION => function ($curl, $headerLine) use (&$headersSent, &$upstreamStatus, $isImage, $isInline, $cleanFilename) {
        $len = strlen($headerLine);
        $trimmed = trim($headerLine);

        if (preg_match('/^HTTP\/[12]\.?\d?\s+(\d+)/i', $trimmed, $matches)) {
            $upstreamStatus = (int)$matches[1];
            // No enviar cabeceras definitivas si es una redirección intermedia
            if ($upstreamStatus >= 300 && $upstreamStatus < 400) {
                return $len;
            }
        }

        // Validar destino de redirección para evitar SSRF en saltos
        if (stripos($trimmed, 'Location:') === 0) {
            $redirUrl = trim(substr($trimmed, 9));
            $parsedRedir = parse_url($redirUrl);
            if (!empty($parsedRedir['host']) && !isHostSafe(strtolower($parsedRedir['host']))) {
                return 0; // Aborta cURL si redirige a red interna
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
            
            if ($isInline) {
                // Modo inline para previsualización directa en el reproductor HTML5
                header('Content-Disposition: inline; filename="' . $cleanFilename . '"');
                header('Cache-Control: public, max-age=3600');
            } else {
                // Modo descarga de archivo
                header('Content-Disposition: attachment; filename="' . $cleanFilename . '"');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: no-cache, no-store, must-revalidate');
                header('Pragma: no-cache');
                header('Expires: 0');
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
    CURLOPT_WRITEFUNCTION => function ($curl, $data) use (&$headersSent, &$upstreamStatus, $isImage, $isInline, $cleanFilename) {
        if ($upstreamStatus >= 400) {
            echo "Error upstream ($upstreamStatus): Unable to retrieve media stream.";
            return strlen($data);
        }

        if (!$headersSent) {
            http_response_code(200);
            header("Content-Type: " . ($isImage ? "image/jpeg" : "video/mp4"));
            header('Content-Disposition: ' . ($isInline ? 'inline' : 'attachment') . '; filename="' . $cleanFilename . '"');
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
    error_log("OmniClip Download cURL Error: " . $curlErr);
}
exit;
