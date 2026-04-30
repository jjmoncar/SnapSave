<?php
session_start();
require_once 'languages.php';
$default_lang = 'en';
$current_lang = $_SESSION['lang'] ?? $default_lang;
$t = $languages[$current_lang];
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['privacy_title']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #0a0a0a; color: white; }
    </style>
</head>
<body class="p-8 md:p-24">
    <div class="max-w-4xl mx-auto space-y-8">
        <a href="index.php" class="text-pink-400 hover:underline font-bold"><?php echo $t['privacy_back']; ?></a>
        <h1 class="text-4xl font-extrabold"><?php echo $t['privacy_header']; ?></h1>
        <div class="text-slate-400 space-y-4">
            <p><?php echo $t['privacy_p1']; ?></p>
            <h2 class="text-2xl font-bold text-white"><?php echo $t['privacy_h2']; ?></h2>
            <p><?php echo $t['privacy_p2']; ?></p>
        </div>
    </div>
</body>
</html>
