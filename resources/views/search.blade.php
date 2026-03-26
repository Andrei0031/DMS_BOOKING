<?php
// Search results page
$title = 'Search Results - Davao Metro Shuttle';
ob_start();

// Require login — redirect to home with modal auto-open
if (!isAuth()) {
    ob_end_clean();
    redirect('/?login=1');
    exit;
}

$conn = $GLOBALS['db'];

// Get search parameters
$from_location = $_GET['from_location'] ?? '';
$to_location   = $_GET['to_location'] ?? '';
$journey_date  = $_GET['journey_date'] ?? date('Y-m-d');
$passengers    = intval($_GET['passengers'] ?? 1);
$trip_type     = $_GET['trip_type'] ?? 'one-way';

// Validate inputs
if (empty($from_location) || empty($to_location)) {
    echo '<div class="max-w-7xl mx-auto px-4 py-12"><div class="bg-red-50 border-l-4 border-red-600 text-red-700 px-6 py-4 rounded-lg"><strong>Error:</strong> Please select valid origin and destination.</div></div>';
    $content = ob_get_clean();
    include __DIR__ . '/layouts/app.blade.php';
    exit;
}

// Search buses
$query = "SELECT * FROM buses WHERE from_location = ? AND to_location = ? AND journey_date = ? AND available_seats >= ? ORDER BY journey_time ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param('sssi', $from_location, $to_location, $journey_date, $passengers);
$stmt->execute();
$result = $stmt->get_result();
$buses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate reverse route for round-trip if needed
$return_buses = [];
if ($trip_type === 'round-trip') {
    $query2 = "SELECT * FROM buses WHERE from_location = ? AND to_location = ? AND journey_date > ? AND available_seats >= ? ORDER BY journey_date ASC, journey_time ASC LIMIT 10";
    $return_date = date('Y-m-d', strtotime($journey_date . ' + 3 days'));
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param('sssi', $to_location, $from_location, $journey_date, $passengers);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $return_buses = $result2->fetch_all(MYSQLI_ASSOC);
    $stmt2->close();
}
?>

    <!-- Search Results Header -->
    <style>
        @keyframes qrFadeIn {
            from { opacity: 0; transform: translateY(-6px) scale(.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
    <section class="bg-gradient-to-r from-blue-600 to-orange-500 text-white py-6 sm:py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-3 sm:mb-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    <a href="/DMS_BOOKING/" class="text-white hover:text-orange-100 transition text-sm sm:text-base"><i class="fas fa-arrow-left mr-2"></i>Back</a>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold">Search Results</h1>
                </div>

                <?php if (!empty($popular_routes)): ?>
                <!-- Quick Route Selector -->
                <div class="relative" id="srQuickRouteWrapper">
                    <button type="button" id="srQuickRouteBtn" onclick="toggleSrQuickRoutes()"
                        class="flex items-center gap-1.5 bg-white/20 hover:bg-white/30 active:scale-95 backdrop-blur text-white text-xs sm:text-sm font-semibold px-3 py-2 rounded-lg shadow transition-all duration-150 border border-white/30 whitespace-nowrap">
                        <i class="fas fa-bolt"></i>
                        <span>Quick Route</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="srQuickRouteChevron"></i>
                    </button>
                    <div id="srQuickRoutePanel"
                        class="hidden absolute right-0 top-full mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden"
                        style="animation: qrFadeIn .15s ease;">
                        <div class="px-3 py-2 bg-gray-50 border-b border-gray-100">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Select a popular route</p>
                        </div>
                        <?php foreach ($popular_routes as $pr):
                            $pr_from = htmlspecialchars($pr['from_location'], ENT_QUOTES);
                            $pr_to   = htmlspecialchars($pr['to_location'], ENT_QUOTES);
                        ?>
                        <button type="button"
                            onclick="applySrQuickRoute('<?php echo $pr_from; ?>', '<?php echo $pr_to; ?>')"
                            class="w-full flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition text-left group">
                            <span class="flex-shrink-0 w-7 h-7 bg-blue-100 group-hover:bg-blue-200 rounded-full flex items-center justify-center transition">
                                <i class="fas fa-route text-blue-600 text-xs"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate"><?php echo $pr_from; ?></p>
                                <p class="text-xs text-gray-400 flex items-center gap-1">
                                    <i class="fas fa-arrow-down text-orange-400"></i><?php echo $pr_to; ?>
                                </p>
                            </div>
                            <?php if (!empty($pr['price_from'])): ?>
                            <span class="ml-auto text-xs font-bold text-blue-600 whitespace-nowrap">₱<?php echo number_format($pr['price_from'], 0); ?>+</span>
                            <?php endif; ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <p class="text-xs sm:text-sm md:text-base text-orange-100">
                <strong><?php echo htmlspecialchars($from_location); ?></strong> 
                <i class="fas fa-arrow-right mx-2"></i>
                <strong><?php echo htmlspecialchars($to_location); ?></strong> 
                on <?php echo date('M d, Y', strtotime($journey_date)); ?>
                for <?php echo $passengers; ?> passenger<?php echo $passengers !== 1 ? 's' : ''; ?>
            </p>
        </div>
    </section>

    <script>
        function toggleSrQuickRoutes() {
            const panel   = document.getElementById('srQuickRoutePanel');
            const chevron = document.getElementById('srQuickRouteChevron');
            const isHidden = panel.classList.contains('hidden');
            panel.classList.toggle('hidden', !isHidden);
            chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
        }

        function applySrQuickRoute(from, to) {
            const params = new URLSearchParams(window.location.search);
            params.set('from_location', from);
            params.set('to_location', to);
            window.location.href = '/DMS_BOOKING/search?' + params.toString();
        }

        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('srQuickRouteWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('srQuickRoutePanel').classList.add('hidden');
                document.getElementById('srQuickRouteChevron').style.transform = '';
            }
        });
    </script>

    <div class="max-w-7xl mx-auto px-4 py-8 sm:py-12 md:py-16">
        <?php if (empty($buses)): ?>
            <div class="bg-blue-50 border-l-4 border-blue-600 text-blue-700 px-4 sm:px-6 py-3 sm:py-4 rounded-lg mb-8">
                <p class="font-semibold text-base sm:text-lg mb-2"><i class="fas fa-info-circle mr-2"></i>No buses available</p>
                <p class="text-xs sm:text-sm mb-3 sm:mb-4">There are no buses available for your selected route and date. Try selecting different dates or routes.</p>
                <a href="/" class="text-blue-600 hover:text-blue-800 font-bold text-xs sm:text-sm">← Search Again</a>
            </div>
        <?php else: ?>
            <!-- Outbound Buses -->
            <div>
                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold mb-4 sm:mb-6 text-gray-900">
                    <i class="fas fa-bus text-blue-600 mr-3"></i>Outbound: <?php echo htmlspecialchars($from_location); ?> → <?php echo htmlspecialchars($to_location); ?>
                </h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4 md:gap-6 mb-8 sm:mb-12">
                    <?php foreach ($buses as $bus): ?>
                        <?php 
                            $total_price = $bus['price_per_seat'] * $passengers;
                            $departure_time = DateTime::createFromFormat('H:i:s', $bus['journey_time'])->format('h:i A');
                        ?>
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border-l-4 border-blue-600 p-4 sm:p-6">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">
                                <!-- Time & Route -->
                                <div class="col-span-1">
                                    <div class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo $departure_time; ?></div>
                                    <p class="text-gray-600 text-xs sm:text-sm mt-1">
                                        <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                                        <?php echo htmlspecialchars($bus['bus_type']); ?> Bus
                                    </p>
                                </div>
                                <!-- Availability -->
                                <div class="col-span-1 sm:col-span-2 text-right">
                                    <div class="text-xs sm:text-sm font-semibold text-white bg-gradient-to-r from-green-500 to-green-600 px-2 sm:px-3 py-1 sm:py-2 rounded-lg inline-block">
                                        <i class="fas fa-check mr-1"></i><?php echo $bus['available_seats']; ?> Available
                                    </div>
                                </div>
                            </div>

                            <!-- Bus Details -->
                            <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6 pb-4 sm:pb-6 border-b border-gray-200">
                                <div>
                                    <p class="text-gray-600 text-xs font-semibold uppercase">Bus Number</p>
                                    <p class="text-base sm:text-lg font-bold text-gray-900"><?php echo htmlspecialchars($bus['bus_number']); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-xs font-semibold uppercase">Total Seats</p>
                                    <p class="text-base sm:text-lg font-bold text-gray-900"><?php echo $bus['total_seats']; ?> Seats</p>
                                </div>
                            </div>

                            <!-- Price & Options -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                                <div>
                                    <p class="text-gray-600 text-xs sm:text-sm">Price per seat</p>
                                    <p class="text-xl sm:text-2xl font-bold text-blue-600">₱<?php echo number_format($total_price, 2); ?></p>
                                    <p class="text-xs text-gray-500 mt-1"><?php echo $passengers; ?> × ₱<?php echo number_format($bus['price_per_seat'], 2); ?></p>
                                </div>
                                <form action="/DMS_BOOKING/booking/create" method="POST" class="w-full sm:w-auto">
                                    <input type="hidden" name="bus_id" value="<?php echo $bus['id']; ?>" />\n                                    <input type="hidden" name="passengers" value="<?php echo $passengers; ?>" />\n                                    <input type="hidden" name="total_price" value="<?php echo $total_price; ?>" />
                                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-orange-500 hover:from-blue-700 hover:to-orange-600 text-white font-bold px-4 sm:px-6 py-2 sm:py-3 rounded-lg transition-all duration-300 text-xs sm:text-sm md:text-base">
                                        <i class="fas fa-ticket-alt mr-2"></i>Select
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Return Buses (if round-trip) -->
            <?php if ($trip_type === 'round-trip' && !empty($return_buses)): ?>
                <div class="mt-8 sm:mt-12 md:mt-16 pt-8 sm:pt-12 border-t-4 border-gray-200">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold mb-4 sm:mb-6 text-gray-900">
                        <i class="fas fa-bus text-orange-500 mr-3"></i>Return: <?php echo htmlspecialchars($to_location); ?> → <?php echo htmlspecialchars($from_location); ?>
                    </h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4 md:gap-6">
                        <?php foreach ($return_buses as $bus): ?>
                            <?php 
                                $total_price = $bus['price_per_seat'] * $passengers;
                                $departure_time = DateTime::createFromFormat('H:i:s', $bus['journey_time'])->format('h:i A');
                            ?>
                            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border-l-4 border-orange-500 p-4 sm:p-6">
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">
                                    <div class="col-span-1">
                                        <div class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo $departure_time; ?></div>
                                        <p class="text-gray-600 text-xs sm:text-sm mt-1">
                                            <i class="fas fa-calendar text-blue-600 mr-1"></i>
                                            <?php echo date('M d, Y', strtotime($bus['journey_date'])); ?>
                                        </p>
                                    </div>
                                    <div class="col-span-1 sm:col-span-2 text-right">
                                        <div class="text-xs sm:text-sm font-semibold text-white bg-gradient-to-r from-green-500 to-green-600 px-2 sm:px-3 py-1 sm:py-2 rounded-lg inline-block">
                                            <i class="fas fa-check mr-1"></i><?php echo $bus['available_seats']; ?> Available
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6 pb-4 sm:pb-6 border-b border-gray-200">
                                    <div>
                                        <p class="text-gray-600 text-xs font-semibold uppercase">Bus Number</p>
                                        <p class="text-base sm:text-lg font-bold text-gray-900"><?php echo htmlspecialchars($bus['bus_number']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 text-xs font-semibold uppercase">Total Seats</p>
                                        <p class="text-base sm:text-lg font-bold text-gray-900"><?php echo $bus['total_seats']; ?> Seats</p>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                                    <div>
                                        <p class="text-gray-600 text-xs sm:text-sm">Price per seat</p>
                                        <p class="text-xl sm:text-2xl font-bold text-orange-600">₱<?php echo number_format($total_price, 2); ?></p>
                                        <p class="text-xs text-gray-500 mt-1"><?php echo $passengers; ?> × ₱<?php echo number_format($bus['price_per_seat'], 2); ?></p>
                                    </div>
                                    <form action="/DMS_BOOKING/booking/create" method="POST" class="w-full sm:w-auto">
                                        <input type="hidden" name="bus_id" value="<?php echo $bus['id']; ?>" />
                                        <input type="hidden" name="passengers" value="<?php echo $passengers; ?>" />
                                        <input type="hidden" name="total_price" value="<?php echo $total_price; ?>" />
                                        <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold px-4 sm:px-6 py-2 sm:py-3 rounded-lg transition-all duration-300 text-xs sm:text-sm md:text-base">
                                            <i class="fas fa-ticket-alt mr-2"></i>Select
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layouts/app.blade.php';
?>
