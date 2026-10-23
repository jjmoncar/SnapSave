<?php
session_start();
require_once 'languages.php';

$default_lang = 'en';

if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $languages)) {
    $_SESSION['lang'] = $_GET['lang'];
}

$current_lang = $_SESSION['lang'] ?? $default_lang;
if (!isset($languages[$current_lang])) {
    $current_lang = $default_lang;
}
$t = $languages[$current_lang];
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_lang); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($t['terms_title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($t['meta_desc']); ?>">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ff8aa5'><path d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14l-4-4 1.41-1.41L11 13.17l6.59-6.59L19 8l-8 8z'/></svg>">

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
                        secondary: '#ffb147',
                        surface: '#0e0e0e',
                        'surface-container-lowest': '#000000',
                        'surface-container-low': '#131313',
                        'surface-container': '#1a1a19',
                        'surface-container-high': '#20201f',
                        'surface-container-highest': '#262626',
                        'surface-bright': '#2c2c2c',
                        'on-surface': '#ffffff',
                        'on-surface-variant': '#adaaaa',
                        'outline-variant': '#484847',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0e0e0e;
            color: #ffffff;
            min-height: 100vh;
        }

        .glass-morphism {
            background: rgba(26, 26, 25, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .ambient-shadow {
            box-shadow: 0 40px 60px rgba(255, 138, 165, 0.06);
        }

        .lang-dropdown {
            background: rgba(38, 38, 38, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>

<body class="font-sans bg-surface text-on-surface flex flex-col min-h-screen">
    <!-- Navbar -->
    <nav class="p-6">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <span class="text-2xl font-bold tracking-widest text-on-surface uppercase font-display">
                <a href="index.php" class="hover:text-primary transition-colors">SnapSave<span class="text-primary">.</span></a>
            </span>
            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-sm font-semibold text-primary hover:brightness-125 transition-all flex items-center gap-1.5">
                    <?php echo htmlspecialchars($t['back_to_home']); ?>
                </a>

                <!-- Language Selector -->
                <div class="relative group">
                    <button class="flex items-center gap-2 bg-surface-container hover:bg-surface-container-high px-3.5 py-1.5 rounded-xl transition-all text-xs font-semibold text-on-surface">
                        <span class="uppercase"><?php echo htmlspecialchars($current_lang); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-36 rounded-xl lang-dropdown opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 ambient-shadow overflow-hidden shadow-2xl">
                        <a href="?lang=en" class="flex items-center gap-2 px-4 py-2.5 text-xs text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors <?php echo $current_lang === 'en' ? 'text-primary font-bold' : ''; ?>">🇺🇸 English</a>
                        <a href="?lang=es" class="flex items-center gap-2 px-4 py-2.5 text-xs text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors <?php echo $current_lang === 'es' ? 'text-primary font-bold' : ''; ?>">🇪🇸 Español</a>
                        <a href="?lang=pt" class="flex items-center gap-2 px-4 py-2.5 text-xs text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors <?php echo $current_lang === 'pt' ? 'text-primary font-bold' : ''; ?>">🇧🇷 Português</a>
                        <a href="?lang=fr" class="flex items-center gap-2 px-4 py-2.5 text-xs text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors <?php echo $current_lang === 'fr' ? 'text-primary font-bold' : ''; ?>">🇫🇷 Français</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-4xl mx-auto px-6 py-12 w-full">
        <div class="space-y-8">
            <div>
                <h1 class="text-4xl md:text-5xl font-display font-extrabold text-on-surface tracking-tight mb-4">
                    <?php echo htmlspecialchars($t['terms_header']); ?><span class="text-primary">.</span>
                </h1>
                <p class="text-base text-on-surface-variant leading-relaxed">
                    <?php echo htmlspecialchars($t['terms_intro']); ?>
                </p>
            </div>

            <div class="space-y-6">
                <!-- Section 1 -->
                <div class="glass-morphism rounded-2xl p-6 ambient-shadow">
                    <h2 class="text-xl font-display font-bold text-on-surface mb-2"><?php echo htmlspecialchars($t['terms_h1']); ?></h2>
                    <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo htmlspecialchars($t['terms_p1']); ?></p>
                </div>

                <!-- Section 2 -->
                <div class="glass-morphism rounded-2xl p-6 ambient-shadow">
                    <h2 class="text-xl font-display font-bold text-on-surface mb-2"><?php echo htmlspecialchars($t['terms_h2']); ?></h2>
                    <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo htmlspecialchars($t['terms_p2']); ?></p>
                </div>

                <!-- Section 3 -->
                <div class="glass-morphism rounded-2xl p-6 ambient-shadow">
                    <h2 class="text-xl font-display font-bold text-on-surface mb-2"><?php echo htmlspecialchars($t['terms_h3']); ?></h2>
                    <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo htmlspecialchars($t['terms_p3']); ?></p>
                </div>

                <!-- Section 4 -->
                <div class="glass-morphism rounded-2xl p-6 ambient-shadow">
                    <h2 class="text-xl font-display font-bold text-on-surface mb-2"><?php echo htmlspecialchars($t['terms_h4']); ?></h2>
                    <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo htmlspecialchars($t['terms_p4']); ?></p>
                </div>

                <!-- Section 5 -->
                <div class="glass-morphism rounded-2xl p-6 ambient-shadow">
                    <h2 class="text-xl font-display font-bold text-on-surface mb-2"><?php echo htmlspecialchars($t['terms_h5']); ?></h2>
                    <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo htmlspecialchars($t['terms_p5']); ?></p>
                </div>

                <!-- Section 6 -->
                <div class="glass-morphism rounded-2xl p-6 ambient-shadow">
                    <h2 class="text-xl font-display font-bold text-on-surface mb-2"><?php echo htmlspecialchars($t['terms_h6']); ?></h2>
                    <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo htmlspecialchars($t['terms_p6']); ?></p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-10 px-6 mt-16 bg-surface-container-lowest border-t border-surface-container-low">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-on-surface-variant text-xs text-center md:text-left"><?php echo htmlspecialchars($t['footer_copyright']); ?></p>
            <div class="flex gap-6 text-xs text-on-surface-variant">
                <a href="terms.php" class="text-primary font-bold transition-colors"><?php echo htmlspecialchars($t['footer_terms']); ?></a>
                <a href="privacy.php" class="hover:text-on-surface transition-colors"><?php echo htmlspecialchars($t['footer_privacy']); ?></a>
                <a href="mailto:emprendedordigital1970@gmail.com" class="hover:text-on-surface transition-colors"><?php echo htmlspecialchars($t['footer_contact']); ?></a>
            </div>
        </div>
    </footer>
</body>

</html>

