<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Davao Metro Shuttle - Bus Booking'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Flatpickr date picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        /* ─── Flatpickr — Spacious iOS-style Calendar ─── */
        .flatpickr-calendar {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            background: #f8f9fb !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 18px !important;
            box-shadow: 0 8px 40px rgba(0,0,0,.12), 0 2px 6px rgba(0,0,0,.04) !important;
            padding: 0 !important;
            width: 400px !important;
            overflow: hidden !important;
        }
        .flatpickr-calendar.arrowTop:before,
        .flatpickr-calendar.arrowTop:after { display: none !important; }

        /* ── Header ── */
        .flatpickr-months {
            background: #fff !important;
            padding: 18px 16px 14px !important;
            position: relative !important;
            align-items: center !important;
            border-bottom: 1px solid #edf0f3 !important;
            height: auto !important;
        }
        .flatpickr-month {
            background: transparent !important;
            color: #1a1a2e !important;
            fill: #1a1a2e !important;
            height: auto !important;
            line-height: 1 !important;
            overflow: visible !important;
        }
        .flatpickr-current-month {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            color: #1a1a2e !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
            justify-content: center !important;
            position: static !important;
            width: auto !important;
            left: auto !important;
            height: auto !important;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background: transparent !important;
            color: #1a1a2e !important;
            font-weight: 700 !important;
            font-size: 1.1rem !important;
            border: none !important;
            padding: 0 2px !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            cursor: pointer !important;
        }
        .flatpickr-current-month input.cur-year {
            color: #1a1a2e !important;
            font-weight: 700 !important;
            font-size: 1.1rem !important;
        }
        .numInputWrapper span { display: none !important; }

        /* Prev / next arrows */
        .flatpickr-prev-month,
        .flatpickr-next-month {
            background: transparent !important;
            border: none !important;
            border-radius: 8px !important;
            width: 34px !important; height: 34px !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
            padding: 0 !important;
            transition: background .15s !important;
            top: 50% !important; transform: translateY(-50%) !important;
            position: absolute !important;
        }
        .flatpickr-prev-month { left: 16px !important; }
        .flatpickr-next-month { right: 16px !important; }
        .flatpickr-prev-month:hover,
        .flatpickr-next-month:hover { background: #f1f3f6 !important; }
        .flatpickr-prev-month svg,
        .flatpickr-next-month svg { fill: #3b82f6 !important; width: 16px !important; height: 16px !important; }

        /* ── Weekday row ── */
        .flatpickr-weekdays {
            background: transparent !important;
            padding: 14px 18px 6px !important;
            height: auto !important;
        }
        .flatpickr-weekdaycontainer {
            display: flex !important;
            justify-content: space-around !important;
        }
        span.flatpickr-weekday {
            background: transparent !important;
            color: #94a3b8 !important;
            font-weight: 600 !important;
            font-size: .82rem !important;
            text-transform: none !important;
            letter-spacing: 0 !important;
            line-height: 1.8rem !important;
            flex: 1 !important;
            max-width: none !important;
        }

        /* ── Days grid — force big spacious cells ── */
        .flatpickr-innerContainer {
            padding: 4px 18px 18px !important;
            display: block !important;
        }
        .flatpickr-rContainer {
            display: block !important;
            width: 100% !important;
        }
        .flatpickr-days {
            width: 100% !important;
            border: none !important;
        }
        .dayContainer {
            padding: 0 !important;
            max-width: none !important;
            min-width: 0 !important;
            width: 100% !important;
            display: grid !important;
            grid-template-columns: repeat(7, 1fr) !important;
            gap: 6px !important;
            justify-items: center !important;
        }

        .flatpickr-day {
            border-radius: 12px !important;
            font-size: 1.05rem !important;
            font-weight: 500 !important;
            color: #1a1a2e !important;
            width: 42px !important;
            height: 42px !important;
            max-width: 42px !important;
            line-height: 42px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            flex: none !important;
            border: 2px solid transparent !important;
            transition: all .12s ease !important;
        }
        .flatpickr-day:hover {
            background: #e0edff !important;
            color: #2563eb !important;
            border-color: transparent !important;
        }
        /* Today — dotted ring */
        .flatpickr-day.today {
            background: transparent !important;
            color: #3b82f6 !important;
            font-weight: 600 !important;
            border: 2px dashed #93bbf3 !important;
        }
        .flatpickr-day.today:hover {
            background: #e0edff !important;
            border-color: #93bbf3 !important;
        }
        /* Selected — solid blue rounded square */
        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background: #3b82f6 !important;
            border: 2px solid #3b82f6 !important;
            color: #fff !important;
            font-weight: 700 !important;
            box-shadow: 0 3px 12px rgba(59,130,246,.35) !important;
        }
        .flatpickr-day.today.selected,
        .flatpickr-day.today.selected:hover {
            background: #3b82f6 !important;
            border: 2px solid #3b82f6 !important;
            color: #fff !important;
        }
        /* Past / disabled */
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            color: #cbd5e1 !important;
            background: transparent !important;
            border-color: transparent !important;
            cursor: default !important;
        }
        /* Out-of-month — visible but faded */
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: #d1d5db !important;
            background: transparent !important;
            border-color: transparent !important;
        }
        .flatpickr-day.prevMonthDay:hover,
        .flatpickr-day.nextMonthDay:hover {
            background: #f1f5f9 !important;
            color: #9ca3af !important;
        }

        /* ── Input wrapper ── */
        .fp-input-wrap { position: relative; }
        .fp-input-wrap .fp-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #3b82f6; pointer-events: none; font-size: .9rem;
        }
        .fp-input-wrap input[readonly] {
            padding-left: 2.2rem; cursor: pointer; background: #fff;
        }
        .fp-input-wrap input[readonly]:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59,130,246,.12) !important;
            outline: none;
        }

        /* ── Mobile ── */
        @media (max-width: 640px) {
            .flatpickr-calendar { width: 100% !important; max-width: 370px !important; }
            .flatpickr-day { width: 40px !important; height: 40px !important; max-width: 40px !important; line-height: 40px !important; font-size: .95rem !important; }
            .dayContainer { gap: 5px !important; }
            .flatpickr-innerContainer { padding: 4px 16px 16px !important; }
        }
    </style>
    <style>
        /* Smooth scroll animation */
        html {
            scroll-behavior: smooth;
        }
        
        /* Override all orange colors with #e3dec9 - COMPREHENSIVE */
        
        /* ALL text-orange classes */
        .text-orange-50, .text-orange-100, .text-orange-200, .text-orange-300, 
        .text-orange-400, .text-orange-500, .text-orange-600, .text-orange-700,
        .text-orange-800, .text-orange-900 { color: #e3dec9 !important; }

        /* ALL bg-orange classes */
        .bg-orange-50 { background-color: #faf8f3 !important; }
        .bg-orange-100, .bg-orange-200, .bg-orange-300, .bg-orange-400,
        .bg-orange-500, .bg-orange-600, .bg-orange-700, .bg-orange-800, .bg-orange-900 {
            background-color: #e3dec9 !important;
        }

        /* ALL border-orange classes */
        .border-orange-50, .border-orange-100, .border-orange-200, .border-orange-300,
        .border-orange-400, .border-orange-500, .border-orange-600, .border-orange-700,
        .border-orange-800, .border-orange-900 { border-color: #e3dec9 !important; }

        /* Focus states with orange */
        .focus\:border-orange-400:focus, .focus\:border-orange-500:focus { border-color: #e3dec9 !important; }

        /* Hover states */
        .hover\:text-orange-100:hover, .hover\:text-orange-50:hover { color: #e3dec9 !important; }
        .hover\:bg-orange-50:hover { background-color: #faf8f3 !important; }
        .hover\:bg-orange-100:hover, .hover\:bg-orange-50:hover { background-color: #e3dec9 !important; }
        .hover\:border-orange-500:hover { border-color: #e3dec9 !important; }

        /* ALL Gradient from-orange */
        .from-orange-400, .from-orange-500, .from-orange-600, .from-orange-700 {
            --tw-gradient-from: #e3dec9 !important;
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgb(227 222 201 / 0)) !important;
        }
        
        /* ALL Gradient to-orange */
        .to-orange-400, .to-orange-500, .to-orange-600, .to-orange-700 {
            --tw-gradient-to: #e3dec9 !important;
        }

        /* ALL Gradient via-orange */
        .via-orange-400, .via-orange-500, .via-orange-600 {
            --tw-gradient-stops: var(--tw-gradient-from), #e3dec9, var(--tw-gradient-to, #e3dec9) !important;
        }

        /* Hover gradient states */
        .hover\:from-orange-600:hover, .hover\:from-orange-700:hover {
            --tw-gradient-from: #e3dec9 !important;
        }

        .hover\:to-orange-600:hover, .hover\:to-orange-700:hover {
            --tw-gradient-to: #e3dec9 !important;
        }
        
        /* Ring colors for outlines */
        .ring-orange-500 { --tw-ring-color: #e3dec9 !important; }
        
        /* Divide colors */
        .divide-orange-500 { border-color: #e3dec9 !important; }
        
        /* Animated gradient for nav bar */
        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        .animate-gradient {
            background: linear-gradient(-45deg, #1e40af, #3b82f6, #60a5fa, #e3dec9, #fbbf24, #3b82f6, #1e40af);
            background-size: 300% 300%;
            animation: gradientShift 8s ease infinite;
        }
        
        /* Select dropdown styling - remove blue highlight */
        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        
        select option {
            background-color: white !important;
            color: #1f2937 !important;
            padding: 8px;
        }
        
        select option:checked {
            background: white !important;
            background-color: white !important;
            color: #1f2937 !important;
            box-shadow: none !important;
        }
        
        select option:hover {
            background-color: #f3f4f6 !important;
            color: #1f2937 !important;
        }
        
        /* Modal animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                backdrop-filter: blur(0px);
            }
            to {
                opacity: 1;
                backdrop-filter: blur(4px);
            }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes slideDown {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
        }
        
        .modal-open {
            animation: fadeIn 0.3s ease-out forwards;
        }
        
        .modal-close {
            animation: fadeIn 0.3s ease-out reverse;
        }
        
        .modal-content-open {
            animation: slideUp 0.3s ease-out forwards;
        }
        
        .modal-content-close {
            animation: slideDown 0.3s ease-out forwards;
        }
        
        /* Animated underline for navigation */
        .nav-link {
            position: relative;
            display: inline-block;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 3px;
            bottom: -8px;
            left: 0;
            background-color: #e3dec9;
            transform: scaleX(0);
            transform-origin: bottom right;
            transition: transform 0.3s ease;
        }
        
        .nav-link:hover::after,
        .nav-link.active::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }
        
        .nav-link.active {
            color: #fff !important;
            font-weight: 600;
        }
        
        /* Animated text for greeting */
        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .animated-greeting {
            animation: fadeInScale 1s ease-out, bounce 3s ease-in-out infinite;
            animation-delay: 0.5s, 2s;
            display: inline-block;
        }
        
        /* Animated button for Book Now */
        @keyframes buttonPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(37, 99, 235, 0);
            }
        }
        
        .btn-book-now {
            position: relative;
            animation: buttonPulse 2s infinite;
        }
        
        .btn-book-now::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            border-radius: inherit;
            animation: buttonPulse 2s infinite;
            animation-delay: 0.3s;
            z-index: -1;
        }
        
        /* Questioning animation */
        @keyframes tiltQuestion {
            0%, 100% {
                transform: rotate(0deg) scale(1);
            }
            50% {
                transform: rotate(2deg) scale(1.02);
            }
        }
        
        .animated-question {
            animation: tiltQuestion 2.5s ease-in-out infinite;
            display: inline-block;
        }
        
        /* Route selector button styles */
        .route-btn {
            transition: all 0.3s ease-in-out;
            border: 2px solid #e3dec9;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .route-btn.active {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border-color: #1e40af;
        }
        
        .route-btn:hover {
            border-color: #1e40af;
        }
        
        /* Custom select dropdown styling */
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px !important;
        }
        
        select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        select option {
            padding: 12px;
            background: white;
        }
        
        select option:checked {
            background: linear-gradient(#3b82f6, #3b82f6);
            color: white;
        }
        
        select option:disabled {
            color: #9ca3af;
            background: #f3f4f6;
        }
        
        /* Expandable routes animation */
        .route-subroutes {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-in-out;
        }
        
        .route-subroutes.open {
            max-height: 500px;
        }
        
        .route-toggle {
            transition: transform 0.3s ease-in-out;
        }
        
        .route-toggle.open {
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="bg-white font-sans text-gray-900">
    <!-- Navigation -->
    <nav class="animate-gradient text-white shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20">
                <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="flex items-center gap-2 md:gap-3 hover:opacity-85 transition">
                    <img src="/DMS_BOOKING/Images/company-logo.png" alt="Davao Metro Shuttle" class="h-12 md:h-14 w-auto object-contain">
                    <h1 class="text-lg md:text-2xl font-bold text-white hidden sm:block">Davao Metro Shuttle</h1>
                </a>
                
                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" onclick="toggleMobileMenu()" class="md:hidden text-white text-2xl p-2">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex gap-4 md:gap-6 items-center">
                    <a href="#" onclick="setActiveNav('home'); window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="nav-link active text-gray-100 hover:text-white transition text-sm font-medium" data-nav="home">Home</a>
                    <a href="#features" onclick="setActiveNav('features')" class="nav-link text-gray-100 hover:text-white transition text-sm font-medium" data-nav="features">Features</a>
                    <a href="#" onclick="setActiveNav('contact'); document.getElementById('footer-contact').scrollIntoView({behavior: 'smooth'}); return false;" class="nav-link text-gray-100 hover:text-white transition text-sm font-medium" data-nav="contact">Contact</a>
                    
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['type'] === 'customer'): ?>
                        <!-- User Avatar Dropdown -->
                        <div class="relative">
                            <button onclick="toggleUserDropdown()" class="flex items-center justify-center w-10 h-10 rounded-full bg-white text-blue-600 font-bold text-lg hover:bg-orange-100 transition shadow-md">
                                <?php echo strtoupper(substr($_SESSION['user']['name'], 0, 1)); ?>
                            </button>
                            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white text-gray-900 rounded-lg shadow-xl z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($_SESSION['user']['email']); ?></p>
                                </div>
                                <button onclick="openAccountSettingsModal()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 transition font-medium border-b border-gray-100 bg-white border-0 cursor-pointer">
                                    <i class="fas fa-cog mr-2 text-blue-600"></i>Account Settings
                                </button>
                                <form action="/DMS_BOOKING/logout" method="POST" class="block">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition font-medium">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <button onclick="openAuthModal()" class="bg-white hover:bg-orange-100 text-blue-600 px-4 md:px-6 py-2 rounded-lg transition font-semibold text-sm shadow-md hover:shadow-lg">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Mobile Navigation Menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4 space-y-2">
                <a href="#" onclick="setActiveNav('home'); window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="block text-gray-100 hover:text-white transition text-sm font-medium px-4 py-2">Home</a>
                <a href="#features" onclick="setActiveNav('features')" class="block text-gray-100 hover:text-white transition text-sm font-medium px-4 py-2">Features</a>
                <a href="#" onclick="setActiveNav('contact'); document.getElementById('footer-contact').scrollIntoView({behavior: 'smooth'}); return false;" class="block text-gray-100 hover:text-white transition text-sm font-medium px-4 py-2">Contact</a>
                
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['type'] === 'customer'): ?>
                    <div class="px-4 py-2 border-t border-gray-800 space-y-2">
                        <p class="text-sm font-semibold text-white mb-3"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></p>
                        <button onclick="openAccountSettingsModal(); toggleMobileMenu();" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-semibold text-sm border-0 cursor-pointer">
                            <i class="fas fa-cog mr-2"></i>Account Settings
                        </button>
                        <form action="/DMS_BOOKING/logout" method="POST" class="block">
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition font-semibold text-sm">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="px-4">
                        <button onclick="openAuthModal(); toggleMobileMenu();" class="w-full bg-white hover:bg-orange-100 text-blue-600 px-4 py-2 rounded-lg transition font-semibold text-sm shadow-md">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Auth Modal -->
    <div id="authModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 modal-open" onclick="if(event.target === this) closeAuthModal()">
        <!-- Login Modal -->
        <div id="loginModal" class="bg-white rounded-2xl shadow-2xl max-w-md w-full modal-content-open">
            <div class="p-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Login to DMS Booking</h2>
                    <button onclick="closeAuthModal()" class="text-gray-400 hover:text-gray-600 text-2xl transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Login Form -->
                <form action="/DMS_BOOKING/customer/login" method="POST" class="space-y-5">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Email Address</label>
                        <input type="email" name="email" placeholder="example@gmail.com" required class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition text-gray-900 placeholder-gray-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition text-gray-900 placeholder-gray-500">
                    </div>

                    <!-- Remember me & Forgot password -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center text-gray-700 cursor-pointer hover:text-gray-900 transition">
                            <input type="checkbox" name="remember" class="w-4 h-4 mr-2 cursor-pointer">
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="text-blue-500 hover:text-blue-600 transition font-semibold">Forgot Password?</a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition shadow-lg transform hover:scale-105 text-base mt-8">
                        Login to your account
                    </button>
                </form>

                <!-- Register Link -->
                <div class="text-center mt-6 text-gray-600 text-sm">
                    Not registered? <a href="#" onclick="switchToRegister(event)" class="text-blue-500 hover:text-blue-600 font-semibold transition">Create account</a>
                </div>
            </div>
        </div>

        <!-- Register Modal -->
        <div id="registerModal" class="hidden bg-white rounded-2xl shadow-2xl max-w-md w-full modal-content-open">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Create DMS Booking Account</h2>
                    <button onclick="closeAuthModal()" class="text-gray-400 hover:text-gray-600 text-2xl transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Register Form -->
                <form action="/DMS_BOOKING/register" method="POST" class="space-y-3">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1 text-sm">Full Name</label>
                        <input type="text" name="name" placeholder="John Doe" required class="w-full px-4 py-2 bg-gray-50 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition text-gray-900 placeholder-gray-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1 text-sm">Email Address</label>
                        <input type="email" name="email" placeholder="example@gmail.com" required class="w-full px-4 py-2 bg-gray-50 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition text-gray-900 placeholder-gray-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1 text-sm">Mobile Number</label>
                        <input type="tel" name="phone" placeholder="+63 9XX XXX XXXX" class="w-full px-4 py-2 bg-gray-50 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition text-gray-900 placeholder-gray-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1 text-sm">Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-2 bg-gray-50 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition text-gray-900 text-sm">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1 text-sm">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="w-full px-4 py-2 bg-gray-50 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition text-gray-900 text-sm">
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition shadow-lg transform hover:scale-105 text-base mt-6">
                        Create Account
                    </button>
                </form>

                <!-- Back to Login Link -->
                <div class="text-center mt-4 text-gray-600 text-sm">
                    Already have an account? <a href="#" onclick="switchToLogin(event)" class="text-blue-500 hover:text-blue-600 font-semibold transition">Sign in</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="mx-auto max-w-7xl px-4 mt-4">
            <div id="success-alert" class="bg-green-50 border-l-4 border-green-600 text-green-800 px-6 py-4 rounded-lg flex justify-between items-center shadow-md transition-opacity duration-500">
                <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
                <button onclick="document.getElementById('success-alert').parentElement.style.opacity='0'; setTimeout(function(){document.getElementById('success-alert').parentElement.style.display='none';},500)" class="text-green-600 font-bold text-lg">&times;</button>
            </div>
        </div>
        <script>
            (function() {
                var alert = document.getElementById('success-alert');
                if (alert) {
                    setTimeout(function() {
                        alert.parentElement.style.opacity = '0';
                        setTimeout(function() {
                            alert.parentElement.style.display = 'none';
                        }, 500);
                    }, 2500);
                }
            })();
        </script>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mx-auto max-w-7xl px-4 mt-4">
            <div id="error-alert" class="bg-red-50 border-l-4 border-red-600 text-red-800 px-6 py-4 rounded-lg flex justify-between items-center shadow-md transition-opacity duration-500">
                <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                <button onclick="document.getElementById('error-alert').parentElement.style.opacity='0'; setTimeout(function(){document.getElementById('error-alert').parentElement.style.display='none';},500)" class="text-red-600 font-bold text-lg">&times;</button>
            </div>
        </div>
        <script>
            (function() {
                var alert = document.getElementById('error-alert');
                if (alert) {
                    setTimeout(function() {
                        alert.parentElement.style.opacity = '0';
                        setTimeout(function() {
                            alert.parentElement.style.display = 'none';
                        }, 500);
                    }, 2500);
                }
            })();
        </script>
    <?php endif; ?>

    <?php if (isset($_SESSION['warning'])): ?>
        <div class="mx-auto max-w-7xl px-4 mt-4">
            <div id="warning-alert" class="bg-yellow-50 border-l-4 border-yellow-600 text-yellow-800 px-6 py-4 rounded-lg flex justify-between items-center shadow-md transition-opacity duration-500">
                <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['warning']); unset($_SESSION['warning']); ?></span>
                <button onclick="document.getElementById('warning-alert').parentElement.style.opacity='0'; setTimeout(function(){document.getElementById('warning-alert').parentElement.style.display='none';},500)" class="text-yellow-600 font-bold text-lg">&times;</button>
            </div>
        </div>
        <script>
            (function() {
                var alert = document.getElementById('warning-alert');
                if (alert) {
                    setTimeout(function() {
                        alert.parentElement.style.opacity = '0';
                        setTimeout(function() {
                            alert.parentElement.style.display = 'none';
                        }, 500);
                    }, 2500);
                }
            })();
        </script>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="min-h-screen">
        <?php if (isset($content)) echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-blue-700 via-blue-600 to-orange-600 text-white mt-12 md:mt-16">
        <div class="max-w-7xl mx-auto px-4 py-8 md:py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 mb-8">
                <div>
                    <h3 class="text-base md:text-lg font-bold mb-3 md:mb-4 flex items-center gap-2 text-white">
                        <i class="fas fa-shuttle-van text-white"></i>Davao Metro Shuttle
                    </h3>
                    <p class="text-white text-sm md:text-base">Your trusted partner for convenient and comfortable bus travel in Davao.</p>
                </div>
                <div>
                    <h3 class="text-base md:text-lg font-bold mb-3 md:mb-4 text-white">Quick Links</h3>
                    <ul class="space-y-2 text-white text-sm md:text-base">
                        <li><a href="#" class="hover:text-e3dec9 transition"><i class="fas fa-angle-right mr-2"></i>Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-e3dec9 transition"><i class="fas fa-angle-right mr-2"></i>Terms & Conditions</a></li>
                        <li><a href="#" class="hover:text-e3dec9 transition"><i class="fas fa-angle-right mr-2"></i>FAQ</a></li>
                    </ul>
                </div>
                <div id="footer-contact">
                    <h3 class="text-base md:text-lg font-bold mb-3 md:mb-4 text-white">Contact Us</h3>
                    <p class="text-white mb-2 text-sm md:text-base"><i class="fas fa-envelope mr-2 text-e3dec9"></i>support@dmsshuttle.ph</p>
                    <p class="text-white mb-2 text-sm md:text-base"><i class="fas fa-phone mr-2 text-e3dec9"></i>+63-82-XXX-XXXX</p>
                    <p class="text-white text-sm md:text-base"><i class="fas fa-map-marker-alt mr-2 text-e3dec9"></i>Davao City, Philippines</p>
                </div>
            </div>
            <div class="border-t border-e3dec9 pt-6 md:pt-8 text-center text-white">
                <p class="text-sm md:text-base font-semibold">&copy; 2026 Davao Metro Shuttle. All rights reserved.</p>
                <div class="flex justify-center gap-4 mt-4">
                    <a href="#" class="text-e3dec9 hover:text-white transition"><i class="fab fa-facebook text-lg md:text-xl"></i></a>
                    <a href="#" class="text-e3dec9 hover:text-white transition"><i class="fab fa-twitter text-lg md:text-xl"></i></a>
                    <a href="#" class="text-e3dec9 hover:text-white transition"><i class="fab fa-instagram text-lg md:text-xl"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        }

        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            if (!dropdown) return;
            const btn = event.target.closest('button[onclick="toggleUserDropdown()"]');
            if (!btn && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        function setActiveNav(navItem) {
            // Remove active class from all nav links
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => link.classList.remove('active'));
            
            // Add active class to clicked nav link
            const activeLink = document.querySelector(`[data-nav="${navItem}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }

        function openAuthModal() {
            const modal = document.getElementById('authModal');
            const loginModal = document.getElementById('loginModal');
            const registerModal = document.getElementById('registerModal');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex', 'modal-open');
            loginModal.classList.remove('hidden');
            registerModal.classList.add('hidden');
            loginModal.classList.add('modal-content-open');
        }

        function closeAuthModal() {
            const modal = document.getElementById('authModal');
            const loginModal = document.getElementById('loginModal');
            const registerModal = document.getElementById('registerModal');
            
            loginModal.classList.remove('modal-content-open');
            registerModal.classList.remove('modal-content-open');
            loginModal.classList.add('modal-content-close');
            registerModal.classList.add('modal-content-close');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex', 'modal-open');
                loginModal.classList.add('hidden');
                registerModal.classList.add('hidden');
                loginModal.classList.remove('modal-content-close');
                registerModal.classList.remove('modal-content-close');
            }, 300);
        }

        function switchToRegister(e) {
            e.preventDefault();
            const loginModal = document.getElementById('loginModal');
            const registerModal = document.getElementById('registerModal');
            
            loginModal.classList.add('modal-content-close');
            
            setTimeout(() => {
                loginModal.classList.add('hidden');
                registerModal.classList.remove('hidden');
                registerModal.classList.add('modal-content-open');
                loginModal.classList.remove('modal-content-close');
            }, 300);
        }

        function switchToLogin(e) {
            e.preventDefault();
            const loginModal = document.getElementById('loginModal');
            const registerModal = document.getElementById('registerModal');
            
            registerModal.classList.add('modal-content-close');
            
            setTimeout(() => {
                registerModal.classList.add('hidden');
                loginModal.classList.remove('hidden');
                loginModal.classList.add('modal-content-open');
                registerModal.classList.remove('modal-content-close');
            }, 300);
        }

        function switchTab(tab) {
            // This function is kept for compatibility but no longer used
        }
        
        // Store current route stops globally
        let currentStops = [];
        
        // Route selector
        function selectRoute(route) {
            // Update button states
            document.getElementById('route-tagum-pitx').classList.remove('active');
            document.getElementById('route-pitx-tagum').classList.remove('active');
            
            const tagumStops = ['Tagum City', 'Trento', 'San France', 'Bayugan', 'Butuan City', 'Kitcharao', 'Surigao City', 'Liloan / Padre Burgos', 'Sogod', 'Bato', 'Bay-Bay', 'Cr. Mahaplag', 'Abuyog', 'Tacloban / Santa Rita', 'Calbiga', 'Catbalogan', 'Calbayog', 'Allen', 'Matnog', 'Sorsogon City', 'Legaspi', 'Bapo', 'Naga City', 'Sipocot', 'Gumaca', 'Lucena', 'Turbina', 'PITX / CUBAO'];
            const pitxStops = ['PITX / CUBAO', 'Turbina', 'Lucena', 'Gumaca', 'Sipocot', 'Naga City', 'Bapo', 'Legaspi', 'Sorsogon City', 'Matnog', 'Allen', 'Calbayog', 'Catbalogan', 'Calbiga', 'Tacloban / Santa Rita', 'Abuyog', 'Cr. Mahaplag', 'Bay-Bay', 'Bato', 'Sogod', 'Liloan / Padre Burgos', 'Surigao City', 'Kitcharao', 'Butuan City', 'Bayugan', 'San France', 'Trento', 'Tagum City', 'Davao City'];
            const fromSelect = document.querySelector('select[name="from_location"]');
            const toSelect = document.querySelector('select[name="to_location"]');
            
            if (route === 'tagum-to-pitx') {
                document.getElementById('route-tagum-pitx').classList.add('active');
                currentStops = tagumStops;
                updateSelectOptions(fromSelect, tagumStops);
                fromSelect.value = 'Tagum City';
                updateToSelectOptions(toSelect, tagumStops, 'Tagum City');
                toSelect.value = 'PITX / CUBAO';
            } else if (route === 'pitx-to-tagum') {
                document.getElementById('route-pitx-tagum').classList.add('active');
                currentStops = pitxStops;
                updateSelectOptions(fromSelect, pitxStops);
                fromSelect.value = 'PITX / CUBAO';
                updateToSelectOptions(toSelect, pitxStops, 'PITX / CUBAO');
                toSelect.value = 'Davao City';
            }
            
            // Add event listener for From select change
            fromSelect.onchange = function() {
                const selectedFrom = this.value;
                updateToSelectOptions(toSelect, currentStops, selectedFrom);
            };
        }
        
        // Helper function to update select options
        function updateSelectOptions(select, stops) {
            const currentValue = select.value;
            select.innerHTML = '<option value="">Select location</option>';
            stops.forEach(stop => {
                const option = document.createElement('option');
                option.value = stop;
                option.textContent = stop;
                select.appendChild(option);
            });
            if (currentValue && stops.includes(currentValue)) {
                select.value = currentValue;
            }
        }
        
        // Helper function to update To select options (excluding selected From)
        function updateToSelectOptions(select, stops, excludeValue) {
            const currentValue = select.value;
            select.innerHTML = '<option value="">Select destination</option>';
            stops.forEach(stop => {
                const option = document.createElement('option');
                option.value = stop;
                option.textContent = stop;
                if (stop === excludeValue) {
                    option.disabled = true;
                    option.textContent = stop + ' (Selected as origin)';
                }
                select.appendChild(option);
            });
            if (currentValue && stops.includes(currentValue) && currentValue !== excludeValue) {
                select.value = currentValue;
            } else if (currentValue === excludeValue) {
                select.value = '';
            }
        }

        // Toggle expandable sub-routes
        function toggleSubroutes(button) {
            const card = button.closest('.bg-white');
            const subroutes = card.querySelector('.route-subroutes');
            const toggle = button.querySelector('.route-toggle');
            
            subroutes.classList.toggle('open');
            toggle.classList.toggle('open');
        }

        // Replace orange colors with cream color #e3dec9 at runtime
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize with default route (TAGUM TO PITX)
            selectRoute('tagum-to-pitx');
            const accentColor = '#e3dec9';
            const accentColorRGB = '227 222 201';
            
            // Create dynamic style tag for computed style replacements
            const styleTag = document.createElement('style');
            styleTag.id = 'dynamic-color-override';
            styleTag.textContent = `
                :root {
                    --my-accent: ${accentColor};
                    --my-accent-rgb: ${accentColorRGB};
                }
                
                /* Replace gradient colors */
                .from-orange-500, .from-orange-600 {
                    --tw-gradient-from: ${accentColor} !important;
                }
                
                .to-orange-500, .to-orange-600 {
                    --tw-gradient-to: ${accentColor} !important;
                }
                
                /* Text color replacements */
                .text-orange-100, .text-orange-300, .text-orange-500, .text-orange-600 {
                    color: ${accentColor} !important;
                }
                
                /* Background replacements */
                .bg-orange-100, .bg-orange-300, .bg-orange-500, .bg-orange-600 {
                    background-color: ${accentColor} !important;
                }
                
                /* Border replacements */
                .border-orange-300, .border-orange-500 {
                    border-color: ${accentColor} !important;
                }
            `;
            document.head.appendChild(styleTag);
            
            // Process any inline style attributes with orange
            document.querySelectorAll('[style*="orange"]').forEach(el => {
                el.style.color = el.style.color.includes('orange') ? accentColor : el.style.color;
                el.style.backgroundColor = el.style.backgroundColor.includes('orange') ? accentColor : el.style.backgroundColor;
                el.style.borderColor = el.style.borderColor.includes('orange') ? accentColor : el.style.borderColor;
            });
        });
    </script>

    <!-- Animations CSS -->
    <style>
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-animate {
            animation: modalSlideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .fade-animate {
            animation: fadeIn 0.3s ease-in;
        }

        .form-group-animate {
            animation: slideInUp 0.4s ease-out forwards;
        }

        .form-group-animate:nth-child(1) { animation-delay: 0.05s; }
        .form-group-animate:nth-child(2) { animation-delay: 0.1s; }
        .form-group-animate:nth-child(3) { animation-delay: 0.15s; }
        .form-group-animate:nth-child(4) { animation-delay: 0.2s; }

        .button-group-animate {
            animation: slideInUp 0.4s ease-out 0.25s forwards;
            opacity: 0;
        }

        input:hover, input:focus {
            transform: translateY(-2px);
        }

        button {
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        button:active {
            transform: translateY(0);
        }
    </style>

    <!-- Account Settings Modal -->
    <div id="accountSettingsModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 fade-animate" onclick="if(event.target === this) closeAccountSettingsModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-screen overflow-y-auto modal-animate">
            <div class="p-8">
                <!-- Header with animation -->
                <div class="flex justify-between items-center mb-8 form-group-animate">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-user-circle text-blue-600 mr-2"></i>Account Settings
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Manage your profile</p>
                    </div>
                    <button onclick="closeAccountSettingsModal()" class="text-gray-400 hover:text-gray-600 text-2xl transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Profile Form -->
                <form id="accountSettingsForm" method="POST" action="/DMS_BOOKING/account-settings" class="space-y-4">
                    <!-- Full Name -->
                    <div class="form-group-animate">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-blue-600 mr-1"></i>Full Name
                        </label>
                        <input type="text" name="name" required 
                               value="<?php echo htmlspecialchars($_SESSION['user']['name'] ?? ''); ?>"
                               placeholder="Enter your full name"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition hover:border-gray-300">
                    </div>

                    <!-- Email Address -->
                    <div class="form-group-animate">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-blue-600 mr-1"></i>Email Address
                        </label>
                        <input type="email" name="email" required 
                               value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>"
                               placeholder="Enter your email address"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition hover:border-gray-300">
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group-animate">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-phone text-blue-600 mr-1"></i>Mobile Number
                        </label>
                        <input type="tel" name="phone" 
                               value="<?php echo htmlspecialchars($_SESSION['user']['phone'] ?? ''); ?>"
                               placeholder="+63 (123) 456-7890"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition hover:border-gray-300">
                    </div>

                    <!-- Buttons Section -->
                    <div class="space-y-3 pt-6 border-t border-gray-200 button-group-animate">
                        <!-- Save Profile Button -->
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 shadow-md">
                            <i class="fas fa-save"></i>Save Changes
                        </button>

                        <!-- Change Password Button -->
                        <button type="button" onclick="openPasswordChangeModal()" class="w-full bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white px-4 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 shadow-md">
                            <i class="fas fa-key"></i>Change Password
                        </button>

                        <!-- Cancel Button -->
                        <button type="button" onclick="closeAccountSettingsModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-3 rounded-lg font-semibold flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Change Modal -->
    <div id="passwordChangeModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 fade-animate" onclick="if(event.target === this) closePasswordChangeModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-screen overflow-y-auto modal-animate">
            <div class="p-8">
                <!-- Header with animation -->
                <div class="flex justify-between items-center mb-8 form-group-animate">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-lock text-amber-600 mr-2"></i>Change Password
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Update your password securely</p>
                    </div>
                    <button onclick="closePasswordChangeModal()" class="text-gray-400 hover:text-gray-600 text-2xl transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Password Form -->
                <form id="passwordChangeForm" method="POST" action="/DMS_BOOKING/change-password" class="space-y-4">
                    <!-- Current Password -->
                    <div class="form-group-animate">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-amber-600 mr-1"></i>Current Password
                        </label>
                        <div class="relative">
                            <input type="password" name="current_password" id="currentPassword" required 
                                   placeholder="Enter your current password"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition hover:border-gray-300 pr-10">
                            <button type="button" onclick="togglePasswordVisibility('currentPassword')" class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="form-group-animate">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-key text-amber-600 mr-1"></i>New Password
                        </label>
                        <div class="relative">
                            <input type="password" name="new_password" id="newPassword" required 
                                   placeholder="Enter a new password"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition hover:border-gray-300 pr-10">
                            <button type="button" onclick="togglePasswordVisibility('newPassword')" class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>Minimum 8 characters required
                        </p>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group-animate">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-check-circle text-amber-600 mr-1"></i>Confirm Password
                        </label>
                        <div class="relative">
                            <input type="password" name="confirm_password" id="confirmPassword" required 
                                   placeholder="Re-enter your new password"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition hover:border-gray-300 pr-10">
                            <button type="button" onclick="togglePasswordVisibility('confirmPassword')" class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Warning Box -->
                    <div class="p-4 bg-amber-50 border-l-4 border-amber-400 rounded form-group-animate">
                        <p class="text-xs text-amber-800 flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle mt-0.5 flex-shrink-0"></i>
                            <span>Make sure both passwords match and are secure. This action cannot be undone.</span>
                        </p>
                    </div>

                    <!-- Buttons Section -->
                    <div class="space-y-3 pt-6 border-t border-gray-200 button-group-animate">
                        <!-- Change Password Button -->
                        <button type="submit" class="w-full bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white px-4 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 shadow-md">
                            <i class="fas fa-shield-alt"></i>Update Password
                        </button>

                        <!-- Cancel Button -->
                        <button type="button" onclick="closePasswordChangeModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-3 rounded-lg font-semibold flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAccountSettingsModal() {
            document.getElementById('accountSettingsModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAccountSettingsModal() {
            document.getElementById('accountSettingsModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openPasswordChangeModal() {
            const accountModal = document.getElementById('accountSettingsModal');
            const passwordModal = document.getElementById('passwordChangeModal');
            // Close account modal and open password modal
            accountModal.classList.add('hidden');
            passwordModal.classList.remove('hidden');
            // Clear password fields
            document.getElementById('passwordChangeForm').reset();
        }

        function closePasswordChangeModal() {
            const accountModal = document.getElementById('accountSettingsModal');
            const passwordModal = document.getElementById('passwordChangeModal');
            passwordModal.classList.add('hidden');
            accountModal.classList.remove('hidden');
            // Clear password fields
            document.getElementById('passwordChangeForm').reset();
        }

        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
        }

        // Helper function to show notifications in modal
        function showNotification(message, type = 'success') {
            const notifId = 'modalNotif_' + Date.now();
            const notifHTML = `
                <div id="${notifId}" class="p-4 rounded-lg mb-4 flex items-start gap-3 animate-pulse" style="animation: slideInUp 0.3s ease-out;">
                    ${type === 'success' ? 
                        '<i class="fas fa-check-circle text-green-600 mt-0.5 flex-shrink-0"></i><div class="text-sm text-green-700">' + message + '</div>' :
                        '<i class="fas fa-exclamation-circle text-red-600 mt-0.5 flex-shrink-0"></i><div class="text-sm text-red-700">' + message + '</div>'
                    }
                </div>
            `;
            
            const form = document.getElementById('accountSettingsForm') || document.getElementById('passwordChangeForm');
            if (form) {
                const container = form.parentElement;
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = notifHTML;
                container.insertBefore(tempDiv.firstElementChild, form);
                
                setTimeout(() => {
                    const elem = document.getElementById(notifId);
                    if (elem) elem.remove();
                }, 4000);
            }
        }

        // Handle Account Settings Form Submission with AJAX
        document.getElementById('accountSettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const saveBtn = this.querySelector('button[type="submit"]');
            const originalText = saveBtn.innerHTML;
            
            // Add loading state
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Saving...';
            
            fetch('/DMS_BOOKING/account-settings', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // If we got a redirect response, the server is redirecting us away
                // This shouldn't happen with our updated handler, but if it does,
                // reload to handle it properly
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
                return response.text();
            })
            .then(data => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
                
                // Check if response contains error
                if (data && data.includes('error')) {
                    showNotification('Failed to update profile', 'error');
                } else {
                    showNotification('Profile updated successfully!', 'success');
                    // Update modal with new data - refresh user data from server
                    setTimeout(() => {
                        location.reload(); // Reload to refresh all user data
                    }, 1500);
                }
            })
            .catch(error => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
                showNotification('Error updating profile: ' + error.message, 'error');
            });
        });

        // Handle Password Change Form Submission with AJAX
        document.getElementById('passwordChangeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword.length < 8) {
                showNotification('Password must be at least 8 characters long', 'error');
                return false;
            }
            
            if (newPassword !== confirmPassword) {
                showNotification('Passwords do not match. Please try again.', 'error');
                document.getElementById('newPassword').focus();
                return false;
            }
            
            // Additional confirmation
            if (!confirm('Are you sure you want to change your password? This action cannot be undone.')) {
                return false;
            }
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Add loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Updating...';
            
            fetch('/DMS_BOOKING/change-password', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
                return response.text();
            })
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                if (data && data.includes('error')) {
                    showNotification('Failed to change password', 'error');
                } else {
                    showNotification('Password changed successfully!', 'success');
                    document.getElementById('passwordChangeForm').reset();
                    setTimeout(() => {
                        closePasswordChangeModal();
                    }, 1500);
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                showNotification('Error changing password: ' + error.message, 'error');
            });
        });
    </script>

    <?php if (isset($_GET['login'])): ?>
    <script>document.addEventListener('DOMContentLoaded', function(){ openAuthModal(); });</script>
    <?php endif; ?>
    <?php if (isset($_GET['settings'])): ?>
    <script>document.addEventListener('DOMContentLoaded', function(){ openAccountSettingsModal(); });</script>
    <?php endif; ?>
</body>
</html>
