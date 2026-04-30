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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    <meta name="description" content="<?php echo $t['meta_desc']; ?>">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        outfit: ["Outfit", "sans-serif"],
                    },
                    colors: {
                        primary: '#ff8aa5',
                        secondary: '#ffb147',
                        surface: '#0a0a0a',
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            font-family: 'Outfit', sans-serif;
            background-color: #0a0a0a;
            color: #f8fafc;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(circle at top right, #1e1515 0%, #0e0e0e 50%, #050505 100%);
        }

        .glass-morphism {
            background: rgba(26, 26, 26, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
        }

        .glow-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .glow-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px rgba(255, 138, 165, 0.3);
        }

        .input-glow:focus {
            box-shadow: 0 0 20px rgba(255, 138, 165, 0.1);
            border-color: rgba(255, 138, 165, 0.2);
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
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="overflow-x-hidden font-outfit">
    <!-- Navbar -->
    <nav class="p-6">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <span class="text-2xl font-bold tracking-tight text-white uppercase tracking-widest">Capture</span>
            <div class="flex items-center space-x-4 md:space-x-8">
                <nav class="hidden lg:flex space-x-8">
                    <a href="#" class="text-white hover:text-primary transition-colors font-medium"><?php echo $t['nav_facebook']; ?></a>
                    <a href="#" class="text-white hover:text-primary transition-colors font-medium"><?php echo $t['nav_tiktok']; ?></a>
                    <a href="#" class="text-primary font-bold border-b-2 border-primary"><?php echo $t['nav_instagram']; ?></a>
                </nav>
                
                <!-- Language Selector -->
                <div class="relative group">
                    <button class="flex items-center gap-2 bg-slate-800/50 hover:bg-slate-700/50 px-4 py-2 rounded-xl border border-slate-700/50 transition-all text-sm font-medium text-white">
                        <span class="uppercase"><?php echo $current_lang; ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-32 rounded-xl lang-dropdown opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 shadow-2xl overflow-hidden border border-white/5">
                        <a href="?lang=en" class="block px-4 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-primary transition-colors border-b border-white/5">🇺🇸 English</a>
                        <a href="?lang=es" class="block px-4 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-primary transition-colors border-b border-white/5">🇪🇸 Español</a>
                        <a href="?lang=pt" class="block px-4 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-primary transition-colors border-b border-white/5">🇧🇷 Português</a>
                        <a href="?lang=fr" class="block px-4 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-primary transition-colors">🇫🇷 Français</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-6 pt-24 pb-24">
        <!-- Header -->
        <div class="text-center mb-16 space-y-4">
            <h1 class="text-6xl md:text-8xl font-extrabold text-white tracking-tighter leading-tight">
                <?php echo $t['header_title']; ?><span class="text-white">.</span>
            </h1>
            <p class="text-lg text-slate-400 max-w-2xl mx-auto font-medium">
                <?php echo $t['header_desc']; ?>
            </p>
        </div>

        <!-- Download Card -->
        <div class="glass-morphism rounded-3xl p-8 md:p-12 mb-8 relative">
            <div class="space-y-6">
                <div class="space-y-4">
                    <input type="text" id="urlInput" placeholder="<?php echo $t['input_placeholder']; ?>"
                        class="w-full bg-slate-800/50 border border-slate-700/50 rounded-2xl py-5 px-6 text-white placeholder:text-slate-500 focus:outline-none input-glow transition-all">
                    <div class="flex justify-end">
                        <button id="analyzeBtn"
                            class="bg-gradient-to-r from-primary to-secondary hover:brightness-110 text-surface font-extrabold py-4 px-12 text-xl rounded-xl transition-all glow-btn shadow-lg shadow-primary/20 uppercase tracking-tight">
                            <?php echo $t['analyze_btn']; ?>
                        </button>
                    </div>
                </div>

                <div id="ad-banner-1"
                    class="w-full h-32 bg-slate-800/20 border border-dashed border-slate-700 rounded-xl flex items-center justify-center text-slate-600 text-sm italic">
                    <script>
                        atOptions = {
                            'key': 'f88f153afa3d18c40ae849fc4ba1ece9',
                            'format': 'iframe',
                            'height': 90,
                            'width': 728,
                            'params': {}
                        };
                    </script>
                    <script src="https://www.highperformanceformat.com/f88f153afa3d18c40ae849fc4ba1ece9/invoke.js"></script>
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
        <div id="resultCard" class="hidden glass-morphism rounded-3xl p-8 overflow-hidden animate-fade-in">
            <div class="flex flex-col md:flex-row gap-8 items-center">
                <div class="w-full md:w-1/3 aspect-square rounded-2xl bg-slate-800 overflow-hidden relative shadow-2xl">
                    <img id="videoThumb" src="" alt="Thumbnail" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                </div>
                <div class="w-full md:w-2/3 space-y-6">
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-2"><?php echo $t['ready_to_download']; ?></h3>
                        <p class="text-slate-400"><?php echo $t['quality']; ?></p>
                    </div>

                    <a id="downloadLink" href="#" target="_blank" class="block w-full">
                        <button
                            class="w-full bg-gradient-to-r from-primary to-secondary hover:brightness-110 text-black font-bold py-5 rounded-2xl transition-all glow-btn shadow-lg shadow-primary/20 flex items-center justify-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <?php echo $t['download_now']; ?>
                        </button>
                    </a>

                    <div id="ad-banner-2"
                        class="w-full h-24 bg-slate-800/20 border border-dashed border-slate-700 rounded-xl flex items-center justify-center text-slate-600 text-sm italic">
                        <script>
                            atOptions = {
                                'key': 'dd299b46bfe1dbaf276b2827e60c30d4',
                                'format': 'iframe',
                                'height': 60,
                                'width': 468,
                                'params': {}
                            };
                        </script>
                        <script src="https://www.highperformanceformat.com/dd299b46bfe1dbaf276b2827e60c30d4/invoke.js"></script>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="grid md:grid-cols-3 gap-6 mt-20">
            <div class="glass-morphism p-6 rounded-2xl space-y-4">
                <div class="w-12 h-12 bg-pink-500/10 text-primary rounded-lg flex items-center justify-center font-bold">01</div>
                <h4 class="text-lg font-bold text-white"><?php echo $t['feature_1_title']; ?></h4>
                <p class="text-slate-400 text-sm leading-relaxed"><?php echo $t['feature_1_desc']; ?></p>
            </div>
            <div class="glass-morphism p-6 rounded-2xl space-y-4">
                <div class="w-12 h-12 bg-orange-500/10 text-secondary rounded-lg flex items-center justify-center font-bold">02</div>
                <h4 class="text-lg font-bold text-white"><?php echo $t['feature_2_title']; ?></h4>
                <p class="text-slate-400 text-sm leading-relaxed"><?php echo $t['feature_2_desc']; ?></p>
            </div>
            <div class="glass-morphism p-6 rounded-2xl space-y-4">
                <div class="w-12 h-12 bg-pink-500/10 text-primary rounded-lg flex items-center justify-center font-bold">03</div>
                <h4 class="text-lg font-bold text-white"><?php echo $t['feature_3_title']; ?></h4>
                <p class="text-slate-400 text-sm leading-relaxed"><?php echo $t['feature_3_desc']; ?></p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-12 px-6">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <p class="text-slate-500 text-sm"><?php echo $t['footer_copyright']; ?></p>
            <div class="flex gap-6 text-sm text-slate-400">
                <a href="terms.php" class="hover:text-white"><?php echo $t['footer_terms']; ?></a>
                <a href="privacy.php" class="hover:text-white"><?php echo $t['footer_privacy']; ?></a>
                <a href="mailto:emprendedordigital1970@gmail.com" class="hover:text-white"><?php echo $t['footer_contact']; ?></a>
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