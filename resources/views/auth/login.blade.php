<?php
$title = 'Login - Davao Metro Shuttle';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center py-12 px-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <img src="/DMS_BOOKING/Images/Updated Company Logo.png" alt="DMS Logo" style="height:80px;width:auto;object-fit:contain;margin:0 auto;display:block;background:#fff;border-radius:12px;padding:6px;">
            <h2 class="text-3xl font-bold mt-3 text-gray-900">Login</h2>
            <p class="text-gray-500 text-sm mt-1">Welcome back to Davao Metro Shuttle</p>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
            <i class="fas fa-exclamation-circle mt-0.5 text-lg"></i>
            <div>
                <p class="font-bold text-sm">Login failed!</p>
                <p class="text-sm mt-1"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST" action="/DMS_BOOKING/customer/login" class="space-y-5">
            <div>
                <label class="block text-gray-700 font-semibold mb-2 text-sm">Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                    autofocus>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2 text-sm">Password</label>
                <input type="password" name="password" required placeholder="Enter your password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-gray-700 text-sm">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 rounded-lg transition transform hover:shadow-lg active:scale-95">
                <i class="fas fa-sign-in-alt mr-2"></i>Login
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
            <a href="/DMS_BOOKING/" class="block text-center text-gray-600 hover:text-gray-900 text-sm font-medium transition">
                <i class="fas fa-home mr-1"></i>Back to Home
            </a>
            <a href="/DMS_BOOKING/login" class="block text-center text-gray-600 hover:text-gray-900 text-sm font-medium transition">
                <i class="fas fa-lock mr-1"></i>Admin Portal
            </a>
        </div>
    </div>
</body>
</html>
