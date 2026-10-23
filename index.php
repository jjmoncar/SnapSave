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
    <title><?php echo htmlspecialchars($t['title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($t['meta_desc']); ?>">
    <meta name="theme-color" content="#0e0e0e">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($t['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($t['meta_desc']); ?>">
    <meta property="og:site_name" content="SnapSave">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($t['title']); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($t['meta_desc']); ?>">

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ff8aa5'><path d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14l-4-4 1.41-1.41L11 13.17l6.59-6.59L19 8l-8 8z'/></svg>">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .glass-morphism {
            background: rgba(26, 26, 25, 0.7);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .ambient-shadow {
            box-shadow: 0 40px 80px rgba(255, 138, 165, 0.08);
        }

        .glow-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 20px 40px rgba(255, 138, 165, 0.15);
        }

        .glow-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 60px rgba(255, 138, 165, 0.3);
        }

        .input-glow:focus {
            background: rgba(38, 38, 38, 0.9);
            box-shadow: 0 0 0 2px rgba(255, 138, 165, 0.35);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        .lang-dropdown {
            background: rgba(38, 38, 38, 0.98);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

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

<body class="overflow-x-hidden font-sans bg-surface text-on-surface flex flex-col min-h-screen">
    <!-- Navbar -->
    <header class="p-6 w-full">
        <div class="max-w-6xl mx-auto flex justify-between items-center gap-4">
            <span class="text-2xl font-bold tracking-widest text-on-surface uppercase font-display shrink-0">
                <a href="index.php" class="hover:text-primary transition-colors">SnapSave<span class="text-primary">.</span></a>
            </span>

            <div class="flex items-center space-x-3 md:space-x-6">
                <!-- Nav Tabs for Desktop and Tablet -->
                <nav class="hidden md:flex space-x-2 bg-surface-container/60 p-1.5 rounded-full border border-white/5 backdrop-blur-md">
                    <button type="button" data-platform="all"
                        class="nav-tab px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all text-primary bg-surface-container-high border border-primary/30 shadow-sm">
                        <?php echo htmlspecialchars($t['nav_all']); ?>
                    </button>
                    <button type="button" data-platform="twitter"
                        class="nav-tab px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all text-on-surface-variant hover:text-primary">
                        <?php echo htmlspecialchars($t['nav_twitter']); ?>
                    </button>
                    <button type="button" data-platform="youtube"
                        class="nav-tab px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all text-on-surface-variant hover:text-primary">
                        <?php echo htmlspecialchars($t['nav_youtube']); ?>
                    </button>
                    <button type="button" data-platform="instagram"
                        class="nav-tab px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all text-on-surface-variant hover:text-primary">
                        <?php echo htmlspecialchars($t['nav_instagram']); ?>
                    </button>
                    <button type="button" data-platform="tiktok"
                        class="nav-tab px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all text-on-surface-variant hover:text-primary">
                        <?php echo htmlspecialchars($t['nav_tiktok']); ?>
                    </button>
                    <button type="button" data-platform="facebook"
                        class="nav-tab px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all text-on-surface-variant hover:text-primary">
                        <?php echo htmlspecialchars($t['nav_facebook']); ?>
                    </button>
                </nav>

                <!-- Language Selector Dropdown -->
                <div class="relative group shrink-0">
                    <button
                        class="flex items-center gap-2 bg-surface-container hover:bg-surface-container-high px-3.5 py-2 rounded-xl transition-all text-xs font-semibold text-on-surface border border-white/5">
                        <span class="uppercase"><?php echo htmlspecialchars($current_lang); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-3.5 w-3.5 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        class="absolute right-0 mt-2 w-36 rounded-xl lang-dropdown opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 ambient-shadow overflow-hidden shadow-2xl">
                        <a href="?lang=en"
                            class="flex items-center gap-2 px-4 py-2.5 text-xs text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors <?php echo $current_lang === 'en' ? 'text-primary font-bold' : ''; ?>">🇺🇸
                            English</a>
                        <a href="?lang=es"
                            class="flex items-center gap-2 px-4 py-2.5 text-xs text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors <?php echo $current_lang === 'es' ? 'text-primary font-bold' : ''; ?>">🇪🇸
                            Español</a>
                        <a href="?lang=pt"
                            class="flex items-center gap-2 px-4 py-2.5 text-xs text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors <?php echo $current_lang === 'pt' ? 'text-primary font-bold' : ''; ?>">🇧🇷
                            Português</a>
                        <a href="?lang=fr"
                            class="flex items-center gap-2 px-4 py-2.5 text-xs text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors <?php echo $current_lang === 'fr' ? 'text-primary font-bold' : ''; ?>">🇫🇷
                            Français</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Scrollable Platform Bar -->
        <div class="md:hidden max-w-6xl mx-auto mt-4 overflow-x-auto no-scrollbar py-1">
            <div class="flex space-x-2 bg-surface-container/60 p-1 rounded-full border border-white/5 backdrop-blur-md w-max">
                <button type="button" data-platform="all"
                    class="nav-tab px-3 py-1 rounded-full text-xs font-semibold transition-all text-primary bg-surface-container-high border border-primary/30">
                    <?php echo htmlspecialchars($t['nav_all']); ?>
                </button>
                <button type="button" data-platform="twitter"
                    class="nav-tab px-3 py-1 rounded-full text-xs font-semibold transition-all text-on-surface-variant hover:text-primary">
                    <?php echo htmlspecialchars($t['nav_twitter']); ?>
                </button>
                <button type="button" data-platform="youtube"
                    class="nav-tab px-3 py-1 rounded-full text-xs font-semibold transition-all text-on-surface-variant hover:text-primary">
                    <?php echo htmlspecialchars($t['nav_youtube']); ?>
                </button>
                <button type="button" data-platform="instagram"
                    class="nav-tab px-3 py-1 rounded-full text-xs font-semibold transition-all text-on-surface-variant hover:text-primary">
                    <?php echo htmlspecialchars($t['nav_instagram']); ?>
                </button>
                <button type="button" data-platform="tiktok"
                    class="nav-tab px-3 py-1 rounded-full text-xs font-semibold transition-all text-on-surface-variant hover:text-primary">
                    <?php echo htmlspecialchars($t['nav_tiktok']); ?>
                </button>
                <button type="button" data-platform="facebook"
                    class="nav-tab px-3 py-1 rounded-full text-xs font-semibold transition-all text-on-surface-variant hover:text-primary">
                    <?php echo htmlspecialchars($t['nav_facebook']); ?>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-4xl mx-auto px-6 pt-10 pb-20 w-full">
        <!-- Header Value Proposition -->
        <div class="text-center mb-10 space-y-4">
            <h1 class="text-5xl md:text-7xl font-display font-extrabold text-on-surface tracking-tight leading-tight">
                <?php echo htmlspecialchars($t['header_title']); ?><span class="text-primary">.</span>
            </h1>
            <p class="text-base md:text-lg text-on-surface-variant max-w-2xl mx-auto font-normal leading-relaxed">
                <?php echo htmlspecialchars($t['header_desc']); ?>
            </p>
        </div>

        <!-- Download Card -->
        <div class="glass-morphism rounded-3xl p-6 md:p-10 mb-8 relative ambient-shadow">
            <div class="space-y-6">
                <div class="space-y-4">
                    <div class="relative flex items-center">
                        <input type="text" id="urlInput" placeholder="<?php echo htmlspecialchars($t['input_placeholder']); ?>"
                            autocomplete="off" spellcheck="false"
                            class="w-full bg-surface-container-highest rounded-3xl py-4 md:py-5 pl-5 md:pl-6 pr-32 text-on-surface placeholder:text-on-surface-variant focus:outline-none input-glow transition-all backdrop-blur-md text-sm md:text-base border border-transparent">
                        
                        <div class="absolute right-3 flex items-center space-x-1.5">
                            <button type="button" id="clearBtn" title="<?php echo htmlspecialchars($t['btn_clear']); ?>"
                                class="hidden p-2 rounded-full text-on-surface-variant hover:text-on-surface hover:bg-surface-bright transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <button type="button" id="pasteBtn"
                                class="px-3.5 py-1.5 md:py-2 bg-surface-bright hover:bg-surface-container-high text-primary font-semibold text-xs md:text-sm rounded-full transition-all flex items-center gap-1.5 border border-primary/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span><?php echo htmlspecialchars($t['btn_paste']); ?></span>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button id="analyzeBtn" type="button"
                            class="w-full md:w-auto bg-gradient-to-r from-primary to-secondary hover:brightness-110 text-surface font-extrabold py-3.5 md:py-4 px-10 md:px-12 text-base md:text-lg rounded-full transition-all glow-btn uppercase tracking-wide flex items-center justify-center gap-2">
                            <span><?php echo htmlspecialchars($t['analyze_btn']); ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loading"
                class="hidden absolute inset-0 glass-morphism rounded-3xl flex flex-col items-center justify-center space-y-4 z-20 backdrop-blur-xl">
                <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                <p class="text-slate-200 font-semibold text-base animate-pulse"><?php echo htmlspecialchars($t['processing']); ?></p>
            </div>
        </div>

        <!-- Result Section -->
        <div id="resultCard" class="hidden glass-morphism rounded-3xl p-6 md:p-8 overflow-hidden animate-fade-in ambient-shadow mb-12">
            <div class="flex flex-col md:flex-row gap-6 md:gap-8 items-center">
                <!-- Thumbnail / Media preview wrapper -->
                <div class="w-full md:w-1/3 aspect-video md:aspect-square rounded-2xl bg-surface-container-highest overflow-hidden relative ambient-shadow flex items-center justify-center shrink-0">
                    <img id="videoThumb" src="" alt="Thumbnail" class="w-full h-full object-cover"
                         onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23ff8aa5\' stroke-width=\'1.5\'><rect x=\'2\' y=\'2\' width=\'20\' height=\'20\' rx=\'5\'/><path d=\'M10 8l6 4-6 4V8z\'/></svg>';">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                    <span id="platformBadge" class="absolute top-3 left-3 bg-surface-container-high/90 text-primary font-bold text-xs uppercase px-3 py-1 rounded-full backdrop-blur-md border border-primary/20">
                        VIDEO
                    </span>
                    <span id="qualityBadge" class="absolute bottom-3 left-3 bg-black/75 text-on-surface font-semibold text-xs px-2.5 py-0.5 rounded-md backdrop-blur-sm">
                        HD
                    </span>
                </div>

                <!-- Info and Actions -->
                <div class="w-full md:w-2/3 space-y-5">
                    <div>
                        <h3 class="text-xl md:text-2xl font-display font-bold text-on-surface mb-1.5"><?php echo htmlspecialchars($t['ready_to_download']); ?></h3>
                        <p id="videoTitle" class="text-on-surface font-medium text-sm line-clamp-2 text-slate-300"></p>
                        <p id="qualityText" class="text-on-surface-variant text-xs mt-1"><?php echo htmlspecialchars($t['quality']); ?></p>
                    </div>

                    <!-- Built-in Video Player Preview (Collapsible) -->
                    <div id="videoPreviewWrapper" class="hidden rounded-2xl overflow-hidden bg-black/60 border border-white/10 mb-3">
                        <video id="videoPlayer" controls playsinline class="w-full max-h-60 rounded-xl bg-black">
                            <source src="" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>
                    </div>

                    <div class="space-y-3">
                        <!-- Direct Action: Download Link Styled As Primary Button -->
                        <a id="downloadLink" href="#" download
                            class="w-full bg-gradient-to-r from-primary to-secondary hover:brightness-110 text-surface font-extrabold py-4 rounded-full transition-all glow-btn flex items-center justify-center gap-2.5 text-base shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span><?php echo htmlspecialchars($t['download_now']); ?></span>
                        </a>

                        <!-- Secondary Actions Row -->
                        <div class="flex gap-2.5">
                            <button type="button" id="previewBtn"
                                class="flex-1 bg-surface-container-high hover:bg-surface-bright text-on-surface font-semibold py-2.5 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 text-xs border border-white/5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span id="previewBtnText"><?php echo htmlspecialchars($t['preview_video']); ?></span>
                            </button>

                            <button type="button" id="copyBtn"
                                class="flex-1 bg-surface-container-high hover:bg-surface-bright text-on-surface font-semibold py-2.5 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 text-xs border border-white/5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span><?php echo htmlspecialchars($t['btn_copy']); ?></span>
                            </button>

                            <button type="button" id="anotherBtn"
                                class="flex-1 bg-surface-container-high hover:bg-surface-bright text-on-surface-variant hover:text-on-surface font-semibold py-2.5 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 text-xs border border-white/5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span><?php echo htmlspecialchars($t['btn_another']); ?></span>
                            </button>
                        </div>

                        <!-- PayPal Support -->
                        <div class="mt-4 pt-4 border-t border-surface-container-high">
                            <a href="https://www.paypal.com/donate/?hosted_button_id=55WTBANAQKV2J" 
                               target="_blank" rel="noopener noreferrer" 
                               class="group block w-full bg-gradient-to-r from-blue-950/40 to-indigo-950/40 hover:from-blue-900/50 hover:to-indigo-900/50 border border-blue-500/20 hover:border-blue-400/40 rounded-2xl p-3.5 transition-all duration-300">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944 3.72a.77.77 0 0 1 .761-.643h6.666c2.404 0 4.296.643 5.4 1.841 1.055 1.144 1.258 2.766.602 4.823-.74 2.327-2.316 4.095-4.484 5.02-1.077.46-2.326.685-3.71.685H8.163a.77.77 0 0 0-.76.645l-1.026 5.865a.64.64 0 0 1-.63.541h1.333z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-wider text-blue-300"><?php echo htmlspecialchars($t['donate_title']); ?></p>
                                            <p class="text-xs text-on-surface-variant line-clamp-1"><?php echo htmlspecialchars($t['donate_desc']); ?></p>
                                        </div>
                                    </div>
                                    <span class="bg-blue-600 group-hover:bg-blue-500 text-white font-bold text-xs px-3 py-1.5 rounded-xl transition-colors shrink-0 flex items-center gap-1 shadow-md">
                                        <span><?php echo htmlspecialchars($t['btn_donate']); ?></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="grid md:grid-cols-3 gap-6 mt-16">
            <div class="glass-morphism p-7 rounded-3xl space-y-3.5 ambient-shadow">
                <div class="w-11 h-11 bg-pink-500/10 text-primary rounded-xl flex items-center justify-center font-bold font-display text-base">
                    01
                </div>
                <h4 class="text-base font-display font-bold text-on-surface"><?php echo htmlspecialchars($t['feature_1_title']); ?></h4>
                <p class="text-on-surface-variant text-xs leading-relaxed"><?php echo htmlspecialchars($t['feature_1_desc']); ?></p>
            </div>
            <div class="glass-morphism p-7 rounded-3xl space-y-3.5 ambient-shadow">
                <div class="w-11 h-11 bg-orange-500/10 text-secondary rounded-xl flex items-center justify-center font-bold font-display text-base">
                    02
                </div>
                <h4 class="text-base font-display font-bold text-on-surface"><?php echo htmlspecialchars($t['feature_2_title']); ?></h4>
                <p class="text-on-surface-variant text-xs leading-relaxed"><?php echo htmlspecialchars($t['feature_2_desc']); ?></p>
            </div>
            <div class="glass-morphism p-7 rounded-3xl space-y-3.5 ambient-shadow">
                <div class="w-11 h-11 bg-pink-500/10 text-primary rounded-xl flex items-center justify-center font-bold font-display text-base">
                    03
                </div>
                <h4 class="text-base font-display font-bold text-on-surface"><?php echo htmlspecialchars($t['feature_3_title']); ?></h4>
                <p class="text-on-surface-variant text-xs leading-relaxed"><?php echo htmlspecialchars($t['feature_3_desc']); ?></p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-10 px-6 mt-auto bg-surface-container-lowest border-t border-surface-container-low">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-on-surface-variant text-xs text-center md:text-left"><?php echo htmlspecialchars($t['footer_copyright']); ?></p>
            <div class="flex gap-6 text-xs text-on-surface-variant">
                <a href="terms.php" class="hover:text-on-surface transition-colors"><?php echo htmlspecialchars($t['footer_terms']); ?></a>
                <a href="privacy.php" class="hover:text-on-surface transition-colors"><?php echo htmlspecialchars($t['footer_privacy']); ?></a>
                <a href="mailto:emprendedordigital1970@gmail.com" class="hover:text-on-surface transition-colors"><?php echo htmlspecialchars($t['footer_contact']); ?></a>
            </div>
        </div>
    </footer>

    <!-- Toast Notification Container -->
    <div id="toastContainer"></div>

    <!-- JQuery and Client Script -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const translations = <?php echo json_encode($t, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

        function showToast(message, isError = false) {
            const toastId = 'toast-' + Date.now();
            const bgClass = isError ? 'bg-red-950/90 text-red-200 border-red-500/30' : 'bg-surface-bright/95 text-on-surface border-primary/30';
            const icon = isError 
                ? '<svg class="w-5 h-5 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                : '<svg class="w-5 h-5 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            
            const html = `
                <div id="${toastId}" class="toast-msg ${bgClass} glass-morphism border px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 max-w-md mb-3 translate-y-4 opacity-0">
                    ${icon}
                    <span class="text-xs md:text-sm font-medium leading-snug">${message}</span>
                </div>
            `;

            $('#toastContainer').append(html);
            setTimeout(() => {
                $(`#${toastId}`).removeClass('translate-y-4 opacity-0');
            }, 30);

            setTimeout(() => {
                $(`#${toastId}`).addClass('translate-y-4 opacity-0');
                setTimeout(() => { $(`#${toastId}`).remove(); }, 300);
            }, 4000);
        }

        // Auto-detect platform from pasted or typed URL
        function detectPlatform(url) {
            if (/youtube\.com|youtu\.be/i.test(url)) return 'youtube';
            if (/twitter\.com|x\.com|fixupx\.com|vxtwitter\.com/i.test(url)) return 'twitter';
            if (/instagram\.com|instagr\.am/i.test(url)) return 'instagram';
            if (/tiktok\.com/i.test(url)) return 'tiktok';
            if (/facebook\.com|fb\.watch|fb\.gg/i.test(url)) return 'facebook';
            return null;
        }

        function setActivePlatformTab(platform) {
            $('.nav-tab').removeClass('text-primary bg-surface-container-high border border-primary/30 shadow-sm')
                        .addClass('text-on-surface-variant');
            
            const targetPlatform = platform || 'all';
            $(`.nav-tab[data-platform="${targetPlatform}"]`)
                .removeClass('text-on-surface-variant')
                .addClass('text-primary bg-surface-container-high border border-primary/30 shadow-sm');
        }

        $(document).ready(function () {
            const $urlInput = $('#urlInput');
            const $analyzeBtn = $('#analyzeBtn');
            const $clearBtn = $('#clearBtn');
            const $pasteBtn = $('#pasteBtn');
            const $loading = $('#loading');
            const $resultCard = $('#resultCard');
            const $videoThumb = $('#videoThumb');
            const $videoTitle = $('#videoTitle');
            const $qualityText = $('#qualityText');
            const $qualityBadge = $('#qualityBadge');
            const $downloadLink = $('#downloadLink');
            const $copyBtn = $('#copyBtn');
            const $anotherBtn = $('#anotherBtn');
            const $previewBtn = $('#previewBtn');
            const $previewBtnText = $('#previewBtnText');
            const $videoPreviewWrapper = $('#videoPreviewWrapper');
            const $videoPlayer = $('#videoPlayer');
            const $platformBadge = $('#platformBadge');
            let isPreviewOpen = false;

            // Input listener with automatic platform detection
            $urlInput.on('input', function () {
                const val = $(this).val().trim();
                if (val.length > 0) {
                    $clearBtn.removeClass('hidden');
                    const detected = detectPlatform(val);
                    if (detected) {
                        setActivePlatformTab(detected);
                    }
                } else {
                    $clearBtn.addClass('hidden');
                    setActivePlatformTab('all');
                }
            });

            // Clear input
            $clearBtn.on('click', function () {
                $urlInput.val('').trigger('input').focus();
                setActivePlatformTab('all');
            });

            // Clipboard Paste button
            $pasteBtn.on('click', async function () {
                try {
                    if (navigator.clipboard && navigator.clipboard.readText) {
                        const text = await navigator.clipboard.readText();
                        if (text && text.trim().length > 0) {
                            $urlInput.val(text.trim()).trigger('input').focus();
                            showToast(translations.toast_pasted || 'Link pasted from clipboard!');
                        } else {
                            $urlInput.focus();
                        }
                    } else {
                        $urlInput.focus();
                        showToast(translations.toast_paste_error || 'Please paste manually.', true);
                    }
                } catch (e) {
                    $urlInput.focus();
                    showToast(translations.toast_paste_error || 'Please paste manually.', true);
                }
            });

            // Navigation tabs click handler
            $('.nav-tab').on('click', function () {
                const platform = $(this).data('platform') || $(this).attr('data-platform');
                setActivePlatformTab(platform);

                if (platform === 'youtube') {
                    $urlInput.attr('placeholder', translations.input_placeholder_yt || translations.input_placeholder);
                } else if (platform === 'instagram') {
                    $urlInput.attr('placeholder', translations.input_placeholder_ig || translations.input_placeholder);
                } else if (platform === 'tiktok') {
                    $urlInput.attr('placeholder', translations.input_placeholder_tt || translations.input_placeholder);
                } else if (platform === 'twitter') {
                    $urlInput.attr('placeholder', translations.input_placeholder_tw || translations.input_placeholder);
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
                    $urlInput.focus();
                    return;
                }

                $analyzeBtn.prop('disabled', true);
                $loading.removeClass('hidden');
                $resultCard.addClass('hidden');

                // Close any open video preview
                if (isPreviewOpen) {
                    $videoPreviewWrapper.addClass('hidden');
                    $videoPlayer[0].pause();
                    $videoPlayer.attr('src', '');
                    $previewBtnText.text(translations.preview_video || 'Preview');
                    isPreviewOpen = false;
                }

                $.ajax({
                    url: 'api.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ url: url }),
                    success: function (data) {
                        if (!data.status && data.error) {
                            showToast((translations.js_alert_prefix || 'Error: ') + (data.message || data.error), true);
                        } else if (data.result && data.result.length > 0) {
                            const result = data.result[0];

                            // Resolve relative URL robustly regardless of subdirectory hosting
                            const fullDownloadUrl = new URL(result.url, window.location.href).href;

                            $videoThumb.attr('src', result.thumb || 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23ff8aa5\' stroke-width=\'1.5\'><rect x=\'2\' y=\'2\' width=\'20\' height=\'20\' rx=\'5\'/><path d=\'M10 8l6 4-6 4V8z\'/></svg>');
                            $downloadLink.attr('href', result.url);
                            if (result.filename) {
                                $downloadLink.attr('download', result.filename);
                            }

                            // Set title if available
                            if (data.title) {
                                $videoTitle.text(data.title).removeClass('hidden');
                            } else {
                                $videoTitle.addClass('hidden');
                            }

                            // Quality and platform badges
                            const platformName = (data.platform || 'VIDEO').toUpperCase();
                            $platformBadge.text(platformName);
                            setActivePlatformTab(data.platform);

                            const quality = data.quality || result.quality || 'HD (MP4)';
                            $qualityBadge.text(quality);
                            $qualityText.text((translations.quality || 'Quality: ') + quality);

                            // Configure video player preview source
                            $videoPlayer.attr('src', result.url);

                            $resultCard.removeClass('hidden');
                            $('html, body').animate({
                                scrollTop: $resultCard.offset().top - 80
                            }, 500);
                        } else {
                            showToast(translations.js_alert_error, true);
                        }
                    },
                    error: function (err) {
                        console.error("SnapSave AJAX Error:", err);
                        showToast(translations.js_alert_error, true);
                    },
                    complete: function () {
                        $loading.addClass('hidden');
                        $analyzeBtn.prop('disabled', false);
                    }
                });
            });

            // Enter key trigger on input
            $urlInput.on('keypress', function (e) {
                if (e.which === 13) {
                    $analyzeBtn.click();
                }
            });

            // Toggle HTML5 Video Preview
            $previewBtn.on('click', function () {
                if (!isPreviewOpen) {
                    $videoPreviewWrapper.removeClass('hidden');
                    $videoPlayer[0].play().catch(() => {});
                    $previewBtnText.text(translations.btn_close_preview || 'Close Preview');
                    isPreviewOpen = true;
                } else {
                    $videoPlayer[0].pause();
                    $videoPreviewWrapper.addClass('hidden');
                    $previewBtnText.text(translations.preview_video || 'Preview');
                    isPreviewOpen = false;
                }
            });

            // Copy Download Link to Clipboard
            $copyBtn.on('click', function () {
                const targetUrl = $downloadLink.attr('href');
                if (targetUrl && targetUrl !== '#') {
                    const fullUrl = new URL(targetUrl, window.location.href).href;
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(fullUrl).then(() => {
                            showToast(translations.toast_copied);
                        }).catch(() => {
                            showToast(translations.toast_copied);
                        });
                    } else {
                        showToast(translations.toast_copied);
                    }
                }
            });

            // Download Another button
            $anotherBtn.on('click', function () {
                if (isPreviewOpen) {
                    $videoPlayer[0].pause();
                    $videoPreviewWrapper.addClass('hidden');
                    $previewBtnText.text(translations.preview_video || 'Preview');
                    isPreviewOpen = false;
                }
                $resultCard.addClass('hidden');
                $urlInput.val('').trigger('input').focus();
                setActivePlatformTab('all');
                $('html, body').animate({
                    scrollTop: $urlInput.offset().top - 120
                }, 400);
            });
        });
    </script>
</body>

</html>