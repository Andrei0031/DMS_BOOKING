<?php
$title = 'Admin Login - Davao Metro Shuttle';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @keyframes busRun {
            0% { left: -50px; }
            100% { left: 100%; }
        }
        
        @keyframes roadMove {
            0% { background-position: 0 0; }
            100% { background-position: 100px 0; }
        }
        
        @keyframes bgColorShift {
            0% { background: linear-gradient(to right, #2563eb, #1d4ed8); }
            25% { background: linear-gradient(135deg, #3b82f6, #0ea5e9); }
            50% { background: linear-gradient(to left, #1e40af, #0369a1); }
            75% { background: linear-gradient(90deg, #3b82f6, #06b6d4); }
            100% { background: linear-gradient(to right, #2563eb, #1d4ed8); }
        }
        
        @keyframes pageBgAnimation {
            0% { 
                background: linear-gradient(45deg, #ffffff 0%, #dbeafe 25%, #60a5fa 50%, #dbeafe 75%, #ffffff 100%);
                background-size: 400% 400%;
                background-position: 0% 50%;
            }
            25% { 
                background-position: 50% 100%;
            }
            50% { 
                background-position: 100% 50%;
            }
            75% { 
                background-position: 50% 0%;
            }
            100% { 
                background: linear-gradient(45deg, #ffffff 0%, #dbeafe 25%, #60a5fa 50%, #dbeafe 75%, #ffffff 100%);
                background-size: 400% 400%;
                background-position: 0% 50%;
            }
        }
        
        .animated-btn {
            position: relative;
            overflow: hidden;
            background: linear-gradient(to right, #2563eb, #1d4ed8);
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-weight: bold;
        }
        
        .animated-btn:hover {
            background: linear-gradient(to right, #1d4ed8, #1e40af);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }
        
        .animated-btn.animating {
            animation: bgColorShift 1.2s ease-in-out forwards;
        }
        
        .road-container {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            overflow: hidden;
            opacity: 0;
            pointer-events: none;
            z-index: 5;
            background: rgba(0, 0, 0, 0.1);
        }
        
        .road-container.active {
            animation: showAnimation 1.2s ease-in-out forwards;
        }
        
        @keyframes showAnimation {
            0% { opacity: 0; }
            10% { opacity: 0.7; }
            90% { opacity: 0.7; }
            100% { opacity: 0; }
        }
        
        .road {
            position: absolute;
            width: 100%;
            height: 3px;
            background: repeating-linear-gradient(
                to right,
                #fbbf24 0px,
                #fbbf24 20px,
                transparent 20px,
                transparent 40px
            );
            animation: roadMove 0.8s linear infinite;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .bus-icon {
            position: absolute;
            animation: busRun 0.8s ease-in-out forwards;
            font-size: 1.5rem;
            color: white;
            text-shadow: 0 0 12px rgba(0, 0, 0, 0.4);
        }
        
        .btn-text {
            position: relative;
            z-index: 10;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        body {
            animation: pageBgAnimation 6s ease-in-out infinite !important;
            background: linear-gradient(45deg, #ffffff 0%, #dbeafe 25%, #60a5fa 50%, #dbeafe 75%, #ffffff 100%);
            background-size: 400% 400%;
            will-change: background;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4" style="background: linear-gradient(45deg, #ffffff 0%, #dbeafe 25%, #60a5fa 50%, #dbeafe 75%, #ffffff 100%); background-size: 400% 400%;">
    <div class="flex flex-col md:flex-row w-full max-w-4xl rounded-3xl shadow-2xl overflow-hidden bg-white">
        
        <!-- Left: Image -->
        <div class="hidden md:flex items-center justify-center p-6 bg-gradient-to-br from-white to-blue-50 md:w-1/2">
            <div class="w-full">
                <img src="/DMS_BOOKING/Images/ads.jpg" alt="Admin Portal" 
                    class="w-full h-auto object-cover rounded-2xl shadow-lg">
            </div>
        </div>
        
        <!-- Right: Login Form -->
        <div class="flex items-center justify-center p-6 md:p-8 bg-white md:w-1/2">
            <div class="w-full">
                <!-- Header -->
                <div class="text-center mb-6">
                    <img src="/DMS_BOOKING/Images/Updated Company Logo.png" alt="DMS Express" 
                        class="h-16 w-auto mx-auto mb-2 object-contain">
                    <p class="text-gray-500 text-xs mt-1">Management Portal</p>
                </div>

                <!-- Error Message -->
                <?php if (!empty($_SESSION['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg mb-4 flex items-start gap-2">
                    <i class="fas fa-exclamation-circle mt-0.5 text-sm flex-shrink-0"></i>
                    <div>
                        <p class="font-bold text-xs">Login Failed</p>
                        <p class="text-xs mt-0.5"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="/DMS_BOOKING/login" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1.5 text-xs">Username</label>
                        <input type="text" name="username" required placeholder="admin"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition text-sm"
                            autofocus>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1.5 text-xs">Password</label>
                        <input type="password" name="password" required placeholder="Enter your password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition text-sm">
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-gray-700 text-xs">Remember me</span>
                        </label>
                    </div>

                    <button type="button" id="login-btn" class="animated-btn w-full text-white font-bold py-2.5 rounded-lg text-sm relative">
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt"></i>Admin Login
                        </span>
                        <div class="road-container" id="road-container">
                            <div class="road"></div>
                            <i class="bus-icon fas fa-bus"></i>
                        </div>
                    </button>
                </form>

                <!-- Footer Links -->
                <div class="mt-5 pt-4 border-t border-gray-200 space-y-2">
                    <a href="/DMS_BOOKING/" class="block text-center text-gray-600 hover:text-gray-900 text-xs font-medium transition">
                        <i class="fas fa-home mr-1"></i>Back to Home
                    </a>
                </div>

                <!-- Security Badge -->
                <div class="mt-3 text-center">
                    <p class="text-gray-500 text-xs flex items-center justify-center gap-1">
                        <i class="fas fa-lock text-green-600"></i> Secure Access
                    </p>
                </div>
                </div>
            </div>
        </div>

    </div>
    
    <script>
        document.getElementById('login-btn').addEventListener('click', function(e) {
            e.preventDefault();
            
            const roadContainer = document.getElementById('road-container');
            const loginForm = document.querySelector('form');
            const loginBtn = document.getElementById('login-btn');
            
            // Disable button and show animation
            loginBtn.disabled = true;
            loginBtn.classList.add('animating');
            roadContainer.classList.add('active');
            
            // Submit form after animation completes (1.2s)
            setTimeout(function() {
                loginForm.submit();
            }, 1200);
        });
    </script>
</body>
</html>
