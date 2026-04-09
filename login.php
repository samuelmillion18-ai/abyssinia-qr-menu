<?php
require_once 'db_connection.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Incorrect email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Abyssinia Cafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-[#FDFCFB] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <a href="index.php" class="inline-block mb-6">
                <div class="w-16 h-16 bg-white border border-stone-100 rounded-[2rem] flex items-center justify-center text-amber-600 font-black text-3xl shadow-xl shadow-stone-100 transition hover:rotate-12">A</div>
            </a>
            <h1 class="text-3xl font-black text-stone-900 tracking-tight">Welcome Back</h1>
            <p class="text-stone-500 font-medium">Log in to view your orders</p>
        </div>

        <div class="bg-white p-10 rounded-[3rem] shadow-2xl shadow-stone-200 border border-stone-50">
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-rose-50 text-rose-600 rounded-2xl font-bold text-sm text-center italic border border-rose-100">
                    "<?php echo $error; ?>"
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black uppercase text-stone-400 mb-2 tracking-widest ml-1">Email</label>
                    <input type="email" name="email" required placeholder="you@example.com"
                        class="w-full bg-stone-50 border border-stone-100 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-600 outline-none transition font-semibold text-stone-800">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-stone-400 mb-2 tracking-widest ml-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="w-full bg-stone-50 border border-stone-100 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-600 outline-none transition font-semibold text-stone-800">
                </div>
                
                <button type="submit" class="w-full bg-amber-600 text-white font-black py-5 rounded-2xl hover:bg-amber-700 transition duration-300 shadow-xl shadow-amber-100 active:scale-95 uppercase text-xs tracking-widest">
                    Enter Cafe
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-sm font-medium text-stone-500">
            New here? <a href="signup.php" class="text-amber-600 font-bold hover:underline">Create an account</a>
        </p>
    </div>

</body>
</html>