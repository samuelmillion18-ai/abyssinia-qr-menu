<?php
require_once 'db_connection.php';

$cafe_id = 1; 
$message = "";
$upload_dir = 'uploads/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = trim(filter_input(INPUT_POST, 'cat_name', FILTER_SANITIZE_SPECIAL_CHARS));
        if ($name) {
            $stmt = $pdo->prepare("INSERT INTO categories (name, cafe_id) VALUES (?, ?)");
            $stmt->execute([$name, $cafe_id]);
            $message = "Category '$name' created successfully! 📁";
        }
    }

    if (isset($_POST['add_item'])) {
        $cat_id = $_POST['category_id'];
        $name   = trim(filter_input(INPUT_POST, 'item_name', FILTER_SANITIZE_SPECIAL_CHARS));
        $desc   = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));
        $price  = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        
        $image_path = ""; 

        if (!empty($_FILES['item_image']['name'])) {
            $file_name = time() . '_' . basename($_FILES['item_image']['name']);
            $target_file = $upload_dir . $file_name;
            $check = getimagesize($_FILES['item_image']['tmp_name']);
            
            if($check !== false) {
                if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file)) {
                    $image_path = $target_file;
                } else {
                    $message = "❌ Upload failed.";
                }
            } else {
                $message = "❌ Invalid image file.";
            }
        }

        if ($cat_id && $name && $price !== false && empty($message)) {
            $stmt = $pdo->prepare("INSERT INTO menu_items (category_id, name, description, price, image_url, cafe_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cat_id, $name, $desc, $price, $image_path, $cafe_id]);
            $message = "Item '$name' added to menu! 🍔";
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories WHERE cafe_id = $cafe_id ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Abyssinia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900">

    <nav class="sticky top-0 z-40 w-full border-b border-slate-200 bg-white/80 backdrop-blur-md">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-amber-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-amber-200">A</div>
                <span class="font-extrabold tracking-tight text-slate-800">Abyssinia <span class="text-amber-600 font-medium">Cafe</span></span>
            </div>
            <div class="flex items-center gap-6">
                <a href="admin.php" class="text-sm font-black text-amber-600 border-b-2 border-amber-600 pb-1">Dashboard</a>
                <a href="categories.php" class="text-sm font-bold text-slate-500 hover:text-amber-600 transition">Categories</a>
                <a href="qr_generator.php" class="text-sm font-bold text-slate-500 hover:text-amber-600 transition">QR Codes</a>
                <a href="index.php" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:shadow-xl transition active:scale-95">View Menu</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-12">
        <div class="mb-12">
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Dashboard</h1>
            <p class="text-slate-500 mt-2 font-medium">Hello, <span class="text-amber-600"><?php echo htmlspecialchars($full_name ?? 'Manager'); ?></span>. Let's update your menu vibes.</p>
        </div>

        <?php if ($message): ?>
            <div class="mb-8 p-5 bg-white border-l-4 border-amber-500 rounded-2xl shadow-sm font-bold text-slate-700 flex items-center gap-3 animate-in fade-in slide-in-from-top-2">
                <span class="text-xl">✨</span> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 transition-hover hover:shadow-md">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-3 bg-amber-50 rounded-2xl text-amber-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                        </div>
                        <h2 class="text-xl font-extrabold">Quick Category</h2>
                    </div>

                    <form method="POST" class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest ml-1">Name</label>
                            <input type="text" name="cat_name" placeholder="Breakfast" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-600 outline-none transition font-semibold">
                        </div>
                        <button type="submit" name="add_category" 
                            class="w-full bg-amber-600 text-white font-black py-4 rounded-2xl hover:bg-amber-700 transition active:scale-95 shadow-xl shadow-amber-100">
                            Create Folder
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div class="bg-white p-8 md:p-10 rounded-[2.5rem] shadow-sm border border-slate-100 transition-hover hover:shadow-md">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-3 bg-blue-50 rounded-2xl text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        </div>
                        <h2 class="text-xl font-extrabold text-slate-800">New Menu Item</h2>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest ml-1">Menu Category</label>
                                <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 outline-none focus:border-blue-500 font-semibold appearance-none">
                                    <option value="">Select Category...</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest ml-1">Dish Name</label>
                                <input type="text" name="item_name" required placeholder="e.g. Special Kitfo"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 outline-none focus:border-blue-500 font-semibold">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest ml-1">Price (ETB)</label>
                                    <input type="number" step="0.01" name="price" required placeholder="0.00"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 outline-none focus:border-blue-500 font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest ml-1">Food Photo</label>
                                    <label class="flex flex-col items-center w-full bg-blue-50 border-2 border-dashed border-blue-200 rounded-2xl cursor-pointer hover:bg-blue-100 transition group">
                                        <span class="py-3 text-[10px] font-black text-blue-600 uppercase group-hover:scale-110 transition">Choose Image</span>
                                        <input type='file' name="item_image" id="imgInput" class="hidden" accept="image/*" />
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6 flex flex-col justify-between">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest ml-1">Ingredients / Description</label>
                                <textarea name="description" rows="4" placeholder="Briefly describe this dish..." 
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 outline-none focus:border-blue-500 font-medium"></textarea>
                            </div>

                            <div id="previewContainer" class="hidden h-24 w-full rounded-2xl overflow-hidden border border-slate-100 relative">
                                <img id="preview" src="#" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/20 flex items-center justify-center text-[10px] text-white font-bold">PREVIEW</div>
                            </div>

                            <button type="submit" name="add_item" 
                                class="w-full bg-slate-900 text-white font-black py-5 rounded-2xl hover:bg-black transition active:scale-95 shadow-xl shadow-slate-200">
                                Add to Menu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        imgInput.onchange = evt => {
            const [file] = imgInput.files
            if (file) {
                preview.src = URL.createObjectURL(file)
                previewContainer.classList.remove('hidden')
            }
        }
    </script>
</body>
</html>