<?php
$title = 'Account Settings - Davao Metro Shuttle';
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
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-blue-600 via-blue-600 to-blue-700 shadow-lg h-16 md:h-20 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 no-underline">
                <img src="/DMS_BOOKING/Images/Updated Company Logo.png" alt="DMS Express" class="h-10 md:h-14 w-auto">
                <h1 class="text-lg md:text-2xl font-bold text-white hidden sm:block">Davao Metro Shuttle</h1>
            </a>
            
            <div class="flex items-center gap-4">
                <a href="/" class="text-white hover:text-blue-100 transition text-sm font-medium">
                    <i class="fas fa-home mr-2"></i>Back to Home
                </a>
                <div class="relative">
                    <button onclick="toggleUserDropdown()" class="flex items-center justify-center w-10 h-10 rounded-full bg-white text-blue-600 font-bold text-lg hover:bg-orange-100 transition shadow-md">
                        <?php echo strtoupper(substr($_SESSION['user']['name'], 0, 1)); ?>
                    </button>
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white text-gray-900 rounded-lg shadow-xl z-50">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($_SESSION['user']['email']); ?></p>
                        </div>
                        <form action="/DMS_BOOKING/logout" method="POST" class="block">
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition font-medium">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-2xl mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-user-circle text-blue-600 mr-3"></i>Account Settings
            </h1>
            <p class="text-gray-600">Manage your profile information</p>
        </div>

        <!-- Alert Messages -->
        <?php if (!empty($_SESSION['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5 flex-shrink-0"></i>
            <div>
                <p class="font-semibold text-sm">Success</p>
                <p class="text-sm mt-1"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5 flex-shrink-0"></i>
            <div>
                <p class="font-semibold text-sm">Error</p>
                <p class="text-sm mt-1"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Settings Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-6 border-b border-blue-200">
                <h2 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-user text-blue-600 mr-2"></i>Personal Information
                </h2>
                <p class="text-sm text-gray-600 mt-1">Update your profile details</p>
            </div>

            <!-- Form -->
            <form method="POST" action="/DMS_BOOKING/account-settings" class="p-6 md:p-8 space-y-6">
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-blue-600 mr-1"></i>Full Name
                    </label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($user['name']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                           placeholder="Enter your full name">
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-blue-600 mr-1"></i>Email Address
                    </label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($user['email']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                           placeholder="Enter your email address">
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-phone text-blue-600 mr-1"></i>Phone Number
                    </label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                           placeholder="Enter your phone number">
                </div>

                <!-- Member Since -->
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-calendar text-gray-400 mr-2"></i>
                        <strong>Member Since:</strong> 
                        <?php echo date('F j, Y', strtotime($user['created_at'])); ?>
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition font-semibold flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>Save Changes
                    </button>
                    <a href="/" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg transition font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 p-6 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600"></i>Account Information
            </h3>
            <ul class="text-sm text-gray-700 space-y-2">
                <li>✓ Your information is secure and private</li>
                <li>✓ Email changes will require verification</li>
                <li>✓ Changes are saved immediately</li>
            </ul>
        </div>
    </div>

    <script>
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            if (!event.target.closest('[onclick="toggleUserDropdown()"]') && 
                !event.target.closest('#userDropdown')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
