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
    <title><?php echo $t['terms_title']; ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                        display: ["Plus Jakarta Sans", "sans-serif"],
                    },
                    colors: {
                        primary: '#ff8aa5',
                        surface: '#0e0e0e',
                        'surface-container-highest': '#262626',
                        'on-surface': '#ffffff',
                        'on-surface-variant': '#adaaaa',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0e0e0e; color: #ffffff; }
    </style>
</head>
<body class="p-8 md:p-24 font-sans bg-surface text-on-surface">
    <div class="max-w-4xl mx-auto space-y-8">
        <a href="index.php" class="text-primary hover:underline font-bold"><?php echo $t['privacy_back']; ?></a>
        <h1 class="text-4xl font-display font-extrabold text-on-surface"><?php echo $t['terms_header']; ?></h1>
        <div class="text-on-surface-variant space-y-4">
            <p><?php echo $t['terms_p1']; ?></p>
            <h2 class="text-2xl font-display font-bold text-on-surface"><?php echo $t['terms_h2']; ?></h2>
            <p><?php echo $t['terms_p2']; ?></p>
        </div>
    </div>
</body>
</html>
