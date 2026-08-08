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

        /* Toast Animation */
        #toastContainer {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 100;
            pointer-events: none;
        }

        .toast-msg {
            pointer-events: auto;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
    </style>
</head>

<body class="overflow-x-hidden font-sans bg-surface text-on-surface">
    <!-- Navbar -->
    <nav class="p-6">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <span class="text-2xl font-bold tracking-tight text-on-surface uppercase font-display tracking-widest">
                <a href="index.php" class="hover:text-primary transition-colors">SnapSave</a>
            </span>
            <div class="flex items-center space-x-4 md:space-x-8">
                <!-- Nav Tabs -->
                <nav class="hidden lg:flex space-x-4">
                    <button type="button" data-platform="all"
                        class="nav-tab px-3 py-1.5 rounded-full text-sm font-medium transition-all text-primary font-bold bg-surface-container-high border border-primary/20">
                        <?php echo $t['nav_all']; ?>
                    </button>
                    <button type="button" data-platform="instagram"
                        class="nav-tab px-3 py-1.5 rounded-full text-sm font-medium transition-all text-on-surface-variant hover:text-primary">
                        <?php echo $t['nav_instagram']; ?>
                    </button>
                    <button type="button" data-platform="tiktok"
                        class="nav-tab px-3 py-1.5 rounded-full text-sm font-medium transition-all text-on-surface-variant hover:text-primary">
                        <?php echo $t['nav_tiktok']; ?>
                    </button>
                    <button type="button" data-platform="facebook"
                        class="nav-tab px-3 py-1.5 rounded-full text-sm font-medium transition-all text-on-surface-variant hover:text-primary">
                        <?php echo $t['nav_facebook']; ?>
                    </button>
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
                        class="absolute right-0 mt-2 w-36 rounded-xl lang-dropdown opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 ambient-shadow overflow-hidden">
                        <a href="?lang=en"
                            class="flex items-center gap-2 px-4 py-3 text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors <?php echo $current_lang === 'en' ? 'text-primary font-bold' : ''; ?>">🇺🇸
                            English</a>
                        <a href="?lang=es"
                            class="flex items-center gap-2 px-4 py-3 text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors <?php echo $current_lang === 'es' ? 'text-primary font-bold' : ''; ?>">🇪🇸
                            Español</a>
                        <a href="?lang=pt"
                            class="flex items-center gap-2 px-4 py-3 text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors <?php echo $current_lang === 'pt' ? 'text-primary font-bold' : ''; ?>">🇧🇷
                            Português</a>
                        <a href="?lang=fr"
                            class="flex items-center gap-2 px-4 py-3 text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors <?php echo $current_lang === 'fr' ? 'text-primary font-bold' : ''; ?>">🇫🇷
                            Français</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-6 pt-16 pb-24">
        <!-- Header -->
        <div class="text-center mb-12 space-y-4">
            <h1 class="text-6xl md:text-8xl font-display font-extrabold text-on-surface tracking-tighter leading-tight">
                <?php echo $t['header_title']; ?><span class="text-primary">.</span>
            </h1>
            <p class="text-lg text-on-surface-variant max-w-2xl mx-auto font-medium">
                <?php echo $t['header_desc']; ?>
            </p>
        </div>

        <!-- Download Card -->
        <div class="glass-morphism rounded-3xl p-6 md:p-10 mb-8 relative ambient-shadow">
            <div class="space-y-6">
                <div class="space-y-4">
                    <div class="relative flex items-center">
                        <input type="text" id="urlInput" placeholder="<?php echo $t['input_placeholder']; ?>"
                            class="w-full bg-surface-container-highest rounded-3xl py-5 pl-6 pr-28 text-on-surface placeholder:text-on-surface-variant focus:outline-none input-glow transition-all backdrop-blur-md text-base md:text-lg">
                        <div class="absolute right-3 flex items-center space-x-1">
                            <button type="button" id="clearBtn" title="<?php echo $t['btn_clear']; ?>"
                                class="hidden p-2.5 rounded-full text-on-surface-variant hover:text-on-surface hover:bg-surface-bright transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <button type="button" id="pasteBtn"
                                class="px-4 py-2 bg-surface-bright hover:bg-surface-container-high text-primary font-semibold text-xs md:text-sm rounded-full transition-all flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span><?php echo $t['btn_paste']; ?></span>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button id="analyzeBtn"
                            class="w-full md:w-auto bg-gradient-to-r from-primary to-secondary hover:brightness-110 text-surface font-extrabold py-4 px-12 text-xl rounded-full transition-all glow-btn uppercase tracking-tight flex items-center justify-center gap-2">
                            <span><?php echo $t['analyze_btn']; ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="ad-banner-1"
                    class="w-full min-h-[90px] bg-surface-container-low rounded-xl flex items-center justify-center text-on-surface-variant text-sm italic overflow-hidden">
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
                class="hidden absolute inset-0 glass-morphism rounded-3xl flex flex-col items-center justify-center space-y-4 z-10 backdrop-blur-xl">
                <div class="w-14 h-14 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                <p class="text-slate-200 font-semibold text-lg animate-pulse"><?php echo $t['processing']; ?></p>
            </div>
        </div>

        <!-- Result Section -->
        <div id="resultCard" class="hidden glass-morphism rounded-3xl p-8 overflow-hidden animate-fade-in ambient-shadow">
            <div class="flex flex-col md:flex-row gap-8 items-center">
                <div class="w-full md:w-1/3 aspect-square rounded-2xl bg-surface-container-highest overflow-hidden relative ambient-shadow flex items-center justify-center">
                    <img id="videoThumb" src="" alt="Thumbnail" class="w-full h-full object-cover"
                         onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23ff8aa5\' stroke-width=\'1.5\'><rect x=\'2\' y=\'2\' width=\'20\' height=\'20\' rx=\'5\'/><path d=\'M10 8l6 4-6 4V8z\'/></svg>';">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <span id="platformBadge" class="absolute top-3 left-3 bg-surface-container-high/90 text-primary font-bold text-xs uppercase px-3 py-1 rounded-full backdrop-blur-md">
                        Video
                    </span>
                </div>
                <div class="w-full md:w-2/3 space-y-6">
                    <div>
                        <h3 class="text-2xl font-display font-bold text-on-surface mb-2"><?php echo $t['ready_to_download']; ?></h3>
                        <p class="text-on-surface-variant font-medium"><?php echo $t['quality']; ?></p>
                    </div>

                    <div class="space-y-3">
                        <a id="downloadLink" href="#" target="_blank" class="block w-full">
                            <button
                                class="w-full bg-gradient-to-r from-primary to-secondary hover:brightness-110 text-surface font-bold py-5 rounded-full transition-all glow-btn flex items-center justify-center gap-3 text-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span><?php echo $t['download_now']; ?></span>
                            </button>
                        </a>

                        <div class="flex gap-3">
                            <button type="button" id="copyBtn"
                                class="flex-1 bg-surface-container-high hover:bg-surface-bright text-on-surface font-semibold py-3 px-4 rounded-xl transition-all flex items-center justify-center gap-2 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span><?php echo $t['btn_copy']; ?></span>
                            </button>
                            <button type="button" id="anotherBtn"
                                class="flex-1 bg-surface-container-high hover:bg-surface-bright text-on-surface-variant hover:text-on-surface font-semibold py-3 px-4 rounded-xl transition-all flex items-center justify-center gap-2 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span><?php echo $t['btn_another']; ?></span>
                            </button>
                        </div>
                    </div>

                    <div id="ad-banner-2"
                        class="w-full min-h-[60px] bg-surface-container-low rounded-xl flex items-center justify-center text-on-surface-variant text-sm italic overflow-hidden">
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
            <div class="glass-morphism p-8 rounded-3xl space-y-4 ambient-shadow">
                <div
                    class="w-12 h-12 bg-pink-500/10 text-primary rounded-xl flex items-center justify-center font-bold font-display text-lg">
                    01</div>
                <h4 class="text-lg font-display font-bold text-on-surface"><?php echo $t['feature_1_title']; ?></h4>
                <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo $t['feature_1_desc']; ?></p>
            </div>
            <div class="glass-morphism p-8 rounded-3xl space-y-4 ambient-shadow">
                <div
                    class="w-12 h-12 bg-orange-500/10 text-secondary rounded-xl flex items-center justify-center font-bold font-display text-lg">
                    02</div>
                <h4 class="text-lg font-display font-bold text-on-surface"><?php echo $t['feature_2_title']; ?></h4>
                <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo $t['feature_2_desc']; ?></p>
            </div>
            <div class="glass-morphism p-8 rounded-3xl space-y-4 ambient-shadow">
                <div
                    class="w-12 h-12 bg-pink-500/10 text-primary rounded-xl flex items-center justify-center font-bold font-display text-lg">
                    03</div>
                <h4 class="text-lg font-display font-bold text-on-surface"><?php echo $t['feature_3_title']; ?></h4>
                <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo $t['feature_3_desc']; ?></p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-12 px-6 mt-16 bg-surface-container-lowest border-t border-surface-container-low">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <p class="text-on-surface-variant text-sm text-center md:text-left"><?php echo $t['footer_copyright']; ?></p>
            <div class="flex gap-6 text-sm text-on-surface-variant">
                <a href="terms.php" class="hover:text-on-surface transition-colors"><?php echo $t['footer_terms']; ?></a>
                <a href="privacy.php" class="hover:text-on-surface transition-colors"><?php echo $t['footer_privacy']; ?></a>
                <a href="mailto:emprendedordigital1970@gmail.com"
                    class="hover:text-on-surface transition-colors"><?php echo $t['footer_contact']; ?></a>
            </div>
        </div>
    </footer>

    <!-- Toast Container -->
    <div id="toastContainer"></div>

    <!-- JQuery and Script -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const translations = <?php echo json_encode($t); ?>;

        function showToast(message, isError = false) {
            const toastId = 'toast-' + Date.now();
            const bgClass = isError ? 'bg-red-900/90 text-red-200 border-red-500/30' : 'bg-surface-bright/95 text-on-surface border-primary/30';
            const icon = isError 
                ? '<svg class="w-5 h-5 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                : '<svg class="w-5 h-5 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            
            const html = `
                <div id="${toastId}" class="toast-msg ${bgClass} glass-morphism border px-5 py-4 rounded-2xl shadow-2xl flex items-center gap-3 max-w-md mb-3 translate-y-4 opacity-0">
                    ${icon}
                    <span class="text-sm font-medium">${message}</span>
                </div>
            `;

            $('#toastContainer').append(html);
            setTimeout(() => {
                $(`#${toastId}`).removeClass('translate-y-4 opacity-0');
            }, 50);

            setTimeout(() => {
                $(`#${toastId}`).addClass('translate-y-4 opacity-0');
                setTimeout(() => { $(`#${toastId}`).remove(); }, 300);
            }, 4000);
        }

        $(document).ready(function () {
            const $urlInput = $('#urlInput');
            const $analyzeBtn = $('#analyzeBtn');
            const $clearBtn = $('#clearBtn');
            const $pasteBtn = $('#pasteBtn');
            const $loading = $('#loading');
            const $resultCard = $('#resultCard');
            const $videoThumb = $('#videoThumb');
            const $downloadLink = $('#downloadLink');
            const $copyBtn = $('#copyBtn');
            const $anotherBtn = $('#anotherBtn');
            const $platformBadge = $('#platformBadge');
            let currentDownloadUrl = '';

            // Toggle clear button visibility
            $urlInput.on('input', function () {
                if ($(this).val().length > 0) {
                    $clearBtn.removeClass('hidden');
                } else {
                    $clearBtn.addClass('hidden');
                }
            });

            // Clear input
            $clearBtn.on('click', function () {
                $urlInput.val('').focus();
                $clearBtn.addClass('hidden');
            });

            // Clipboard Paste
            $pasteBtn.on('click', async function () {
                try {
                    if (navigator.clipboard && navigator.clipboard.readText) {
                        const text = await navigator.clipboard.readText();
                        if (text) {
                            $urlInput.val(text).trigger('input').focus();
                            showToast('Enlace pegado desde el portapapeles');
                        }
                    } else {
                        $urlInput.focus();
                    }
                } catch (e) {
                    $urlInput.focus();
                }
            });

            // Navigation tabs
            $('.nav-tab').on('click', function () {
                $('.nav-tab').removeClass('text-primary font-bold bg-surface-container-high border border-primary/20')
                            .addClass('text-on-surface-variant');
                $(this).removeClass('text-on-surface-variant')
                       .addClass('text-primary font-bold bg-surface-container-high border border-primary/20');
                
                const platform = $(this).data('platform') || $(this).attr('data-platform');
                if (platform === 'instagram') {
                    $urlInput.attr('placeholder', translations.input_placeholder_ig || translations.input_placeholder);
                } else if (platform === 'tiktok') {
                    $urlInput.attr('placeholder', translations.input_placeholder_tt || translations.input_placeholder);
                } else if (platform === 'facebook') {
                    $urlInput.attr('placeholder', translations.input_placeholder_fb || translations.input_placeholder);
                } else {
                    $urlInput.attr('placeholder', translations.input_placeholder);
                }
                $urlInput.focus();
            });

            // Analyze Action
            $analyzeBtn.on('click', function () {
                const url = $urlInput.val().trim();

                if (!url) {
                    showToast(translations.js_alert_invalid_link, true);
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
                        if (!data.status && data.error) {
                            showToast(translations.js_alert_prefix + (data.message || data.error), true);
                        } else if (data.result && data.result.length > 0) {
                            const result = data.result[0];
                            currentDownloadUrl = window.location.origin + '/' + result.url;

                            $videoThumb.attr('src', result.thumb || 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23ff8aa5\' stroke-width=\'1.5\'><rect x=\'2\' y=\'2\' width=\'20\' height=\'20\' rx=\'5\'/><path d=\'M10 8l6 4-6 4V8z\'/></svg>');
                            $downloadLink.attr('href', result.url);
                            
                            const platformName = data.platform ? data.platform.toUpperCase() : 'VIDEO';
                            $platformBadge.text(platformName);
                            
                            $resultCard.removeClass('hidden');
                            $('html, body').animate({
                                scrollTop: $resultCard.offset().top - 80
                            }, 500);
                        } else {
                            showToast(translations.js_alert_error, true);
                        }
                    },
                    error: function (err) {
                        console.error("AJAX Error:", err);
                        showToast(translations.js_alert_error, true);
                    },
                    complete: function () {
                        $loading.addClass('hidden');
                    }
                });
            });

            // Enter key trigger
            $urlInput.on('keypress', function (e) {
                if (e.which === 13) {
                    $analyzeBtn.click();
                }
            });

            // Copy Link
            $copyBtn.on('click', function () {
                const targetUrl = $downloadLink.attr('href');
                if (targetUrl && targetUrl !== '#') {
                    const fullUrl = window.location.origin + '/' + targetUrl;
                    navigator.clipboard.writeText(fullUrl).then(() => {
                        showToast(translations.toast_copied);
                    }).catch(() => {
                        showToast(translations.toast_copied);
                    });
                }
            });

            // Download Another
            $anotherBtn.on('click', function () {
                $resultCard.addClass('hidden');
                $urlInput.val('').trigger('input').focus();
                $('html, body').animate({
                    scrollTop: $urlInput.offset().top - 120
                }, 400);
            });
        });
    </script>
</body>

</html>