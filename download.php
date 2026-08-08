<?php
// Prevenir cualquier salida accidental antes de enviar el stream
if (ob_get_level()) {
    @ob_end_clean();
}

$url = $_GET['url'] ?? '';
$rawFilename = $_GET['filename'] ?? '';
$isImage = isset($_GET['isImage']);

if (!$url) {
    http_response_code(400);
    die("URL is required");
}

if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $url)) {
    http_response_code(400);
    die("Invalid URL provided");
}

// Validación SSRF: Verificar Host e IP
$parsedUrl = parse_url($url);
$host = $parsedUrl['host'] ?? '';

if (!$host) {
    http_response_code(400);
    die("Invalid host in URL");
}

// Resolver IP del host y bloquear IPs privadas o de loopback
$ip = gethostbyname($host);
if (filter_var($ip, FILTER_VALIDATE_IP)) {
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        http_response_code(403);
        die("Access to private/reserved network resources is blocked");
    }
} else {
    http_response_code(400);
    die("Could not resolve host");
}

// Sanitizar nombre de archivo contra Header Injection y Path Traversal
$defaultExt = $isImage ? '.jpg' : '.mp4';
$cleanFilename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $rawFilename);
if (!$cleanFilename) {
    $randomStr = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 12);
    $cleanFilename = "SnapSave_" . $randomStr . $defaultExt;
} else {
    // Garantizar que termine con la extensión correcta si no tiene una
    if (!preg_match('/\.(mp4|mov|avi|mkv|jpg|jpeg|png|webp)$/i', $cleanFilename)) {
        $cleanFilename .= $defaultExt;
    }
}

// Configurar cabeceras de respuesta
header("Content-Type: " . ($isImage ? "image/jpeg" : "video/mp4"));

if (!$isImage) {
    header('Content-Disposition: attachment; filename="' . $cleanFilename . '"');
    header("Content-Transfer-Encoding: binary");
} else {
    header("Cache-Control: public, max-age=86400");
}

// Usar cURL para transmitir el archivo directamente al cliente (Streaming seguro)
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 5,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 120,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
]);

curl_exec($ch);
$curlErr = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    error_log("SnapSave Download cURL Error: " . $curlErr);
}
exit;

