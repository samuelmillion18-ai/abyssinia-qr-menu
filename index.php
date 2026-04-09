<?php
require_once 'db_connection.php';
session_start();

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Check if they scanned a table QR code and store it in session
if (isset($_GET['table'])) {
    $_SESSION['table_number'] = filter_input(INPUT_GET, 'table', FILTER_VALIDATE_INT);
}
$current_table = $_SESSION['table_number'] ?? null;

// Get user info from session
$full_name = $_SESSION['full_name'] ?? '';
$user_initial = !empty($full_name) ? strtoupper(substr($full_name, 0, 1)) : '';

$cafe_id = 1; 
$currency = "ETB";

// 1. Fetch Categories
try {
    $stmt_cats = $pdo->prepare("SELECT id, name FROM categories WHERE cafe_id = ? ORDER BY name ASC");
    $stmt_cats->execute([$cafe_id]);
    $categories = $stmt_cats->fetchAll();
} catch (PDOException $e) {
    die("Category Fetch Error");
}

// 2. Fetch Menu Items
$selected_category = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);
try {
    $sql_items = "SELECT id, category_id, name, description, price, image_url 
                  FROM menu_items 
                  WHERE cafe_id = ? AND is_available = 1";
    $params = [$cafe_id];
    if ($selected_category) {
        $sql_items .= " AND category_id = ?";
        $params[] = $selected_category;
    }
    $stmt_items = $pdo->prepare($sql_items);
    $stmt_items->execute($params);
    $menu_items = $stmt_items->fetchAll();
} catch (PDOException $e) {
    die("Item Fetch Error");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abyssinia Cafe | Premium Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .glass-nav { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
        
        /* Typewriter Cursor Effect */
        .typewriter-cursor::after {
            content: '|';
            animation: blink 1s infinite;
            margin-left: 4px;
            color: #fbbf24;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
    </style>
</head>
<body class="bg-[#FAFAF9] text-slate-800 antialiased selection:bg-amber-200 selection:text-amber-900">

    <div class="bg-white/95 backdrop-blur-md border-b border-stone-200 sticky top-0 z-[60] shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-5">
                <a href="index.php" class="text-amber-600 font-extrabold text-2xl tracking-tight flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/></svg>
                    ABYSSINIA
                </a>
                <?php if ($current_table): ?>
                    <div class="hidden sm:flex items-center gap-1.5 bg-stone-100 text-stone-700 px-3.5 py-1.5 rounded-full text-xs font-bold border border-stone-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                        Table <?php echo $current_table; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="flex items-center gap-4">
                <?php if (!empty($full_name)): ?>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group p-1 pr-3 rounded-full hover:bg-stone-50 transition-colors border border-transparent hover:border-stone-200">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold shadow-sm group-hover:scale-105 transition-transform">
                                <?php echo $user_initial; ?>
                            </div>
                            <span class="text-sm font-semibold text-slate-700 hidden md:block group-hover:text-amber-700 transition-colors"><?php echo htmlspecialchars($full_name); ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-stone-400 hidden md:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" style="display: none;"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                             class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-stone-100 py-2 z-50">
                            <div class="px-4 py-3 border-b border-stone-100 md:hidden">
                                <p class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Signed in as</p>
                                <p class="text-sm font-bold text-slate-800 truncate mt-0.5"><?php echo $full_name; ?></p>
                            </div>
                            <a href="?logout=1" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors mx-2 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Sign Out
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white bg-slate-900 hover:bg-amber-600 rounded-full transition-colors shadow-sm">
                        Sign In
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <header class="relative h-[45vh] min-h-[350px] md:h-[55vh] flex items-center justify-center overflow-hidden">
        <img src="header_image.png" class="absolute inset-0 w-full h-full object-cover z-0" alt="Cafe Header">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20 z-10"></div>
        
        <div class="relative z-20 text-center px-4 max-w-3xl mx-auto mt-8">
            <span class="inline-block py-1 px-3 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold tracking-widest uppercase mb-4">Welcome to</span>
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-extrabold text-white tracking-tight drop-shadow-xl mb-6">
                <span id="typewriter-title" class="typewriter-cursor"></span>
            </h1>
            <p id="typewriter-sub" class="text-lg md:text-xl text-stone-200 font-medium drop-shadow-md min-h-[1.5em]"></p>
        </div>
    </header>

    <nav class="sticky top-16 z-40 glass-nav border-b border-stone-200/60 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center space-x-2 overflow-x-auto no-scrollbar pb-1">
                <a href="index.php" class="px-6 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all duration-300 
                    <?php echo !$selected_category ? 'bg-slate-900 text-white shadow-md' : 'bg-white border border-stone-200 text-stone-600 hover:border-slate-900 hover:text-slate-900'; ?>">
                    All Menu
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="index.php?category=<?php echo $cat['id']; ?>" class="px-6 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all duration-300 
                        <?php echo ($selected_category == $cat['id']) ? 'bg-slate-900 text-white shadow-md' : 'bg-white border border-stone-200 text-stone-600 hover:border-slate-900 hover:text-slate-900'; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-12 md:py-20">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    <?php echo $selected_category ? "Menu Selection" : "Chef's Specials"; ?>
                </h2>
                <p class="text-stone-500 mt-2 font-medium">Discover our carefully curated dishes.</p>
            </div>
            <span class="hidden md:inline-flex items-center justify-center px-4 py-1.5 bg-stone-100 text-stone-600 rounded-full text-sm font-bold">
                <?php echo count($menu_items); ?> Items
            </span>
        </div>

        <?php if (empty($menu_items)): ?>
            <div class="text-center py-24 bg-white rounded-3xl border border-stone-200 shadow-sm">
                <div class="w-24 h-24 bg-stone-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-2">No items found</h3>
                <p class="text-stone-500 mb-6">We're updating our menu for this category.</p>
                <a href="index.php" class="inline-flex items-center justify-center px-6 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-amber-600 transition-colors">
                    View Full Menu
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php foreach ($menu_items as $item): ?>
                    <div class="group bg-white rounded-[2rem] shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.12)] transition-all duration-300 border border-stone-100 flex flex-col overflow-hidden hover:-translate-y-1">
                        <div class="relative aspect-[4/3] overflow-hidden bg-stone-100">
                            <?php if ($item['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <?php else: ?>
                                <div class="w-full h-full flex flex-col items-center justify-center text-stone-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                            <?php endif; ?>
                            <div class="absolute top-4 right-4 bg-white/95 backdrop-blur px-3 py-1.5 rounded-xl shadow-md border border-white/50">
                                <span class="text-base font-extrabold text-slate-900"><?php echo number_format($item['price']); ?> <span class="text-xs text-stone-500 font-bold ml-0.5"><?php echo $currency; ?></span></span>
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <div class="flex-grow">
                                <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-amber-600 transition-colors line-clamp-1"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="text-sm text-stone-500 leading-relaxed line-clamp-2"><?php echo htmlspecialchars($item['description']); ?></p>
                            </div>
                            <div class="mt-6 pt-6 border-t border-stone-100">
                                <button class="w-full bg-stone-50 hover:bg-slate-900 text-slate-700 hover:text-white py-3.5 rounded-xl font-bold text-sm transition-colors duration-300 flex items-center justify-center gap-2 group/btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-stone-400 group-hover/btn:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add to Order
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-white border-t border-stone-200 pt-16 pb-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-12 mb-12 text-center md:text-left">
                <div>
                    <a href="#" class="text-amber-600 font-extrabold text-2xl tracking-tight flex items-center justify-center md:justify-start gap-2 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/></svg>
                        ABYSSINIA
                    </a>
                    <p class="text-stone-500 text-sm leading-relaxed max-w-xs mx-auto md:mx-0">Bringing the heart of Ethiopian hospitality and premium coffee to your neighborhood.</p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-4 uppercase tracking-wider text-sm">Opening Hours</h4>
                    <ul class="space-y-2 text-sm text-stone-500">
                        <li class="flex justify-center md:justify-start gap-2">
                            <span class="font-medium text-stone-700">Mon - Fri:</span> 7:00 AM - 10:00 PM
                        </li>
                        <li class="flex justify-center md:justify-start gap-2">
                            <span class="font-medium text-stone-700">Sat - Sun:</span> 8:00 AM - 11:00 PM
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-4 uppercase tracking-wider text-sm">Quick Links</h4>
                    <div class="flex flex-col space-y-2 text-sm text-stone-500">
                        <a href="admin.php" class="hover:text-amber-600 transition-colors font-medium">Dashboard Login</a>
                        <a href="#" class="hover:text-amber-600 transition-colors font-medium">Privacy Policy</a>
                    </div>
                </div>
            </div>
            <div class="pt-8 border-t border-stone-100 text-center text-sm text-stone-400">
                <p>&copy; <?php echo date('Y'); ?> Abyssinia Cafe. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Typewriter logic
        function typeWriter(elementId, text, speed, callback) {
            let i = 0;
            const element = document.getElementById(elementId);
            function type() {
                if (i < text.length) {
                    element.innerHTML += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                } else if (callback) {
                    callback();
                }
            }
            type();
        }

        // Start animation on load
        window.onload = function() {
            typeWriter('typewriter-title', 'Abyssinia Experience.', 100, function() {
                // Remove cursor from title and start subtitle after a small delay
                document.getElementById('typewriter-title').classList.remove('typewriter-cursor');
                setTimeout(() => {
                    const sub = document.getElementById('typewriter-sub');
                    sub.classList.add('typewriter-cursor');
                    typeWriter('typewriter-sub', 'Authentic flavors, crafted with passion.', 50);
                }, 500);
            });
        };
    </script>
</body>
</html>