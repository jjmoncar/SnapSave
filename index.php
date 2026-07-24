<?php
session_start();
require_once 'languages.php';

// Set default language
$default_lang = 'en';

// Check if language is set in URL
if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $languages)) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Get current language from session or default
$current_lang = $_SESSION['lang'] ?? $default_lang;
$t = $languages[$current_lang];
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">

<head>

    <script>(function (s) { s.dataset.zone = '10969272', s.src = 'https://al5sm.com/tag.min.js' })([document.documentElement, document.body].filter(Boolean).pop().appendChild(document.createElement('script')))</script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    <meta name="description" content="<?php echo $t['meta_desc']; ?>">

    <!-- Tailwind CSS CDN -->
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
        :root {
            background-color: #0e0e0e;
            color: #ffffff;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background-color: #0e0e0e;
        }

        .glass-morphism {
            background: rgba(26, 26, 25, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: none;
        }

        .ambient-shadow {
            box-shadow: 0 40px 60px rgba(255, 138, 165, 0.06);
        }

        .glow-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 20px 40px rgba(255, 138, 165, 0.1);
        }

        .glow-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 60px rgba(255, 138, 165, 0.2);
        }

        .input-glow:focus {
            background: rgba(38, 38, 38, 0.8);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .lang-dropdown {
            background: rgba(38, 38, 38, 0.95);
            backdrop-filter: blur(20px);
            border: none;
        }
    </style>
</head>

<body class="overflow-x-hidden font-sans bg-surface text-on-surface">
    <!-- Navbar -->
    <nav class="p-6">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <span class="text-2xl font-bold tracking-tight text-on-surface uppercase font-display tracking-widest"><a
                    href="https://omg10.com/4/10969248">Capture</a></span>
            <div class="flex items-center space-x-4 md:space-x-8">
                <nav class="hidden lg:flex space-x-8">
                    <a href="#"
                        class="text-white hover:text-primary transition-colors font-medium"><?php echo $t['nav_facebook']; ?></a>
                    <a href="#"
                        class="text-white hover:text-primary transition-colors font-medium"><?php echo $t['nav_tiktok']; ?></a>
                    <a href="#"
                        class="text-primary font-bold border-b-2 border-primary"><?php echo $t['nav_instagram']; ?></a>
                </nav>

                <!-- Language Selector -->
                <div class="relative group">
                    <button
                        class="flex items-center gap-2 bg-surface-container hover:bg-surface-container-high px-4 py-2 rounded-xl transition-all text-sm font-medium text-on-surface">
                        <span class="uppercase"><?php echo $current_lang; ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        class="absolute right-0 mt-2 w-32 rounded-xl lang-dropdown opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 ambient-shadow overflow-hidden ">
                        <a href="?lang=en"
                            class="block px-4 py-3 text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors ">🇺🇸
                            English</a>
                        <a href="?lang=es"
                            class="block px-4 py-3 text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors ">🇪🇸
                            Español</a>
                        <a href="?lang=pt"
                            class="block px-4 py-3 text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors ">🇧🇷
                            Português</a>
                        <a href="?lang=fr"
                            class="block px-4 py-3 text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors">🇫🇷
                            Français</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-6 pt-24 pb-24">
        <!-- Header -->
        <div class="text-center mb-16 space-y-4">
            <h1 class="text-6xl md:text-8xl font-display font-extrabold text-on-surface tracking-tighter leading-tight">
                <?php echo $t['header_title']; ?><span class="text-white">.</span>
            </h1>
            <p class="text-lg text-on-surface-variant max-w-2xl mx-auto font-medium">
                <?php echo $t['header_desc']; ?>
            </p>
        </div>

        <!-- Download Card -->
        <div class="glass-morphism rounded-3xl p-8 md:p-12 mb-8 relative ambient-shadow">
            <div class="space-y-6">
                <div class="space-y-4">
                    <input type="text" id="urlInput" placeholder="<?php echo $t['input_placeholder']; ?>"
                        class="w-full bg-surface-container-highest rounded-3xl py-5 px-6 text-on-surface placeholder:text-on-surface-variant focus:outline-none input-glow transition-all backdrop-blur-md">
                    <div class="flex justify-end">
                        <button id="analyzeBtn"
                            class="bg-gradient-to-r from-primary to-secondary hover:brightness-110 text-surface font-extrabold py-4 px-12 text-xl rounded-full transition-all glow-btn uppercase tracking-tight">
                            <?php echo $t['analyze_btn']; ?>
                        </button>
                    </div>
                </div>

                <div id="ad-banner-1"
                    class="w-full h-32 bg-surface-container-low rounded-xl flex items-center justify-center text-on-surface-variant text-sm italic">
                    <script>
                        atOptions = {
                            'key': 'f88f153afa3d18c40ae849fc4ba1ece9',
                            'format': 'iframe',
                            'height': 90,
                            'width': 728,
                            'params': {}
                        };
                    </script>
                    <script
                        src="https://www.highperformanceformat.com/f88f153afa3d18c40ae849fc4ba1ece9/invoke.js"></script>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loading"
                class="hidden absolute inset-0 glass-morphism rounded-3xl flex flex-col items-center justify-center space-y-4 z-10">
                <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                <p class="text-slate-300 font-medium"><?php echo $t['processing']; ?></p>
            </div>
        </div>

        <!-- Result Section -->
        <div id="resultCard" class="hidden glass-morphism rounded-3xl p-8 overflow-hidden animate-fade-in ambient-shadow">
            <div class="flex flex-col md:flex-row gap-8 items-center">
                <div class="w-full md:w-1/3 aspect-square rounded-2xl bg-surface-container-highest overflow-hidden relative ambient-shadow">
                    <img id="videoThumb" src="" alt="Thumbnail" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                </div>
                <div class="w-full md:w-2/3 space-y-6">
                    <div>
                        <h3 class="text-2xl font-display font-bold text-on-surface mb-2"><?php echo $t['ready_to_download']; ?></h3>
                        <p class="text-on-surface-variant"><?php echo $t['quality']; ?></p>
                    </div>

                    <a id="downloadLink" href="#" target="_blank" class="block w-full">
                        <button
                            class="w-full bg-gradient-to-r from-primary to-secondary hover:brightness-110 text-surface font-bold py-5 rounded-full transition-all glow-btn flex items-center justify-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <?php echo $t['download_now']; ?>
                        </button>
                    </a>

                    <div id="ad-banner-2"
                        class="w-full h-24 bg-surface-container-low rounded-xl flex items-center justify-center text-on-surface-variant text-sm italic">
                        <script>
                            atOptions = {
                                'key': 'dd299b46bfe1dbaf276b2827e60c30d4',
                                'format': 'iframe',
                                'height': 60,
                                'width': 468,
                                'params': {}
                            };
                        </script>
                        <script
                            src="https://www.highperformanceformat.com/dd299b46bfe1dbaf276b2827e60c30d4/invoke.js"></script>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="grid md:grid-cols-3 gap-6 mt-20">
            <div class="glass-morphism p-8 rounded-3xl space-y-4 ambient-shadow">
                <div
                    class="w-12 h-12 bg-pink-500/10 text-primary rounded-lg flex items-center justify-center font-bold">
                    01</div>
                <h4 class="text-lg font-display font-bold text-on-surface"><?php echo $t['feature_1_title']; ?></h4>
                <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo $t['feature_1_desc']; ?></p>
            </div>
            <div class="glass-morphism p-8 rounded-3xl space-y-4 ambient-shadow">
                <div
                    class="w-12 h-12 bg-orange-500/10 text-secondary rounded-lg flex items-center justify-center font-bold">
                    02</div>
                <h4 class="text-lg font-display font-bold text-on-surface"><?php echo $t['feature_2_title']; ?></h4>
                <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo $t['feature_2_desc']; ?></p>
            </div>
            <div class="glass-morphism p-8 rounded-3xl space-y-4 ambient-shadow">
                <div
                    class="w-12 h-12 bg-pink-500/10 text-primary rounded-lg flex items-center justify-center font-bold">
                    03</div>
                <h4 class="text-lg font-display font-bold text-on-surface"><?php echo $t['feature_3_title']; ?></h4>
                <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo $t['feature_3_desc']; ?></p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-12 px-6 mt-16 bg-surface-container-lowest">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <p class="text-on-surface-variant text-sm"><?php echo $t['footer_copyright']; ?></p>
            <div class="flex gap-6 text-sm text-on-surface-variant">
                <a href="terms.php" class="hover:text-on-surface"><?php echo $t['footer_terms']; ?></a>
                <a href="privacy.php" class="hover:text-on-surface"><?php echo $t['footer_privacy']; ?></a>
                <a href="mailto:emprendedordigital1970@gmail.com"
                    class="hover:text-on-surface"><?php echo $t['footer_contact']; ?></a>
            </div>
        </div>
    </footer>

    <!-- JQuery and Script -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const translations = <?php echo json_encode($t); ?>;

        $(document).ready(function () {
            const $urlInput = $('#urlInput');
            const $analyzeBtn = $('#analyzeBtn');
            const $loading = $('#loading');
            const $resultCard = $('#resultCard');
            const $videoThumb = $('#videoThumb');
            const $downloadLink = $('#downloadLink');

            $analyzeBtn.on('click', function () {
                const url = $urlInput.val().trim();

                if (!url) {
                    alert(translations.js_alert_invalid_link);
                    return;
                }

                $loading.removeClass('hidden');
                $resultCard.addClass('hidden');

                $.ajax({
                    url: 'api.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ url: url }),
                    success: function (data) {
                        if (data.error) {
                            alert(translations.js_alert_prefix + (data.message || data.error));
                        } else {
                            const result = data.result[0];
                            $videoThumb.attr('src', result.thumb || 'https://via.placeholder.com/640x360.png?text=Preview+Not+Available');
                            $downloadLink.attr('href', result.url);
                            $resultCard.removeClass('hidden');
                            $('html, body').animate({
                                scrollTop: $resultCard.offset().top - 100
                            }, 500);
                        }
                    },
                    error: function (err) {
                        console.error("AJAX Error:", err);
                        alert(translations.js_alert_error);
                    },
                    complete: function () {
                        $loading.addClass('hidden');
                    }
                });
            });

            $urlInput.on('keypress', function (e) {
                if (e.which === 13) {
                    $analyzeBtn.click();
                }
            });
        });
    </script>
</body>

</html>