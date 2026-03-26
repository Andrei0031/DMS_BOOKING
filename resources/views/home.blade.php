<?php
// Landing page - pure PHP
$title = 'Davao Metro Shuttle - Book Your Bus Tickets Online';
ob_start();
?>

    <!-- Booking Search & Popular Routes Section -->
    <section class="bg-gradient-to-br from-white to-blue-50 py-12 md:py-20 mt-8 md:mt-16 relative z-10 px-4 sm:px-0">
        <div class="max-w-7xl mx-auto">
            <!-- Two Column Layout: Search on Left, Routes on Right -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10">
                <!-- LEFT: Search Card -->
                <div>
                    <div class="bg-white rounded-2xl shadow-2xl p-4 sm:p-6 md:p-10">
                        <!-- Card Header -->
                        <div class="mb-2">
                            <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">
                                <i class="fas fa-search text-orange-500 mr-3"></i>Find Your Bus
                            </h2>
                            <p class="text-lg md:text-xl font-semibold text-blue-600 mt-1 animated-greeting">
                                Maayong Pagsakay!
                            </p>
                        </div>

                        <div class="border-b-2 border-gray-100 mb-6 mt-4"></div>

                        <?php
                        // Build JS routes array for the smart dropdowns
                        $js_routes = [];
                        foreach ($popular_routes as $pr) {
                            $js_routes[] = [
                                'from'  => $pr['from_location'],
                                'to'    => $pr['to_location'],
                                'price' => !empty($pr['price_from']) ? number_format($pr['price_from'], 0) : null,
                            ];
                        }
                        ?>
                        <style>
                            .route-dd { animation: qrFadeIn .13s ease; }
                            .route-dd-item:hover  { background: #eff6ff; }
                            .route-dd-item.active { background: #dbeafe; }
                            @keyframes qrFadeIn {
                                from { opacity:0; transform:translateY(-5px) scale(.98); }
                                to   { opacity:1; transform:translateY(0) scale(1); }
                            }
                        </style>

                        <form action="/DMS_BOOKING/search" method="GET" class="grid grid-cols-1 gap-4" id="searchForm">
                            <!-- Hidden trip_type field (default one-way) -->
                            <input type="hidden" name="trip_type" value="one-way">
                            <!-- First Row: 1 column on mobile, 2 on tablet -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- From Location -->
                                <div class="relative">
                                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">From</label>
                                    <input type="text" id="field_from" name="from_location" required
                                        placeholder="e.g. Davao City" autocomplete="off"
                                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border-2 border-gray-300 rounded-lg focus:border-orange-400 focus:outline-none transition text-xs sm:text-sm" />
                                    <div id="dd_from" class="route-dd hidden absolute left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden max-h-52 overflow-y-auto"></div>
                                </div>

                                <!-- To Location -->
                                <div class="relative">
                                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">To</label>
                                    <input type="text" id="field_to" name="to_location" required
                                        placeholder="e.g. General Santos" autocomplete="off"
                                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border-2 border-gray-300 rounded-lg focus:border-orange-400 focus:outline-none transition text-xs sm:text-sm" />
                                    <div id="dd_to" class="route-dd hidden absolute left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden max-h-52 overflow-y-auto"></div>
                                </div>

                                <!-- Departure Date -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Depart</label>
                                    <div class="fp-input-wrap">
                                        <i class="fas fa-calendar-alt fp-icon"></i>
                                        <input type="text" id="journey_date_picker" name="journey_date" required readonly
                                            placeholder="Select date"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border-2 border-gray-300 rounded-lg transition text-xs sm:text-sm" />
                                    </div>
                                </div>

                                <!-- Passengers -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Passengers</label>
                                    <input type="number" name="passengers" min="1" max="50" value="1" required class="w-full px-3 sm:px-4 py-2 sm:py-3 border-2 border-gray-300 rounded-lg focus:border-orange-400 focus:outline-none transition text-xs sm:text-sm" />
                                </div>
                            </div>

                            <!-- Search Button on its own row -->
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-orange-500 hover:from-blue-700 hover:to-orange-600 text-white font-bold px-6 py-3 sm:py-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg text-sm sm:text-base">
                                <i class="fas fa-search mr-2"></i>Search Trip
                            </button>
                        </form>

                        <p class="text-center text-xs sm:text-sm text-gray-500 mt-4">
                            <i class="fas fa-info-circle mr-1"></i>Book your tickets and pay securely
                        </p>
                    </div>
                </div>

                <!-- RIGHT: Popular Routes Section -->
                <div>
                    <div class="flex justify-between items-center mb-6 sm:mb-8">
                        <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">
                            Popular Routes
                        </h3>
                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg text-xs md:text-sm transition">
                            View All Routes
                        </a>
                    </div>
                    
                    <div class="space-y-3">
                        <?php
                        $border_colors = ['border-blue-600','border-orange-500','border-indigo-500','border-emerald-500','border-rose-500'];
                        $i = 0;
                        foreach ($popular_routes as $route):
                            $border = $border_colors[$i % count($border_colors)];
                            $from_esc = htmlspecialchars(addslashes($route['from_location']));
                            $to_esc   = htmlspecialchars(addslashes($route['to_location']));
                            $i++;
                        ?>
                        <div class="bg-white rounded-lg shadow-md hover:shadow-xl hover:scale-105 transition-all duration-300 p-4 border-l-4 <?php echo $border; ?>">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <p class="text-gray-500 font-semibold text-xs">Popular Route</p>
                                    <h4 class="text-lg md:text-xl font-bold text-gray-900">
                                        <?php echo htmlspecialchars($route['from_location']); ?> ↔ <?php echo htmlspecialchars($route['to_location']); ?>
                                    </h4>
                                </div>
                                <button onclick="searchRoute('<?php echo $from_esc; ?>', '<?php echo $to_esc; ?>')"
                                    class="btn-book-now bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1 rounded text-xs whitespace-nowrap transition">
                                    Book Now!
                                </button>
                            </div>
                            <div class="space-y-1 text-sm">
                                <?php if (!empty($route['duration'])): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Travel Time:</span>
                                    <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($route['duration']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($route['price_from'])): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">From:</span>
                                    <span class="font-semibold text-blue-600">₱<?php echo number_format($route['price_from'], 2); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($popular_routes)): ?>
                        <div class="bg-white rounded-lg shadow-md p-6 text-center text-gray-400">
                            <i class="fas fa-route text-3xl mb-2 block text-gray-200"></i>
                            <p class="text-sm">No popular routes available yet.</p>
                        </div>
                        <?php endif; ?>
                    </div>
            </div>

            <!-- Quick Info Stats -->
            <div class="grid grid-cols-3 gap-2 sm:gap-4 md:gap-6 mt-8 sm:mt-12 md:mt-16">
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl md:text-4xl font-bold text-blue-600 mb-1 sm:mb-2">500+</div>
                    <p class="text-gray-600 text-xs sm:text-sm md:text-base">Buses Daily</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl md:text-4xl font-bold text-orange-500 mb-1 sm:mb-2">100+</div>
                    <p class="text-gray-600 text-xs sm:text-sm md:text-base">Routes</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl md:text-4xl font-bold text-blue-600 mb-1 sm:mb-2">24/7</div>
                    <p class="text-gray-600 text-xs sm:text-sm md:text-base">Support</p>
                </div>
            </div>

            <!-- Why Travel with Davao Metro Shuttle Section -->
            <div class="mt-12 sm:mt-16 md:mt-20 text-center">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-12 animated-question">
                    Why Travel with <span class="text-blue-600">Davao Metro Shuttle?</span>
                </h2>
            </div>
        </div>

        <!-- JavaScript for search functionality -->
        <script>
            const popularRoutes = <?php echo json_encode($js_routes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

            function buildRouteItem(route, onClickFn) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'route-dd-item w-full flex items-center gap-3 px-4 py-3 text-left transition cursor-pointer';
                btn.innerHTML = `
                    <span class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-route text-blue-600 text-xs"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-gray-800">${route.from}</p>
                        <p class="text-xs text-gray-400"><i class="fas fa-arrow-down text-orange-400 mr-1"></i>${route.to}</p>
                    </div>
                    ${route.price ? `<span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">₱${route.price}+</span>` : ''}
                `;
                btn.addEventListener('mousedown', (e) => { e.preventDefault(); onClickFn(route); });
                return btn;
            }

            function showDropdown(ddEl, routes, onClickFn) {
                ddEl.innerHTML = '';
                if (!routes.length) { ddEl.classList.add('hidden'); return; }
                const header = document.createElement('div');
                header.className = 'px-4 py-2 bg-gray-50 border-b border-gray-100';
                header.innerHTML = '<p class="text-xs font-bold text-gray-400 uppercase tracking-wide"><i class="fas fa-bolt text-orange-400 mr-1"></i>Popular Routes</p>';
                ddEl.appendChild(header);
                routes.forEach(r => ddEl.appendChild(buildRouteItem(r, onClickFn)));
                ddEl.classList.remove('hidden');
            }

            function hideAll() {
                document.getElementById('dd_from').classList.add('hidden');
                document.getElementById('dd_to').classList.add('hidden');
            }

            // --- From field ---
            const fieldFrom = document.getElementById('field_from');
            const ddFrom    = document.getElementById('dd_from');

            fieldFrom.addEventListener('focus', () => {
                const q = fieldFrom.value.trim().toLowerCase();
                const filtered = popularRoutes.filter(r =>
                    !q || r.from.toLowerCase().includes(q) || r.to.toLowerCase().includes(q)
                );
                showDropdown(ddFrom, filtered, (route) => {
                    fieldFrom.value = route.from;
                    document.getElementById('field_to').value = route.to;
                    hideAll();
                });
            });
            fieldFrom.addEventListener('input', () => {
                const q = fieldFrom.value.trim().toLowerCase();
                const filtered = popularRoutes.filter(r =>
                    r.from.toLowerCase().includes(q) || r.to.toLowerCase().includes(q)
                );
                showDropdown(ddFrom, filtered, (route) => {
                    fieldFrom.value = route.from;
                    document.getElementById('field_to').value = route.to;
                    hideAll();
                });
            });
            fieldFrom.addEventListener('blur', () => setTimeout(() => ddFrom.classList.add('hidden'), 150));

            // --- To field ---
            const fieldTo = document.getElementById('field_to');
            const ddTo    = document.getElementById('dd_to');

            fieldTo.addEventListener('focus', () => {
                const q = fieldTo.value.trim().toLowerCase();
                const fromVal = fieldFrom.value.trim().toLowerCase();
                const filtered = popularRoutes.filter(r =>
                    (!fromVal || r.from.toLowerCase().includes(fromVal)) &&
                    (!q || r.to.toLowerCase().includes(q))
                );
                showDropdown(ddTo, filtered.length ? filtered : popularRoutes, (route) => {
                    fieldFrom.value = route.from;
                    fieldTo.value   = route.to;
                    hideAll();
                });
            });
            fieldTo.addEventListener('input', () => {
                const q = fieldTo.value.trim().toLowerCase();
                const filtered = popularRoutes.filter(r => r.to.toLowerCase().includes(q));
                showDropdown(ddTo, filtered, (route) => {
                    fieldFrom.value = route.from;
                    fieldTo.value   = route.to;
                    hideAll();
                });
            });
            fieldTo.addEventListener('blur', () => setTimeout(() => ddTo.classList.add('hidden'), 150));

            // Close on outside click
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#searchForm')) hideAll();
            });

            /* ── Flatpickr date picker ── */
            flatpickr('#journey_date_picker', {
                minDate: 'today',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'F j, Y',
                disableMobile: true,
                showMonths: 1,
                locale: {
                    weekdays: {
                        shorthand: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        longhand:  ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']
                    },
                    firstDayOfWeek: 0
                }
            });

            /* ── Popular Route "Book Now" cards ── */
            function searchRoute(from, to) {
                if (!<?php echo isAuth() ? 'true' : 'false'; ?>) {
                    openAuthModal();
                    return;
                }
                const searchParams = new URLSearchParams({
                    from_location: from,
                    to_location: to,
                    journey_date: new Date().toISOString().split('T')[0]
                });
                window.location.href = '/DMS_BOOKING/search?' + searchParams.toString();
            }
        </script>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-8 sm:py-12 md:py-20 bg-white border-t-4 border-orange-500">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-white to-blue-50 p-4 sm:p-6 md:p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-l-4 border-orange-500">
                    <div class="text-3xl sm:text-4xl md:text-5xl mb-3 sm:mb-4 text-blue-600 inline-block p-2 sm:p-3 md:p-4 bg-blue-100 rounded-lg"><i class="fas fa-mobile-alt"></i></div>
                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold mb-2 sm:mb-3 text-gray-900">Easy Booking</h3>
                    <p class="text-gray-700 leading-relaxed text-xs sm:text-sm md:text-base">Book your tickets in just a few clicks with our simple and user-friendly interface.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-white to-blue-50 p-4 sm:p-6 md:p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-l-4 border-orange-500">
                    <div class="text-3xl sm:text-4xl md:text-5xl mb-3 sm:mb-4 text-blue-600 inline-block p-2 sm:p-3 md:p-4 bg-blue-100 rounded-lg"><i class="fas fa-lock"></i></div>
                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold mb-2 sm:mb-3 text-gray-900">Secure Payments</h3>
                    <p class="text-gray-700 leading-relaxed text-xs sm:text-sm md:text-base">Safe and secure payment gateway. Your data is protected with encryption.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-white to-blue-50 p-4 sm:p-6 md:p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-l-4 border-orange-500">
                    <div class="text-3xl sm:text-4xl md:text-5xl mb-3 sm:mb-4 text-blue-600 inline-block p-2 sm:p-3 md:p-4 bg-blue-100 rounded-lg"><i class="fas fa-wifi"></i></div>
                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold mb-2 sm:mb-3 text-gray-900">Always Connected</h3>
                    <p class="text-gray-700 leading-relaxed text-sm md:text-base">Access your bookings anytime, anywhere from any device instantly.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gradient-to-br from-white to-blue-50 p-4 sm:p-6 md:p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-l-4 border-orange-500">
                    <div class="text-3xl sm:text-4xl md:text-5xl mb-3 sm:mb-4 text-blue-600 inline-block p-2 sm:p-3 md:p-4 bg-blue-100 rounded-lg"><i class="fas fa-bell"></i></div>
                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold mb-2 sm:mb-3 text-gray-900">Live Updates</h3>
                    <p class="text-gray-700 leading-relaxed text-xs sm:text-sm md:text-base">Get instant notifications about your bookings and bus status in real-time.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gradient-to-br from-white to-blue-50 p-4 sm:p-6 md:p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-l-4 border-orange-500">
                    <div class="text-3xl sm:text-4xl md:text-5xl mb-3 sm:mb-4 text-blue-600 inline-block p-2 sm:p-3 md:p-4 bg-blue-100 rounded-lg"><i class="fas fa-tag"></i></div>
                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold mb-2 sm:mb-3 text-gray-900">Best Prices</h3>
                    <p class="text-gray-700 leading-relaxed text-xs sm:text-sm md:text-base">Competitive prices for comfortable bus travels across all routes daily.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gradient-to-br from-white to-blue-50 p-4 sm:p-6 md:p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-l-4 border-orange-500">
                    <div class="text-3xl sm:text-4xl md:text-5xl mb-3 sm:mb-4 text-blue-600 inline-block p-2 sm:p-3 md:p-4 bg-blue-100 rounded-lg"><i class="fas fa-headset"></i></div>
                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold mb-2 sm:mb-3 text-gray-900">24/7 Support</h3>
                    <p class="text-gray-600 leading-relaxed text-xs sm:text-sm md:text-base">Customer support available round the clock to help and assist you.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-8 sm:py-12 md:py-20 bg-white border-t-4 border-gray-200">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-2xl sm:text-3xl md:text-5xl font-bold mb-6 sm:mb-8 md:mb-12 text-center text-gray-900">About <span class="text-blue-600">Davao Metro Shuttle</span></h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 md:gap-16 items-center">
                <div>
                    <p class="text-sm sm:text-base md:text-lg text-gray-700 mb-3 sm:mb-4 md:mb-6 leading-relaxed font-semibold">Davao Metro Shuttle is your trusted partner for convenient and comfortable bus travel in Davao and nearby provinces.</p>
                    <p class="text-sm sm:text-base md:text-lg text-gray-700 mb-4 sm:mb-6 md:mb-8 leading-relaxed">With years of experience in the transportation industry, we are committed to providing you with reliable service and competitive prices.</p>
                    <ul class="space-y-2 sm:space-y-3 md:space-y-4">
                        <li class="flex items-center gap-2 sm:gap-3 text-gray-700 text-xs sm:text-sm md:text-lg">
                            <span class="flex-shrink-0 flex items-center justify-center h-6 sm:h-8 w-6 sm:w-8 rounded-full bg-blue-600 text-white font-bold text-xs">✓</span>
                            <span>Extensive network of routes across Davao</span>
                        </li>
                        <li class="flex items-center gap-2 sm:gap-3 text-gray-700 text-xs sm:text-sm md:text-lg">
                            <span class="flex-shrink-0 flex items-center justify-center h-6 sm:h-8 w-6 sm:w-8 rounded-full bg-blue-600 text-white font-bold text-xs">✓</span>
                            <span>Competitive and transparent pricing</span>
                        </li>
                        <li class="flex items-center gap-2 sm:gap-3 text-gray-700 text-xs sm:text-sm md:text-lg">
                            <span class="flex-shrink-0 flex items-center justify-center h-6 sm:h-8 w-6 sm:w-8 rounded-full bg-blue-600 text-white font-bold text-xs">✓</span>
                            <span>Flexible and easy cancellation policy</span>
                        </li>
                        <li class="flex items-center gap-2 sm:gap-3 text-gray-700 text-xs sm:text-sm md:text-lg">
                            <span class="flex-shrink-0 flex items-center justify-center h-6 sm:h-8 w-6 sm:w-8 rounded-full bg-blue-600 text-white font-bold text-xs">✓</span>
                            <span>Exclusive discounts and loyalty rewards</span>
                        </li>
                    </ul>
                </div>
                <div class="flex justify-center mt-6 sm:mt-8 lg:mt-0">
                    <div class="bg-gradient-to-br from-blue-50 to-white p-4 sm:p-8 md:p-12 rounded-2xl shadow-xl border-4 border-orange-300">
                        <i class="fas fa-shuttle-van text-4xl sm:text-6xl md:text-8xl lg:text-9xl text-orange-200 opacity-60"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- CTA Section -->
    <section id="contact" class="bg-white py-8 sm:py-12 md:py-20 border-t-4 border-gray-200">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <div class="bg-gradient-to-r from-blue-600 to-orange-500 text-white rounded-2xl p-6 sm:p-10 md:p-16 shadow-2xl">
                <h2 class="text-2xl sm:text-3xl md:text-5xl font-bold mb-3 sm:mb-4 md:mb-6">Ready to Book Your Journey?</h2>
                <p class="text-xs sm:text-base md:text-xl text-orange-100 mb-6 sm:mb-8 md:mb-10 font-light">Start your hassle-free bus booking experience with Davao Metro Shuttle today!</p>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 md:gap-4 justify-center">
                    <?php if (isAuth()): ?>
                        <a href="/DMS_BOOKING/dashboard" class="inline-block bg-white hover:bg-orange-100 text-blue-600 font-bold px-6 sm:px-8 md:px-12 py-2 sm:py-3 md:py-4 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg text-xs sm:text-sm md:text-base\">
                            <i class="fas fa-ticket-alt mr-2"></i>Book Your Ticket Now
                        </a>
                    <?php else: ?>
                        <button onclick="openAuthModal()" class="inline-block bg-white hover:bg-orange-100 text-blue-600 font-bold px-6 sm:px-8 md:px-12 py-2 sm:py-3 md:py-4 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg text-xs sm:text-sm md:text-base\">
                            <i class="fas fa-user-plus mr-2"></i>Create Account Now
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php 
$content = ob_get_clean();
include __DIR__ . '/layouts/app.blade.php';
?>
