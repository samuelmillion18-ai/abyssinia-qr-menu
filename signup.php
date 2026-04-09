<?php
require_once 'db_connection.php';
session_start();

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'];

    if ($full_name && $email && $password) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt->execute([$full_name, $email, $hashed_password]);
            $message = "Account created! You can now login.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "This email is already registered.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Abyssinia Cafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-[#FDFCFB] min-h-screen flex items-center justify-center p-4">

    <div class="max-w-5xl w-full bg-white rounded-[3rem] shadow-2xl shadow-stone-200 overflow-hidden flex flex-col md:flex-row border border-stone-100">
        <div class="md:w-1/2 bg-amber-600 relative p-12 flex flex-col justify-between text-white">
            <div class="absolute inset-0 opacity-20" style="background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-amber-600 font-black text-2xl mb-8">A</div>
                <h2 class="text-4xl font-black leading-tight">Start your morning with us.</h2>
                <p class="mt-4 text-amber-100 font-medium text-lg">Create an account to save your favorite dishes and order faster.</p>
            </div>
            <div class="relative z-10 text-sm font-bold opacity-70 tracking-widest uppercase">© 2026 Abyssinia Cafe</div>
        </div>

        <div class="md:w-1/2 p-8 md:p-16">
            <h1 class="text-3xl font-black text-stone-900 mb-2">Create Account</h1>
            <p class="text-stone-500 mb-8 font-medium">Join our community of coffee lovers.</p>

            <?php if ($message): ?>
                <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-2xl font-bold text-sm border border-emerald-100">
                    <?php echo $message; ?> <a href="login.php" class="underline">Login here</a>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-rose-50 text-rose-700 rounded-2xl font-bold text-sm border border-rose-100"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-[10px] font-black uppercase text-stone-400 mb-2 tracking-widest">Full Name</label>
                    <input type="text" name="full_name" required placeholder="Abebe Bikila"
                        class="w-full bg-stone-50 border border-stone-100 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-600 outline-none transition font-semibold">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-stone-400 mb-2 tracking-widest">Email Address</label>
                    <input type="email" name="email" required placeholder="abebe@example.com"
                        class="w-full bg-stone-50 border border-stone-100 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-600 outline-none transition font-semibold">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-stone-400 mb-2 tracking-widest">Password</label>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="w-full bg-stone-50 border border-stone-100 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-600 outline-none transition font-semibold">
                </div>
                <button type="submit" class="w-full bg-stone-900 text-white font-black py-5 rounded-2xl hover:bg-amber-600 transition duration-300 shadow-xl shadow-stone-200 active:scale-95">
                    Sign Up
                </button>
            </form>

            <p class="mt-8 text-center text-sm font-medium text-stone-500">
                Already have an account? <a href="login.php" class="text-amber-600 font-bold hover:underline">Log In</a>
            </p>
        </div>
    </div>
</body>
</html>