<?php
require_once 'db_connection.php';

$cafe_id = 1; 
$message = "";

// --- 1. Handle Create Category ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim(filter_input(INPUT_POST, 'cat_name', FILTER_SANITIZE_SPECIAL_CHARS));
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, cafe_id) VALUES (?, ?)");
        $stmt->execute([$name, $cafe_id]);
        $message = "Category '$name' added successfully! ✨";
    }
}

// --- 2. Handle Update Category ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $id = filter_input(INPUT_POST, 'cat_id', FILTER_VALIDATE_INT);
    $name = trim(filter_input(INPUT_POST, 'cat_name', FILTER_SANITIZE_SPECIAL_CHARS));
    if ($id && !empty($name)) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ? AND cafe_id = ?");
        $stmt->execute([$name, $id, $cafe_id]);
        $message = "Category updated! 🪄";
    }
}

// --- 3. Handle Delete Category ---
if (isset($_GET['delete'])) {
    $delete_id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($delete_id) {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ? AND cafe_id = ?");
        $stmt->execute([$delete_id, $cafe_id]);
        header("Location: categories.php?msg=deleted");
        exit();
    }
}

// Fetch Categories
$stmt = $pdo->prepare("SELECT * FROM categories WHERE cafe_id = ? ORDER BY id DESC");
$stmt->execute([$cafe_id]);
$all_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories | Abyssinia Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 min-h-screen">

    <nav class="sticky top-0 z-40 w-full border-b border-slate-200 bg-white/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-amber-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">A</div>
                <span class="font-extrabold tracking-tight text-slate-800">Abyssinia <span class="text-amber-600">Admin</span></span>
            </div>
            <div class="flex items-center gap-4">
                <a href="admin.php" class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition">Items</a>
                <a href="admin/index.php" class="bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-200">Live Menu</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-10">
        
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Menu Categories</h1>
            <p class="text-slate-500 mt-1">Organize your food and drinks for a better customer experience.</p>
        </div>

        <?php if ($message || isset($_GET['msg'])): ?>
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl font-bold flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <?php echo $message ?: "Successfully deleted! ✅"; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <aside class="lg:col-span-4">
                <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm sticky top-24">
                    <div class="flex items-center gap-3 mb-6 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-xl font-bold text-slate-800">New Category</h2>
                    </div>
                    
                    <form method="POST" class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Category Name</label>
                            <input type="text" name="cat_name" placeholder="e.g. Hot Drinks" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-600 outline-none transition font-semibold">
                        </div>
                        <button type="submit" name="add_category" class="w-full bg-amber-600 text-white font-extrabold py-4 rounded-2xl hover:bg-amber-700 transition active:scale-95 shadow-xl shadow-amber-100 flex items-center justify-center gap-2">
                            Add Category
                        </button>
                    </form>
                </div>
            </aside>

            <section class="lg:col-span-8">
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Name</th>
                                <th class="px-8 py-5 text-right text-xs font-bold text-slate-400 uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($all_categories as $cat): ?>
                            <tr class="group transition hover:bg-slate-50/50">
                                <td class="px-8 py-6">
                                    <span class="font-bold text-slate-700 text-lg"><?php echo htmlspecialchars($cat['name']); ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-3">
                                        <button onclick="openEditModal(<?php echo $cat['id']; ?>, '<?php echo addslashes($cat['name']); ?>')" 
                                            class="p-3 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                        <a href="?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Delete this category?')" 
                                           class="p-3 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 000-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>
    </main>

    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
        <div class="relative bg-white w-full max-w-md p-10 rounded-[2.5rem] shadow-2xl scale-95 transition-transform duration-300" id="modalBox">
            <h2 class="text-2xl font-black text-slate-800 mb-6 flex items-center gap-2">
                <span class="text-indigo-500 italic">Edit</span> Category
            </h2>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="cat_id" id="edit_cat_id">
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest ml-1">Updated Name</label>
                    <input type="text" name="cat_name" id="edit_cat_name" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-bold text-lg">
                </div>
                <div class="flex gap-4">
                    <button type="button" onclick="closeEditModal()" class="flex-1 bg-slate-100 text-slate-500 font-bold py-4 rounded-2xl hover:bg-slate-200 transition">Cancel</button>
                    <button type="submit" name="update_category" class="flex-1 bg-indigo-600 text-white font-bold py-4 rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name) {
            const modal = document.getElementById('editModal');
            const modalBox = document.getElementById('modalBox');
            document.getElementById('edit_cat_id').value = id;
            document.getElementById('edit_cat_name').value = name;
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modalBox.classList.remove('scale-95');
            modalBox.classList.add('scale-100');
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            const modalBox = document.getElementById('modalBox');
            modal.classList.add('opacity-0', 'pointer-events-none');
            modalBox.classList.add('scale-95');
            modalBox.classList.remove('scale-100');
        }
    </script>
</body>
</html>