<?php

/**
 * BusBook - Laravel-style Bus Booking System
 * Entry Point for XAMPP Environment
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Base paths
define('BASE_PATH', __DIR__ . '/..');
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

// Initialize database tables if needed (BEFORE loading helpers)
require_once BASE_PATH . '/database/init.php';

// Load helpers and bootstrap
require_once BASE_PATH . '/routes/helpers.php';

// Database connection
$conn = $GLOBALS['db'];

// Simple Router
class Router
{
    private $routes = [];
    private $conn;
    private $user;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->user = $_SESSION['user'] ?? null;
    }

    public function get($path, $handler)
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler)
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function delete($path, $handler)
    {
        $this->routes['DELETE'][$path] = $handler;
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base = '/DMS_BOOKING';
        
        // Remove base path
        if (strpos($path, $base) === 0) {
            $path = substr($path, strlen($base));
        }
        if (empty($path)) $path = '/';

        // Check for route
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = $this->convertRouteToPattern($route);
            if (preg_match($pattern, $path, $matches)) {
                return $this->executeHandler($handler, array_slice($matches, 1));
            }
        }

        return $this->notFound();
    }

    private function convertRouteToPattern($route)
    {
        $pattern = preg_replace('/{([a-zA-Z_][a-zA-Z0-9_]*)}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }

    private function executeHandler($handler, $params)
    {
        if (is_string($handler)) {
            list($controller, $method) = explode('@', $handler);
            $class = 'App\\Http\\Controllers\\' . $controller;
            $instance = new $class();
            $instance->setConnection($this->conn);
            $instance->setUser($this->user);
            return call_user_func_array([$instance, $method], $params);
        }
        return call_user_func_array($handler, $params);
    }

    private function notFound()
    {
        header("HTTP/1.0 404 Not Found");
        return view('errors.404');
    }
}

// Define routes
$router = new Router($conn);

// Home and Auth routes
$router->get('/', function() {
    global $conn;
    $user = $_SESSION['user'] ?? null;
    $popular_routes = $conn->query("SELECT * FROM popular_routes WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")->fetch_all(MYSQLI_ASSOC);
    return view('home', ['user' => $user, 'popular_routes' => $popular_routes]);
});

// Search buses route
$router->get('/search', function() {
    global $conn;
    $popular_routes = $conn->query("SELECT * FROM popular_routes WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")->fetch_all(MYSQLI_ASSOC);
    return view('search', compact('popular_routes'));
});

// Customer registration
$router->post('/register', function() {
    global $conn;
    $name  = $conn->real_escape_string(trim($_POST['name'] ?? ''));
    $email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirmation'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'All fields are required.';
        redirect('/');
    }
    if ($password !== $confirm) {
        $_SESSION['error'] = 'Passwords do not match.';
        redirect('/');
    }
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters.';
        redirect('/');
    }
    $check = $conn->query("SELECT id FROM customers WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $_SESSION['error'] = 'An account with this email already exists.';
        redirect('/');
    }
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $conn->query("INSERT INTO customers (name, email, password) VALUES ('$name', '$email', '$hashed')");
    $customer = $conn->query("SELECT * FROM customers WHERE email = '$email'")->fetch_assoc();
    $_SESSION['user'] = array_merge($customer, ['type' => 'customer']);
    $_SESSION['success'] = 'Account created! Welcome, ' . htmlspecialchars($name) . '!';
    redirect('/dashboard');
});

// Book a seat from search results
$router->post('/booking/create', function() {
    if (!isAuth()) {
        redirect('/?login=1');
    }
    global $conn;
    $user_id    = intval($_SESSION['user']['id']);
    $bus_id     = intval($_POST['bus_id'] ?? 0);
    $passengers = intval($_POST['passengers'] ?? 1);
    $total_price = floatval($_POST['total_price'] ?? 0);

    if (!$bus_id) { $_SESSION['error'] = 'Invalid bus selection.'; redirect('/'); }

    $bus = $conn->query("SELECT * FROM buses WHERE id = $bus_id")->fetch_assoc();
    if (!$bus) { $_SESSION['error'] = 'Bus not found.'; redirect('/'); }
    if ($bus['available_seats'] < $passengers) {
        $_SESSION['error'] = 'Not enough seats available.';
        redirect('/?back=search');
    }

    $from     = $conn->real_escape_string($bus['from_location']);
    $to       = $conn->real_escape_string($bus['to_location']);
    $date     = $conn->real_escape_string($bus['journey_date']);
    $bus_type = $conn->real_escape_string($bus['bus_type'] ?? 'standard');

    $conn->query("INSERT INTO bookings (user_id, bus_id, from_location, to_location, journey_date, number_of_seats, bus_type, total_price, status)
                 VALUES ($user_id, $bus_id, '$from', '$to', '$date', $passengers, '$bus_type', $total_price, 'pending')");
    $conn->query("UPDATE buses SET available_seats = available_seats - $passengers WHERE id = $bus_id");

    $_SESSION['success'] = 'Booking created successfully!';
    redirect('/dashboard');
});

// =====================
// CUSTOMER LOGIN (for regular users after registration)
// =====================

$router->get('/customer/login', function() {
    if (isset($_SESSION['user'])) {
        if (($_SESSION['user']['type'] ?? '') === 'admin') {
            redirect('/admin');
        }
        redirect('/dashboard');
    }
    redirect('/?login=1');
});

$router->post('/customer/login', function() {
    global $conn;

    $email    = $conn->real_escape_string($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email and password required';
        redirect('/');
    }

    $result = $conn->query("SELECT * FROM customers WHERE email = '$email'");
    if ($result->num_rows === 1) {
        $customer = $result->fetch_assoc();
        if (password_verify($password, $customer['password'])) {
            $_SESSION['user'] = array_merge($customer, ['type' => 'customer']);
            $_SESSION['success'] = 'Logged in successfully!';
            redirect('/dashboard');
        }
    }

    $_SESSION['error'] = 'Invalid email or password.';
    redirect('/');
});

// =====================
// ADMIN LOGIN (for administrators only)
// =====================

$router->get('/login', function() {
    // Only auto-redirect if already authenticated as admin
    if (isset($_SESSION['user']) && ($_SESSION['user']['type'] ?? '') === 'admin') {
        redirect('/admin');
    }
    return view('admin.login');
});

$router->post('/login', function() {
    global $conn;
    
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username and password required';
        redirect('/login');
    }

    $result = $conn->query("SELECT * FROM admins WHERE name = '$username' OR email = '$username'");
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = array_merge($user, ['type' => 'admin']);
            $_SESSION['success'] = 'Admin logged in successfully!';
            redirect('/admin');
        }
    }

    $_SESSION['error'] = 'Invalid admin credentials';
    redirect('/login');
});

$router->post('/logout', function() {
    session_destroy();
    session_start();
    $_SESSION['success'] = 'Logged out successfully!';
    redirect('/');
});

// Dashboard routes (require auth)
$router->get('/dashboard', function() {
    if (!isset($_SESSION['user'])) redirect('/?login=1');
    
    global $conn;
    $user_id = $_SESSION['user']['id'];
    $result = $conn->query("SELECT * FROM bookings WHERE user_id = $user_id ORDER BY created_at DESC");
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
    
    return view('dashboard.bookings', ['bookings' => $bookings, 'user' => $_SESSION['user']]);
});

$router->get('/dashboard/book', function() {
    if (!isset($_SESSION['user'])) redirect('/?login=1');
    return view('dashboard.create-booking', ['user' => $_SESSION['user']]);
});

$router->post('/dashboard/book', function() {
    if (!isset($_SESSION['user'])) redirect('/?login=1');
    
    global $conn;
    $user_id = $_SESSION['user']['id'];
    $from = $conn->real_escape_string($_POST['from_location'] ?? '');
    $to = $conn->real_escape_string($_POST['to_location'] ?? '');
    $date = $_POST['journey_date'] ?? '';
    $seats = intval($_POST['number_of_seats'] ?? 1);
    $bus_type = $conn->real_escape_string($_POST['bus_type'] ?? 'standard');

    if (empty($from) || empty($to) || empty($date)) {
        $_SESSION['error'] = 'All fields required';
        redirect('/dashboard/book');
    }

    $prices = ['standard' => 50, 'ac' => 75, 'sleeper' => 100];
    $price = ($prices[$bus_type] ?? 50) * $seats;

    $conn->query("INSERT INTO bookings (user_id, from_location, to_location, journey_date, number_of_seats, bus_type, total_price, status) 
                 VALUES ($user_id, '$from', '$to', '$date', $seats, '$bus_type', $price, 'pending')");

    $_SESSION['success'] = 'Booking created successfully!';
    redirect('/dashboard');
});

$router->post('/dashboard/bookings/{id}/cancel', function($id) {
    if (!isset($_SESSION['user'])) redirect('/?login=1');
    global $conn;
    $id = intval($id);
    $user_id = intval($_SESSION['user']['id']);
    $result = $conn->query("SELECT * FROM bookings WHERE id = $id AND user_id = $user_id");
    if ($result->num_rows === 0) {
        $_SESSION['error'] = 'Booking not found.';
        redirect('/dashboard');
    }
    $conn->query("UPDATE bookings SET status = 'cancelled' WHERE id = $id AND user_id = $user_id");
    $_SESSION['success'] = 'Booking cancelled.';
    redirect('/dashboard');
});

// =====================
// ADMIN ROUTES
// =====================

$requireAdmin = function() {
    if (!isset($_SESSION['user'])) redirect('/login');
    if (($_SESSION['user']['type'] ?? '') !== 'admin') {
        $_SESSION['error'] = 'Access denied. Admins only.';
        redirect('/');
    }
};

// Admin Dashboard
$router->get('/admin', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $total_bookings = $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'];
    $pending = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='pending'")->fetch_assoc()['c'];
    $confirmed = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='confirmed'")->fetch_assoc()['c'];
    $cancelled = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='cancelled'")->fetch_assoc()['c'];
    $total_users = $conn->query("SELECT COUNT(*) as c FROM customers")->fetch_assoc()['c'];
    $total_buses = $conn->query("SELECT COUNT(*) as c FROM buses")->fetch_assoc()['c'];
    $revenue_row = $conn->query("SELECT SUM(total_price) as r FROM bookings WHERE status='confirmed'")->fetch_assoc();
    $revenue = $revenue_row['r'] ?? 0;
    $recent_bookings = $conn->query("SELECT b.*, c.name as user_name FROM bookings b JOIN customers c ON b.user_id = c.id ORDER BY b.created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
    return view('admin.dashboard', compact('total_bookings','pending','confirmed','cancelled','total_users','total_buses','revenue','recent_bookings'));
});

// Admin Bookings
$router->get('/admin/bookings', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $where = [];
    if ($status_filter) $where[] = "b.status = '$status_filter'";
    if ($search) $where[] = "(u.name LIKE '%$search%' OR b.from_location LIKE '%$search%' OR b.to_location LIKE '%$search%')";
    $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $bookings = $conn->query("SELECT b.*, c.name as user_name, c.email as user_email FROM bookings b JOIN customers c ON b.user_id = c.id $where_sql ORDER BY b.created_at DESC")->fetch_all(MYSQLI_ASSOC);
    return view('admin.bookings.index', compact('bookings','status_filter','search'));
});

$router->post('/admin/bookings/{id}/status', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $allowed = ['pending','confirmed','cancelled'];
    $status = $_POST['status'] ?? '';
    if (!in_array($status, $allowed)) {
        $_SESSION['error'] = 'Invalid status.';
        redirect('/admin/bookings');
    }
    $status = $conn->real_escape_string($status);
    $conn->query("UPDATE bookings SET status = '$status' WHERE id = $id");
    $_SESSION['success'] = 'Booking status updated.';
    redirect('/admin/bookings');
});

$router->post('/admin/bookings/{id}/delete', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $conn->query("DELETE FROM bookings WHERE id = $id");
    $_SESSION['success'] = 'Booking deleted.';
    redirect('/admin/bookings');
});

// Admin Buses
$router->get('/admin/buses', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $buses = $conn->query("SELECT * FROM buses ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
    return view('admin.buses.index', compact('buses'));
});

$router->get('/admin/buses/create', function() use ($requireAdmin) {
    $requireAdmin();
    return view('admin.buses.create', []);
});

$router->post('/admin/buses', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $bus_number  = $conn->real_escape_string($_POST['bus_number'] ?? '');
    $from        = $conn->real_escape_string($_POST['from_location'] ?? '');
    $to          = $conn->real_escape_string($_POST['to_location'] ?? '');
    $time        = $conn->real_escape_string($_POST['journey_time'] ?? '');
    $date        = $conn->real_escape_string($_POST['journey_date'] ?? '');
    $total       = intval($_POST['total_seats'] ?? 0);
    $available   = intval($_POST['available_seats'] ?? 0);
    $price       = floatval($_POST['price_per_seat'] ?? 0);
    $bus_type    = $conn->real_escape_string($_POST['bus_type'] ?? 'standard');
    if (empty($bus_number) || empty($from) || empty($to) || empty($date) || $total <= 0) {
        $_SESSION['error'] = 'All required fields must be filled.';
        redirect('/admin/buses/create');
    }
    $conn->query("INSERT INTO buses (bus_number, from_location, to_location, journey_time, journey_date, total_seats, available_seats, price_per_seat, bus_type) VALUES ('$bus_number','$from','$to','$time','$date',$total,$available,$price,'$bus_type')");
    $_SESSION['success'] = 'Bus added successfully.';
    redirect('/admin/buses');
});

$router->get('/admin/buses/{id}/edit', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $result = $conn->query("SELECT * FROM buses WHERE id = $id");
    if ($result->num_rows === 0) { $_SESSION['error'] = 'Bus not found.'; redirect('/admin/buses'); }
    $bus = $result->fetch_assoc();
    return view('admin.buses.edit', compact('bus'));
});

$router->post('/admin/buses/{id}/update', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id          = intval($id);
    $bus_number  = $conn->real_escape_string($_POST['bus_number'] ?? '');
    $from        = $conn->real_escape_string($_POST['from_location'] ?? '');
    $to          = $conn->real_escape_string($_POST['to_location'] ?? '');
    $time        = $conn->real_escape_string($_POST['journey_time'] ?? '');
    $date        = $conn->real_escape_string($_POST['journey_date'] ?? '');
    $total       = intval($_POST['total_seats'] ?? 0);
    $available   = intval($_POST['available_seats'] ?? 0);
    $price       = floatval($_POST['price_per_seat'] ?? 0);
    $bus_type    = $conn->real_escape_string($_POST['bus_type'] ?? 'standard');
    $conn->query("UPDATE buses SET bus_number='$bus_number', from_location='$from', to_location='$to', journey_time='$time', journey_date='$date', total_seats=$total, available_seats=$available, price_per_seat=$price, bus_type='$bus_type' WHERE id=$id");
    $_SESSION['success'] = 'Bus updated successfully.';
    redirect('/admin/buses');
});

$router->post('/admin/buses/{id}/delete', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $conn->query("DELETE FROM buses WHERE id = $id");
    $_SESSION['success'] = 'Bus deleted.';
    redirect('/admin/buses');
});

// Admin Users
$router->get('/admin/users', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $where = $search ? "WHERE name LIKE '%$search%' OR email LIKE '%$search%'" : '';
    $users = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM bookings WHERE user_id = c.id) as booking_count FROM customers c $where ORDER BY c.created_at DESC")->fetch_all(MYSQLI_ASSOC);
    return view('admin.users.index', compact('users','search'));
});

$router->post('/admin/users/{id}/delete', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $conn->query("DELETE FROM customers WHERE id = $id");
    $_SESSION['success'] = 'Customer deleted.';
    redirect('/admin/users');
});

// Admin Popular Routes
$router->get('/admin/routes', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $routes = $conn->query("SELECT * FROM popular_routes ORDER BY sort_order ASC, id ASC")->fetch_all(MYSQLI_ASSOC);
    // Normalize sort_order to be sequential (1,2,3…) so up/down logic is reliable
    foreach ($routes as $i => $r) {
        $new_order = $i + 1;
        if (intval($r['sort_order']) !== $new_order) {
            $rid = intval($r['id']);
            $conn->query("UPDATE popular_routes SET sort_order = $new_order WHERE id = $rid");
            $routes[$i]['sort_order'] = $new_order;
        }
    }
    return view('admin.routes.index', compact('routes'));
});

$router->post('/admin/routes', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $from     = $conn->real_escape_string(trim($_POST['from_location'] ?? ''));
    $to       = $conn->real_escape_string(trim($_POST['to_location'] ?? ''));
    $duration = $conn->real_escape_string(trim($_POST['duration'] ?? ''));
    $price    = floatval($_POST['price_from'] ?? 0);
    if (empty($from) || empty($to)) {
        $_SESSION['error'] = 'From and To locations are required.';
        redirect('/admin/routes');
    }
    $max = $conn->query("SELECT COALESCE(MAX(sort_order),0) as m FROM popular_routes")->fetch_assoc()['m'];
    $order = intval($max) + 1;
    $conn->query("INSERT INTO popular_routes (from_location, to_location, duration, price_from, sort_order) VALUES ('$from','$to','$duration',$price,$order)");
    $_SESSION['success'] = 'Route added successfully.';
    redirect('/admin/routes');
});

$router->post('/admin/routes/{id}/delete', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $conn->query("DELETE FROM popular_routes WHERE id = $id");
    $_SESSION['success'] = 'Route deleted.';
    redirect('/admin/routes');
});

$router->post('/admin/routes/{id}/toggle', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $conn->query("UPDATE popular_routes SET is_active = 1 - is_active WHERE id = $id");
    redirect('/admin/routes');
});

$router->get('/admin/routes/{id}/edit', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $result = $conn->query("SELECT * FROM popular_routes WHERE id = $id");
    if ($result->num_rows === 0) { $_SESSION['error'] = 'Route not found.'; redirect('/admin/routes'); }
    $route = $result->fetch_assoc();
    return view('admin.routes.edit', compact('route'));
});

$router->post('/admin/routes/{id}/update', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id       = intval($id);
    $from     = $conn->real_escape_string(trim($_POST['from_location'] ?? ''));
    $to       = $conn->real_escape_string(trim($_POST['to_location'] ?? ''));
    $duration = $conn->real_escape_string(trim($_POST['duration'] ?? ''));
    $price    = floatval($_POST['price_from'] ?? 0);
    if (empty($from) || empty($to)) {
        $_SESSION['error'] = 'From and To locations are required.';
        redirect('/admin/routes');
    }
    $conn->query("UPDATE popular_routes SET from_location='$from', to_location='$to', duration='$duration', price_from=$price WHERE id=$id");
    $_SESSION['success'] = 'Route updated successfully.';
    redirect('/admin/routes');
});

$router->post('/admin/routes/{id}/move-up', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id  = intval($id);
    $row = $conn->query("SELECT sort_order FROM popular_routes WHERE id = $id")->fetch_assoc();
    if ($row) {
        $cur = intval($row['sort_order']);
        $prev = $conn->query("SELECT id, sort_order FROM popular_routes WHERE sort_order < $cur ORDER BY sort_order DESC LIMIT 1")->fetch_assoc();
        if ($prev) {
            $pid = intval($prev['id']); $po = intval($prev['sort_order']);
            $conn->query("UPDATE popular_routes SET sort_order = $po WHERE id = $id");
            $conn->query("UPDATE popular_routes SET sort_order = $cur WHERE id = $pid");
        }
    }
    redirect('/admin/routes');
});

$router->post('/admin/routes/{id}/move-down', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id  = intval($id);
    $row = $conn->query("SELECT sort_order FROM popular_routes WHERE id = $id")->fetch_assoc();
    if ($row) {
        $cur = intval($row['sort_order']);
        $next = $conn->query("SELECT id, sort_order FROM popular_routes WHERE sort_order > $cur ORDER BY sort_order ASC LIMIT 1")->fetch_assoc();
        if ($next) {
            $nid = intval($next['id']); $no = intval($next['sort_order']);
            $conn->query("UPDATE popular_routes SET sort_order = $no WHERE id = $id");
            $conn->query("UPDATE popular_routes SET sort_order = $cur WHERE id = $nid");
        }
    }
    redirect('/admin/routes');
});

// Dispatch the request
echo $router->dispatch();
