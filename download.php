<?php
$url = $_GET['url'] ?? '';
$filename = $_GET['filename'] ?? (substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 12) . ".mp4");
$isImage = isset($_GET['isImage']);

if (!$url) {
    die("URL is required");
}

if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $url)) {
    die("Invalid URL");
}

// Configurar cabeceras según el tipo de contenido
header("Content-Type: " . ($isImage ? "image/jpeg" : "video/mp4"));

if (!$isImage) {
    header("Content-Disposition: attachment; filename=\"$filename\"");
} else {
    header("Cache-Control: public, max-age=86400");
}

// Usar cURL para transmitir el archivo directamente al cliente (Streaming)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // No guardar en memoria, enviar al buffer de salida
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36');
curl_exec($ch);
curl_close($ch);
