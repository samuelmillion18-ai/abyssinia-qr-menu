<?php
require_once 'db_connection.php';
session_start();

// Default values for the generator
$base_url = "http://localhost/abyssinia/index.php";
$num_tables = 1; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $base_url = trim(filter_input(INPUT_POST, 'base_url', FILTER_SANITIZE_URL));
    $num_tables = filter_input(INPUT_POST, 'num_tables', FILTER_VALIDATE_INT);
    if (!$num_tables || $num_tables < 1) $num_tables = 1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Menu Generator | Abyssinia Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Print Styles: Hide UI and optimize for A4 paper */
        @media print {
            body { background: white !margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .print-grid { 
                display: grid !important; 
                grid-template-columns: repeat(2, 1fr) !important; 
                gap: 2rem !important; 
                padding: 0 !important;
            }
            .qr-card { 
                break-inside: avoid; 
                page-break-inside: avoid;
                border: 2px dashed #e2e8f0 !important;
                box-shadow: none !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page { margin: 1cm; size: A4 portrait; }
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900">

    <nav class="no-print sticky top-0 z-40 w-full border-b border-slate-200 bg-white/80 backdrop-blur-md">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-amber-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-amber-200">A</div>
                <span class="font-extrabold tracking-tight text-slate-800">Abyssinia <span class="text-amber-600 font-medium">Cafe</span></span>
            </div>
            <div class="flex items-center gap-6">
                <a href="admin.php" class="text-sm font-bold text-slate-500 hover:text-amber-600 transition">Dashboard</a>
                <a href="qr_generator.php" class="text-sm font-black text-amber-600 border-b-2 border-amber-600 pb-1">QR Codes</a>
                <a href="index.php" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:shadow-xl transition">View Menu</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-12">
        
        <div class="no-print mb-12 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">QR Menu Generator</h1>
                    <p class="text-slate-500 mt-1 font-medium">Create common QR codes for your customers to access the digital menu.</p>
                </div>
                <button onclick="window.print()" class="bg-amber-600 text-white px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-amber-700 transition active:scale-95 flex items-center justify-center gap-2 shadow-xl shadow-amber-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" /></svg>
                    Print QR Cards
                </button>
            </div>

            <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest ml-1">Menu Destination URL</label>
                    <input type="url" name="base_url" value="<?php echo htmlspecialchars($base_url); ?>" placeholder="http://localhost/abyssinia/index.php" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-600 outline-none transition font-semibold">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest ml-1">Quantity to Print</label>
                    <div class="flex gap-4">
                        <input type="number" name="num_tables" value="<?php echo $num_tables; ?>" min="1" max="50" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-600 outline-none transition font-semibold text-center">
                        <button type="submit" class="bg-slate-900 text-white px-6 py-4 rounded-2xl font-black text-sm hover:bg-black transition active:scale-95">
                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="print-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php for ($i = 1; $i <= $num_tables; $i++): ?>
                <?php 
                    // UPDATED RELIABLE QR CODE LINK
                    $qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($base_url);
                ?>
                <div class="qr-card bg-white p-8 rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col items-center text-center relative overflow-hidden">
                    
                    <div class="absolute top-0 inset-x-0 h-32 bg-gradient-to-b from-amber-50 to-transparent -z-10"></div>

                    <div class="mb-6">
                        <div class="w-14 h-14 bg-amber-600 rounded-2xl flex items-center justify-center text-white font-black text-3xl shadow-lg shadow-amber-200 mx-auto mb-3">A</div>
                        <h2 class="text-2xl font-black tracking-tighter text-slate-800 uppercase">Abyssinia <span class="text-amber-600 italic font-medium">Cafe</span></h2>
                        <div class="h-1 w-12 bg-amber-200 mx-auto mt-2 rounded-full"></div>
                    </div>

                    <div class="bg-white p-4 rounded-[2rem] border-4 border-stone-50 shadow-inner mb-6 transition-transform hover:scale-105 duration-500">
                        <img src="<?php echo $qr_api_url; ?>" 
                             alt="Scan Menu" 
                             class="w-48 h-48 object-contain"
                             onerror="this.src='https://via.placeholder.com/300?text=Scan+Menu'">
                    </div>

                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-amber-600 uppercase tracking-[0.3em]">Welcome</p>
                        <p class="text-lg font-extrabold text-slate-800">Scan to View Menu</p>
                        <p class="text-xs font-medium text-slate-400 max-w-[180px] mx-auto mt-2">Enjoy our premium Ethiopian coffee and cuisine.</p>
                    </div>

                    <div class="mt-8 pt-6 border-t border-stone-100 w-full">
                         <span class="text-[10px] font-bold text-stone-300 uppercase tracking-widest">Table Copy #<?php echo $i; ?></span>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

    </main>
</body>
</html>