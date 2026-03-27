<?php

/**
 * BusBook - Laravel-style Bus Booking System
 * Entry Point for XAMPP Environment
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session timeout: 5 minutes of inactivity (300 seconds)
define('SESSION_TIMEOUT', 5 * 60); // 5 minutes

// Enforce inactivity timeout for admin/operator sessions only
if (isset($_SESSION['user'])) {
    $current_time = time();
    $user_type = $_SESSION['user']['type'] ?? '';

    if ($user_type === 'admin' || $user_type === 'operator') {
        $last_activity = $_SESSION['last_activity'] ?? $current_time;

        // If more than 5 minutes have passed, force re-login with warning
        if (($current_time - $last_activity) > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['warning'] = 'Your session has expired due to inactivity. Please login again.';
            header('Location: /DMS_BOOKING/login');
            exit;
        }

        $_SESSION['last_activity'] = $current_time;
    } else {
        // Keep customer/public sessions from inheriting staff timeout tracking
        unset($_SESSION['last_activity']);
    }
}

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

// Audit logger for admin/operator transparency trail
function audit_log($action, $entity = null, $entity_id = null, $details = []) {
    global $conn;

    if (!$conn || !isset($_SESSION['user'])) return;

    $user = $_SESSION['user'];
    $actor_type = $user['type'] ?? 'unknown';
    if ($actor_type !== 'admin' && $actor_type !== 'operator') return;

    $actor_id = intval($user['id'] ?? 0);
    $actor_name = $conn->real_escape_string($user['name'] ?? 'Unknown');
    $actor_type_sql = $conn->real_escape_string($actor_type);
    $action_sql = $conn->real_escape_string($action);
    $entity_sql = $entity ? "'" . $conn->real_escape_string($entity) . "'" : "NULL";
    $entity_id_sql = ($entity_id !== null) ? intval($entity_id) : "NULL";
    $details_json = $conn->real_escape_string(json_encode($details, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    $ip = $conn->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '');
    $ua = $conn->real_escape_string(substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255));

    $conn->query("INSERT INTO audit_logs (actor_id, actor_type, actor_name, action, entity, entity_id, details, ip_address, user_agent)
                 VALUES ($actor_id, '$actor_type_sql', '$actor_name', '$action_sql', $entity_sql, $entity_id_sql, '$details_json', '$ip', '$ua')");
}

// Refresh session user data from DB on each request (queries correct table based on user_type)
if (isset($_SESSION['user']['id']) && isset($_SESSION['user']['type'])) {
    $uid = intval($_SESSION['user']['id']);
    $type = $_SESSION['user']['type'];
    
    $table = match($type) {
        'admin' => 'admins',
        'staff' => 'staff',
        'operator' => 'staff',
        default => 'customers',
    };
    
    $refreshed = $conn->query("SELECT * FROM $table WHERE id = $uid");
    if ($refreshed && $refreshed->num_rows === 1) {
        $fresh = $refreshed->fetch_assoc();
        $_SESSION['user'] = array_merge($fresh, ['type' => $type]);
    }
}

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
    $advisories = $conn->query("SELECT a.title, a.message, a.type, a.status, b.bus_number, b.from_location as bus_from, b.to_location as bus_to FROM advisories a LEFT JOIN buses b ON a.bus_id = b.id WHERE a.is_active = 1 ORDER BY a.created_at DESC")->fetch_all(MYSQLI_ASSOC);
    return view('home', ['user' => $user, 'popular_routes' => $popular_routes, 'advisories' => $advisories]);
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
    
    // Check if email exists in any table
    $check = $conn->query("SELECT 1 FROM customers WHERE email = '$email' UNION SELECT 1 FROM staff WHERE email = '$email' UNION SELECT 1 FROM admins WHERE email = '$email'");
    if ($check && $check->num_rows > 0) {
        $_SESSION['error'] = 'An account with this email already exists.';
        redirect('/');
    }
    
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $conn->query("INSERT INTO customers (name, email, password) VALUES ('$name', '$email', '$hashed')");
    $customer = $conn->query("SELECT * FROM customers WHERE email = '$email'")->fetch_assoc();
    $_SESSION['user'] = array_merge($customer, ['type' => 'customer']);
    unset($_SESSION['warning'], $_SESSION['error']);
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
            unset($_SESSION['warning'], $_SESSION['error']);
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
    // Auto-redirect if already authenticated
    if (isset($_SESSION['user'])) {
        $type = $_SESSION['user']['type'] ?? '';
        if ($type === 'admin') redirect('/admin');
        if ($type === 'operator') redirect('/operator');
    }
    return view('admin.login');
});

// Client-side inactivity timeout — destroys session and redirects to login with warning
$router->get('/session-expire', function() {
    if (isset($_SESSION['user'])) {
        $utype = $_SESSION['user']['type'] ?? 'unknown';
        if ($utype === 'admin' || $utype === 'operator') {
            audit_log('Session expired', 'auth', intval($_SESSION['user']['id'] ?? 0), ['reason' => 'client_inactivity_timeout']);
        }
    }
    session_destroy();
    session_start();
    $_SESSION['warning'] = 'Your session has expired due to inactivity. Please login again.';
    redirect('/login');
});

// Helper function to verify passwords (supports both bcrypt and MD5)
function verify_password($plain_password, $stored_hash) {
    // Try bcrypt first
    if (password_verify($plain_password, $stored_hash)) {
        return true;
    }
    // Fall back to MD5 for legacy passwords
    if (md5($plain_password) === $stored_hash) {
        return true;
    }
    return false;
}

$router->post('/login', function() {
    global $conn;
    
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username and password required';
        redirect('/login');
    }

    // Try to authenticate from admins table
    $result = $conn->query("SELECT * FROM admins WHERE name = '$username' OR email = '$username'");
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (verify_password($password, $user['password'])) {
            $_SESSION['user'] = array_merge($user, ['type' => 'admin']);
            unset($_SESSION['warning'], $_SESSION['error']);
            audit_log('Logged in', 'auth', intval($user['id']), ['login_as' => 'admin']);
            $_SESSION['success'] = 'Admin logged in successfully!';
            redirect('/admin');
        }
    }

    // Try to authenticate from staff table
    $result = $conn->query("SELECT * FROM staff WHERE name = '$username' OR email = '$username'");
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (verify_password($password, $user['password'])) {
            $type = ($user['role'] ?? 'operator') === 'admin' ? 'admin' : 'operator';
            $_SESSION['user'] = array_merge($user, ['type' => $type]);
            unset($_SESSION['warning'], $_SESSION['error']);
            audit_log('Logged in', 'auth', intval($user['id']), ['login_as' => $type]);
            if ($type === 'admin') {
                $_SESSION['success'] = 'Admin logged in successfully!';
                redirect('/admin');
            } else {
                $_SESSION['success'] = 'Operator logged in successfully!';
                redirect('/operator');
            }
        }
    }

    // Try to authenticate from customers table
    $result = $conn->query("SELECT * FROM customers WHERE name = '$username' OR email = '$username'");
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (verify_password($password, $user['password'])) {
            $_SESSION['user'] = array_merge($user, ['type' => 'customer']);
            unset($_SESSION['warning'], $_SESSION['error']);
            $_SESSION['success'] = 'Logged in successfully!';
            redirect('/dashboard');
        }
    }

    $_SESSION['error'] = 'Invalid credentials';
    redirect('/login');
});

$router->post('/logout', function() {
    if (isset($_SESSION['user'])) {
        audit_log('Logged out', 'auth', intval($_SESSION['user']['id'] ?? 0), ['logout_type' => ($_SESSION['user']['type'] ?? 'unknown')]);
    }
    session_destroy();
    session_start();
    $_SESSION['success'] = 'Logged out successfully!';
    redirect('/');
});

// Dashboard routes (require auth)
$router->get('/dashboard', function() {
    if (!isset($_SESSION['user'])) redirect('/?login=1');
    
    // Customers go back to home page
    if ($_SESSION['user']['type'] === 'customer') {
        redirect('/');
    }
    
    // This shouldn't be reached, but as fallback redirect to home
    redirect('/');
});

$router->get('/dashboard/book', function() {
    if (!isset($_SESSION['user'])) redirect('/?login=1');
    // Redirect to home for booking instead
    redirect('/');
});

$router->post('/dashboard/book', function() {
    if (!isset($_SESSION['user'])) redirect('/?login=1');
    if ($_SESSION['user']['type'] !== 'customer') redirect('/');
    
    global $conn;
    $user_id = $_SESSION['user']['id'];
    $from = $conn->real_escape_string($_POST['from_location'] ?? '');
    $to = $conn->real_escape_string($_POST['to_location'] ?? '');
    $date = $_POST['journey_date'] ?? '';
    $seats = intval($_POST['number_of_seats'] ?? 1);
    $bus_type = $conn->real_escape_string($_POST['bus_type'] ?? 'standard');

    if (empty($from) || empty($to) || empty($date)) {
        $_SESSION['error'] = 'All fields required';
        redirect('/');
    }

    $prices = ['standard' => 50, 'ac' => 75, 'sleeper' => 100];
    $price = ($prices[$bus_type] ?? 50) * $seats;

    $conn->query("INSERT INTO bookings (user_id, from_location, to_location, journey_date, number_of_seats, bus_type, total_price, status) 
                 VALUES ($user_id, '$from', '$to', '$date', $seats, '$bus_type', $price, 'pending')");

    $_SESSION['success'] = 'Booking created successfully!';
    redirect('/');
});

$router->post('/dashboard/bookings/{id}/cancel', function($id) {
    if (!isset($_SESSION['user'])) redirect('/?login=1');
    if ($_SESSION['user']['type'] !== 'customer') redirect('/');
    global $conn;
    $id = intval($id);
    $user_id = intval($_SESSION['user']['id']);
    $result = $conn->query("SELECT * FROM bookings WHERE id = $id AND user_id = $user_id");
    if ($result->num_rows === 0) {
        $_SESSION['error'] = 'Booking not found.';
        redirect('/');
    }
    $conn->query("UPDATE bookings SET status = 'cancelled' WHERE id = $id AND user_id = $user_id");
    $_SESSION['success'] = 'Booking cancelled.';
    redirect('/');
});

// Account Settings Modal - Form submission only
$router->post('/account-settings', function() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
        $_SESSION['error'] = 'You must be logged in as a customer to update settings';
        redirect('/');
    }
    
    global $conn;
    $user_id = intval($_SESSION['user']['id']);
    $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
    $phone = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
    $email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    
    if (empty($name) || empty($email)) {
        $_SESSION['error'] = 'Name and email are required';
        redirect('/');
    }
    
    // Check if email is already taken by another user
    $email_check = $conn->query("SELECT id FROM customers WHERE email = '$email' AND id != $user_id");
    if ($email_check && $email_check->num_rows > 0) {
        $_SESSION['error'] = 'Email already in use by another account';
        redirect('/');
    }
    
    // Update the database
    $update_result = $conn->query("UPDATE customers SET name = '$name', phone = '$phone', email = '$email', updated_at = NOW() WHERE id = $user_id");
    
    if (!$update_result) {
        $_SESSION['error'] = 'Failed to update profile: ' . $conn->error;
        redirect('/');
    }
    
    // Verify the update worked by fetching fresh data
    $user_result = $conn->query("SELECT * FROM customers WHERE id = $user_id");
    if ($user_result && $user_result->num_rows > 0) {
        $updated_user = $user_result->fetch_assoc();
        // Update session with fresh data
        $_SESSION['user'] = array_merge($updated_user, ['type' => 'customer']);
    }
    
    $_SESSION['success'] = 'Profile updated successfully!';
    redirect('/');
});

// Change Password Route
$router->post('/change-password', function() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'customer') {
        $_SESSION['error'] = 'You must be logged in to change your password';
        redirect('/');
    }
    
    global $conn;
    $user_id = intval($_SESSION['user']['id']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = 'All password fields are required';
        redirect('/');
    }
    
    if (strlen($new_password) < 8) {
        $_SESSION['error'] = 'New password must be at least 8 characters long';
        redirect('/');
    }
    
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = 'New passwords do not match';
        redirect('/');
    }
    
    // Get current password from database
    $user_result = $conn->query("SELECT password FROM customers WHERE id = $user_id");
    if (!$user_result || $user_result->num_rows === 0) {
        $_SESSION['error'] = 'User not found';
        redirect('/');
    }
    
    $user = $user_result->fetch_assoc();
    
    // Verify current password (supports both bcrypt and MD5)
    if (!verify_password($current_password, $user['password'])) {
        $_SESSION['error'] = 'Current password is incorrect';
        redirect('/');
    }
    
    // Hash new password using MD5 for consistency with existing system
    $new_password_hash = md5($new_password);
    
    // Update password
    $update_result = $conn->query("UPDATE customers SET password = '$new_password_hash', updated_at = NOW() WHERE id = $user_id");
    
    if (!$update_result) {
        $_SESSION['error'] = 'Failed to change password: ' . $conn->error;
        redirect('/');
    }
    
    $_SESSION['success'] = 'Password changed successfully!';
    redirect('/');
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
    $activity_logs = $conn->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 20")->fetch_all(MYSQLI_ASSOC);
    return view('admin.dashboard', compact('total_bookings','pending','confirmed','cancelled','total_users','total_buses','revenue','recent_bookings','activity_logs'));
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
    $bookings = $conn->query("SELECT b.*, c.name as user_name, c.email as user_email FROM bookings b JOIN users c ON b.user_id = c.id $where_sql ORDER BY b.created_at DESC")->fetch_all(MYSQLI_ASSOC);
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
    audit_log('Updated booking status', 'booking', $id, ['status' => $status]);
    $_SESSION['success'] = 'Booking status updated.';
    redirect('/admin/bookings');
});

$router->post('/admin/bookings/{id}/delete', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $conn->query("DELETE FROM bookings WHERE id = $id");
    audit_log('Deleted booking', 'booking', $id);
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
    $new_bus_id = intval($conn->insert_id);
    audit_log('Created bus', 'bus', $new_bus_id, ['bus_number' => $bus_number, 'from' => $from, 'to' => $to]);
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
    audit_log('Updated bus', 'bus', $id, ['bus_number' => $bus_number, 'from' => $from, 'to' => $to]);
    $_SESSION['success'] = 'Bus updated successfully.';
    redirect('/admin/buses');
});

$router->post('/admin/buses/{id}/delete', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $conn->query("DELETE FROM buses WHERE id = $id");
    audit_log('Deleted bus', 'bus', $id);
    $_SESSION['success'] = 'Bus deleted.';
    redirect('/admin/buses');
});

// Admin Users
$router->get('/admin/users', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $users = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM bookings WHERE user_id = c.id) as booking_count FROM customers c " . ($search ? "WHERE (c.name LIKE '%$search%' OR c.email LIKE '%$search%') " : '') . "ORDER BY c.created_at DESC")->fetch_all(MYSQLI_ASSOC);
    return view('admin.users.index', compact('users','search'));
});

$router->post('/admin/users/{id}/delete', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $conn->query("DELETE FROM customers WHERE id = $id");
    audit_log('Deleted customer', 'customer', $id);
    $_SESSION['success'] = 'Customer deleted.';
    redirect('/admin/users');
});

// ── Admin Staff Management ──
$router->get('/admin/staff', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $role_filter = isset($_GET['role']) && in_array($_GET['role'], ['admin','operator']) ? $_GET['role'] : '';
    
    // Get staff from staff table
    $staff_conditions = [];
    if ($role_filter) $staff_conditions[] = "role = '$role_filter'";
    if ($search) $staff_conditions[] = "(name LIKE '%$search%' OR email LIKE '%$search%')";
    $staff_where = $staff_conditions ? 'WHERE ' . implode(' AND ', $staff_conditions) : '';
    $staff = $conn->query("SELECT id, name, email, phone, role, permissions, created_at FROM staff $staff_where ORDER BY role ASC, created_at DESC")->fetch_all(MYSQLI_ASSOC) ?: [];
    
    // Get admins from admins table
    $admin_conditions = [];
    if (!$role_filter || $role_filter === 'admin') {
        if ($search) $admin_conditions[] = "(name LIKE '%$search%' OR email LIKE '%$search%')";
        $admin_where = $admin_conditions ? 'WHERE ' . implode(' AND ', $admin_conditions) : '';
        $admins = $conn->query("SELECT id, name, email, phone, 'admin' as role, NULL as permissions, created_at FROM admins $admin_where ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC) ?: [];
        $staff = array_merge($staff, $admins);
    }
    
    usort($staff, fn($a, $b) => $a['created_at'] <=> $b['created_at']);
    return view('admin.staff.index', compact('staff','search','role_filter'));
});

$router->post('/admin/staff/create', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
    $email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    $phone = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role = in_array($_POST['role'] ?? '', ['admin','operator']) ? $_POST['role'] : 'operator';

    if (empty($name) || empty($email) || strlen($password) < 6) {
        $_SESSION['error'] = 'Name, email, and password (min 6 chars) are required.';
        redirect('/admin/staff');
    }
    
    // Check email uniqueness across all tables
    $check = $conn->query("SELECT 1 FROM customers WHERE email = '$email' UNION SELECT 1 FROM staff WHERE email = '$email' UNION SELECT 1 FROM admins WHERE email = '$email'");
    if ($check && $check->num_rows > 0) {
        $_SESSION['error'] = 'A user with this email already exists.';
        redirect('/admin/staff');
    }
    
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    
    if ($role === 'admin') {
        $conn->query("INSERT INTO admins (name, email, password, phone) VALUES ('$name', '$email', '$hashed', '$phone')");
        $new_staff_id = intval($conn->insert_id);
        audit_log('Created admin account', 'admin', $new_staff_id, ['name' => $name, 'email' => $email]);
    } else {
        // Collect permissions for operators
        $valid_perms = ['manage_routes','manage_buses','manage_bookings','manage_advisory','view_activity_logs','view_reports','manage_users'];
        $perms = [];
        if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
            $perms = array_values(array_intersect($_POST['permissions'], $valid_perms));
        }
        $perms_json = $conn->real_escape_string(json_encode($perms));
        $conn->query("INSERT INTO staff (name, email, password, phone, role, permissions) VALUES ('$name', '$email', '$hashed', '$phone', 'operator', '$perms_json')");
        $new_staff_id = intval($conn->insert_id);
        audit_log('Created operator account', 'staff', $new_staff_id, ['name' => $name, 'email' => $email, 'permissions' => $perms]);
    }
    
    $_SESSION['success'] = ucfirst($role) . ' "' . htmlspecialchars($name) . '" created successfully.';
    redirect('/admin/staff');
});

$router->post('/admin/staff/{id}/role', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $current_user_id = intval($_SESSION['user']['id'] ?? 0);
    if ($id === $current_user_id) {
        $_SESSION['error'] = 'You cannot change your own role.';
        redirect('/admin/staff');
    }
    $role = in_array($_POST['role'] ?? '', ['admin','operator']) ? $_POST['role'] : 'operator';
    
    // Can only change operator role, not admin (admins are in different table)
    if ($role === 'operator') {
        $conn->query("UPDATE staff SET role = 'operator' WHERE id = $id");
        audit_log('Updated staff role', 'staff', $id, ['new_role' => 'operator']);
        $_SESSION['success'] = 'Staff role updated to Operator.';
    } else {
        $_SESSION['error'] = 'Cannot change admin role via this method.';
    }
    redirect('/admin/staff');
});

$router->post('/admin/staff/{id}/delete', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $current_user_id = intval($_SESSION['user']['id'] ?? 0);
    if ($id === $current_user_id) {
        $_SESSION['error'] = 'You cannot delete your own account.';
        redirect('/admin/staff');
    }
    // Delete from staff table (operators and non-admin staff)
    $conn->query("DELETE FROM staff WHERE id = $id");
    audit_log('Deleted staff account', 'staff', $id);
    $_SESSION['success'] = 'Staff member deleted.';
    redirect('/admin/staff');
});

$router->post('/admin/staff/{id}/edit', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $current_user_id = intval($_SESSION['user']['id'] ?? 0);

    $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
    $email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    $phone = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email)) {
        $_SESSION['error'] = 'Name and email are required.';
        redirect('/admin/staff');
    }

    // Check email uniqueness (exclude self, across all tables)
    $check = $conn->query("SELECT 1 FROM customers WHERE email = '$email' AND id != $id UNION SELECT 1 FROM staff WHERE email = '$email' AND id != $id UNION SELECT 1 FROM admins WHERE email = '$email' AND id != $id");
    if ($check && $check->num_rows > 0) {
        $_SESSION['error'] = 'Another user with this email already exists.';
        redirect('/admin/staff');
    }
    
    // Determine which table this staff is in
    $in_staff = $conn->query("SELECT role, permissions FROM staff WHERE id = $id")->fetch_assoc();
    $in_admin = $conn->query("SELECT id FROM admins WHERE id = $id")->num_rows > 0;
    
    if (!$in_staff && !$in_admin) {
        $_SESSION['error'] = 'Staff member not found.';
        redirect('/admin/staff');
    }

    // Build update query
    $updates = "name = '$name', email = '$email', phone = '$phone'";
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters.';
            redirect('/admin/staff');
        }
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $updates .= ", password = '$hashed'";
    }

    if ($in_staff) {
        // Update permissions for operators
        $valid_perms = ['manage_routes','manage_buses','manage_bookings','manage_advisory','view_activity_logs','view_reports','manage_users'];
        $perms = [];
        if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
            $perms = array_values(array_intersect($_POST['permissions'], $valid_perms));
        }
        $perms_json = $conn->real_escape_string(json_encode($perms));
        $updates .= ", permissions = '$perms_json'";
        $conn->query("UPDATE staff SET $updates WHERE id = $id");
        audit_log('Updated staff account', 'staff', $id, ['name' => $name, 'email' => $email, 'permissions' => $perms]);
    } else {
        // Just update basic info for admins
        $conn->query("UPDATE admins SET $updates WHERE id = $id");
        audit_log('Updated admin account', 'admin', $id, ['name' => $name, 'email' => $email]);
    }
    
    $_SESSION['success'] = 'Staff member "' . htmlspecialchars($name) . '" updated successfully.';
    redirect('/admin/staff');
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
    $new_route_id = intval($conn->insert_id);
    audit_log('Created route', 'route', $new_route_id, ['from' => $from, 'to' => $to]);
    $_SESSION['success'] = 'Route added successfully.';
    redirect('/admin/routes');
});

$router->post('/admin/routes/{id}/delete', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $conn->query("DELETE FROM popular_routes WHERE id = $id");
    audit_log('Deleted route', 'route', $id);
    $_SESSION['success'] = 'Route deleted.';
    redirect('/admin/routes');
});

$router->post('/admin/routes/{id}/toggle', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $conn->query("UPDATE popular_routes SET is_active = 1 - is_active WHERE id = $id");
    audit_log('Toggled route visibility', 'route', $id);
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
    audit_log('Updated route', 'route', $id, ['from' => $from, 'to' => $to]);
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
    audit_log('Moved route up', 'route', $id);
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
    audit_log('Moved route down', 'route', $id);
    redirect('/admin/routes');
});

// ── Admin Advisory ──
$router->get('/admin/advisory', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    
    // Fetch advisories with author names using UNION JOIN
    $advisories = $conn->query("
        SELECT a.*, 
               COALESCE(u.name, s.name) as author_name,
               b.bus_number, b.from_location as bus_from, b.to_location as bus_to
        FROM advisories a
        LEFT JOIN admins u ON a.created_by = u.id AND a.created_by_type = 'admin'
        LEFT JOIN staff s ON a.created_by = s.id AND a.created_by_type IN ('staff', 'operator')
        LEFT JOIN buses b ON a.bus_id = b.id
        ORDER BY a.created_at DESC
    ")->fetch_all(MYSQLI_ASSOC);
    
    $buses = $conn->query("SELECT id, bus_number, from_location, to_location FROM buses ORDER BY bus_number ASC")->fetch_all(MYSQLI_ASSOC);
    $panel = 'admin';
    return view('admin.advisory.index', compact('advisories','buses','panel'));
});

$router->post('/admin/advisory', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $title   = $conn->real_escape_string(trim($_POST['title'] ?? ''));
    $message = $conn->real_escape_string(trim($_POST['message'] ?? ''));
    $type    = in_array($_POST['type'] ?? '', ['info','warning','danger','success']) ? $_POST['type'] : 'info';
    $bus_id  = !empty($_POST['bus_id']) ? intval($_POST['bus_id']) : 'NULL';
    $status  = $conn->real_escape_string(trim($_POST['status'] ?? ''));
    $status_sql = !empty($status) ? "'$status'" : 'NULL';
    if (empty($title) || empty($message)) {
        $_SESSION['error'] = 'Title and message are required.';
        redirect('/admin/advisory');
    }
    $user_id = intval($_SESSION['user']['id']);
    $user_type = $_SESSION['user']['type']; // 'admin' or 'staff'
    $conn->query("INSERT INTO advisories (title, message, type, bus_id, status, created_by, created_by_type) VALUES ('$title', '$message', '$type', $bus_id, $status_sql, $user_id, '$user_type')");
    $new_adv_id = intval($conn->insert_id);
    audit_log('Created advisory', 'advisory', $new_adv_id, ['title' => $title, 'type' => $type]);
    $_SESSION['success'] = 'Advisory posted successfully.';
    redirect('/admin/advisory');
});

$router->post('/admin/advisory/{id}/toggle', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $advBefore = $conn->query("SELECT title, type, status, bus_id, is_active FROM advisories WHERE id = $id")->fetch_assoc();
    $conn->query("UPDATE advisories SET is_active = 1 - is_active WHERE id = $id");
    $fromState = ($advBefore && intval($advBefore['is_active']) === 1) ? 'active' : 'inactive';
    $toState = ($fromState === 'active') ? 'inactive' : 'active';
    audit_log('Toggled advisory status', 'advisory', $id, [
        'title' => $advBefore['title'] ?? 'Unknown advisory',
        'type' => $advBefore['type'] ?? null,
        'status' => $advBefore['status'] ?? null,
        'bus_id' => $advBefore['bus_id'] ?? null,
        'from' => $fromState,
        'to' => $toState,
    ]);
    $_SESSION['success'] = 'Advisory status updated.';
    redirect('/admin/advisory');
});

$router->post('/admin/advisory/{id}/delete', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $advBefore = $conn->query("SELECT title, type, status, bus_id, is_active FROM advisories WHERE id = $id")->fetch_assoc();
    $conn->query("DELETE FROM advisories WHERE id = $id");
    audit_log('Deleted advisory', 'advisory', $id, [
        'title' => $advBefore['title'] ?? 'Unknown advisory',
        'type' => $advBefore['type'] ?? null,
        'status' => $advBefore['status'] ?? null,
        'bus_id' => $advBefore['bus_id'] ?? null,
        'state' => ($advBefore && intval($advBefore['is_active']) === 1) ? 'active' : 'inactive',
    ]);
    $_SESSION['success'] = 'Advisory deleted.';
    redirect('/admin/advisory');
});

$router->post('/admin/advisory/{id}/update', function($id) use ($requireAdmin) {
    $requireAdmin();
    global $conn;
    $id = intval($id);
    $title   = $conn->real_escape_string(trim($_POST['title'] ?? ''));
    $message = $conn->real_escape_string(trim($_POST['message'] ?? ''));
    $type    = in_array($_POST['type'] ?? '', ['info','warning','danger','success']) ? $_POST['type'] : 'info';
    $bus_id  = !empty($_POST['bus_id']) ? intval($_POST['bus_id']) : 'NULL';
    $status  = $conn->real_escape_string(trim($_POST['status'] ?? ''));
    $status_sql = !empty($status) ? "'$status'" : 'NULL';
    
    if (empty($title) || empty($message)) {
        $_SESSION['error'] = 'Title and message are required.';
        redirect('/admin/advisory');
    }
    
    $conn->query("UPDATE advisories SET title = '$title', message = '$message', type = '$type', bus_id = $bus_id, status = $status_sql WHERE id = $id");
    audit_log('Updated advisory', 'advisory', $id, ['title' => $title, 'type' => $type]);
    $_SESSION['success'] = 'Advisory updated successfully.';
    redirect('/admin/advisory');
});

// Admin Activity Logs
$router->get('/admin/logs', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;

    $role_filter = isset($_GET['role']) ? $conn->real_escape_string(trim($_GET['role'])) : '';
    $entity_filter = isset($_GET['entity']) ? $conn->real_escape_string(trim($_GET['entity'])) : '';
    $search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';

    $where = [];
    if ($role_filter && in_array($role_filter, ['admin', 'operator'])) {
        $where[] = "actor_type = '$role_filter'";
    }
    if ($entity_filter) {
        $where[] = "entity = '$entity_filter'";
    }
    if ($search) {
        $where[] = "(actor_name LIKE '%$search%' OR action LIKE '%$search%' OR details LIKE '%$search%')";
    }

    $where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
    $activity_logs = $conn->query("SELECT * FROM audit_logs $where_sql ORDER BY created_at DESC LIMIT 100")->fetch_all(MYSQLI_ASSOC);
    $entities = $conn->query("SELECT DISTINCT entity FROM audit_logs WHERE entity IS NOT NULL AND entity <> '' ORDER BY entity ASC")->fetch_all(MYSQLI_ASSOC);
    $panel = 'admin';

    return view('admin.logs.index', compact('activity_logs', 'entities', 'role_filter', 'entity_filter', 'search', 'panel'));
});

$router->get('/admin/logs/live', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;

    $role_filter = isset($_GET['role']) ? $conn->real_escape_string(trim($_GET['role'])) : '';
    $entity_filter = isset($_GET['entity']) ? $conn->real_escape_string(trim($_GET['entity'])) : '';
    $search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
    $since_id = intval($_GET['since_id'] ?? 0);

    $where = ["id > $since_id"];
    if ($role_filter && in_array($role_filter, ['admin', 'operator'])) {
        $where[] = "actor_type = '$role_filter'";
    }
    if ($entity_filter) {
        $where[] = "entity = '$entity_filter'";
    }
    if ($search) {
        $where[] = "(actor_name LIKE '%$search%' OR action LIKE '%$search%' OR details LIKE '%$search%')";
    }

    $where_sql = 'WHERE ' . implode(' AND ', $where);
    $new_logs = $conn->query("SELECT * FROM audit_logs $where_sql ORDER BY id ASC LIMIT 50")->fetch_all(MYSQLI_ASSOC);

    header('Content-Type: application/json');
    echo json_encode(['logs' => $new_logs]);
    exit;
});

$router->get('/admin/logs/stream', function() use ($requireAdmin) {
    $requireAdmin();
    global $conn;

    $role_filter = isset($_GET['role']) ? $conn->real_escape_string(trim($_GET['role'])) : '';
    $entity_filter = isset($_GET['entity']) ? $conn->real_escape_string(trim($_GET['entity'])) : '';
    $search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
    $last_id = intval($_GET['since_id'] ?? 0);

    ignore_user_abort(true);
    @set_time_limit(0);
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache, no-transform');
    header('Connection: keep-alive');

    $started_at = time();
    while (!connection_aborted() && (time() - $started_at) < 300) {
        $where = ["id > $last_id"];
        if ($role_filter && in_array($role_filter, ['admin', 'operator'])) {
            $where[] = "actor_type = '$role_filter'";
        }
        if ($entity_filter) {
            $where[] = "entity = '$entity_filter'";
        }
        if ($search) {
            $where[] = "(actor_name LIKE '%$search%' OR action LIKE '%$search%' OR details LIKE '%$search%')";
        }

        $where_sql = 'WHERE ' . implode(' AND ', $where);
        $new_logs = $conn->query("SELECT * FROM audit_logs $where_sql ORDER BY id ASC LIMIT 50")->fetch_all(MYSQLI_ASSOC);

        foreach ($new_logs as $log) {
            $lid = intval($log['id'] ?? 0);
            if ($lid > $last_id) $last_id = $lid;
            echo "event: log\n";
            echo 'data: ' . json_encode($log, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n\n";
        }

        echo ": keepalive\n\n";
        @ob_flush();
        @flush();
        sleep(1);
    }
    exit;
});

// =====================
// OPERATOR ROUTES
// =====================

$requireOperator = function() {
    if (!isset($_SESSION['user'])) redirect('/login');
    $type = $_SESSION['user']['type'] ?? '';
    if ($type !== 'operator' && $type !== 'admin') {
        $_SESSION['error'] = 'Access denied.';
        redirect('/');
    }
};

// Helper: check if session user has a specific permission
$hasPermission = function($perm) {
    $perms = json_decode($_SESSION['user']['permissions'] ?? '[]', true);
    if (!is_array($perms)) $perms = [];
    // Admins accessing operator routes get all permissions
    if (($_SESSION['user']['type'] ?? '') === 'admin') return true;
    return in_array($perm, $perms);
};

// Operator Dashboard
$router->get('/operator', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    global $conn;
    $total_bookings = $pending = $confirmed = $cancelled = $total_users = $total_buses = $revenue = 0;
    $recent_bookings = [];
    if ($hasPermission('manage_bookings')) {
        $total_bookings = $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'];
        $pending = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='pending'")->fetch_assoc()['c'];
        $confirmed = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='confirmed'")->fetch_assoc()['c'];
        $cancelled = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='cancelled'")->fetch_assoc()['c'];
        $recent_bookings = $conn->query("SELECT b.*, c.name as user_name FROM bookings b JOIN customers c ON b.user_id = c.id ORDER BY b.created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
    }
    if ($hasPermission('manage_users')) {
        $total_users = $conn->query("SELECT COUNT(*) as c FROM customers")->fetch_assoc()['c'];
    }
    if ($hasPermission('manage_buses')) {
        $total_buses = $conn->query("SELECT COUNT(*) as c FROM buses")->fetch_assoc()['c'];
    }
    if ($hasPermission('view_reports')) {
        $revenue_row = $conn->query("SELECT SUM(total_price) as r FROM bookings WHERE status='confirmed'")->fetch_assoc();
        $revenue = $revenue_row['r'] ?? 0;
    }
    return view('operator.dashboard', compact('total_bookings','pending','confirmed','cancelled','total_users','total_buses','revenue','recent_bookings'));
});

// Operator Bookings
$router->get('/operator/bookings', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_bookings')) { $_SESSION['error'] = 'No permission to manage bookings.'; redirect('/operator'); }
    global $conn;
    $status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $where = [];
    if ($status_filter) $where[] = "b.status = '$status_filter'";
    if ($search) $where[] = "(u.name LIKE '%$search%' OR b.from_location LIKE '%$search%' OR b.to_location LIKE '%$search%')";
    $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $bookings = $conn->query("SELECT b.*, c.name as user_name, c.email as user_email FROM bookings b JOIN users c ON b.user_id = c.id $where_sql ORDER BY b.created_at DESC")->fetch_all(MYSQLI_ASSOC);
    return view('admin.bookings.index', compact('bookings','status_filter','search'));
});

$router->post('/operator/bookings/{id}/status', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_bookings')) { $_SESSION['error'] = 'No permission.'; redirect('/operator'); }
    global $conn;
    $id = intval($id);
    $allowed = ['pending','confirmed','cancelled'];
    $status = $_POST['status'] ?? '';
    if (!in_array($status, $allowed)) { $_SESSION['error'] = 'Invalid status.'; redirect('/operator/bookings'); }
    $status = $conn->real_escape_string($status);
    $conn->query("UPDATE bookings SET status = '$status' WHERE id = $id");
    audit_log('Updated booking status', 'booking', $id, ['status' => $status]);
    $_SESSION['success'] = 'Booking status updated.';
    redirect('/operator/bookings');
});

// Operator Buses
$router->get('/operator/buses', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_buses')) { $_SESSION['error'] = 'No permission to manage buses.'; redirect('/operator'); }
    global $conn;
    $buses = $conn->query("SELECT * FROM buses ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
    return view('admin.buses.index', compact('buses'));
});

$router->get('/operator/buses/create', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_buses')) { $_SESSION['error'] = 'No permission.'; redirect('/operator'); }
    return view('admin.buses.create', []);
});

$router->post('/operator/buses', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_buses')) { $_SESSION['error'] = 'No permission.'; redirect('/operator'); }
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
        redirect('/operator/buses/create');
    }
    $conn->query("INSERT INTO buses (bus_number, from_location, to_location, journey_time, journey_date, total_seats, available_seats, price_per_seat, bus_type) VALUES ('$bus_number','$from','$to','$time','$date',$total,$available,$price,'$bus_type')");
    $new_bus_id = intval($conn->insert_id);
    audit_log('Created bus', 'bus', $new_bus_id, ['bus_number' => $bus_number, 'from' => $from, 'to' => $to]);
    $_SESSION['success'] = 'Bus added successfully.';
    redirect('/operator/buses');
});

$router->get('/operator/buses/{id}/edit', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_buses')) { $_SESSION['error'] = 'No permission.'; redirect('/operator'); }
    global $conn;
    $id = intval($id);
    $result = $conn->query("SELECT * FROM buses WHERE id = $id");
    if ($result->num_rows === 0) { $_SESSION['error'] = 'Bus not found.'; redirect('/operator/buses'); }
    $bus = $result->fetch_assoc();
    return view('admin.buses.edit', compact('bus'));
});

$router->post('/operator/buses/{id}/update', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_buses')) { $_SESSION['error'] = 'No permission.'; redirect('/operator'); }
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
    audit_log('Updated bus', 'bus', $id, ['bus_number' => $bus_number, 'from' => $from, 'to' => $to]);
    $_SESSION['success'] = 'Bus updated successfully.';
    redirect('/operator/buses');
});

$router->post('/operator/buses/{id}/delete', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_buses')) { $_SESSION['error'] = 'No permission.'; redirect('/operator'); }
    global $conn;
    $id = intval($id);
    $conn->query("DELETE FROM buses WHERE id = $id");
    audit_log('Deleted bus', 'bus', $id);
    $_SESSION['success'] = 'Bus deleted.';
    redirect('/operator/buses');
});

// Operator Users
$router->get('/operator/users', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_users')) { $_SESSION['error'] = 'No permission to manage users.'; redirect('/operator'); }
    global $conn;
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $users = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM bookings WHERE user_id = c.id) as booking_count FROM customers c " . ($search ? "WHERE (c.name LIKE '%$search%' OR c.email LIKE '%$search%') " : '') . "ORDER BY c.created_at DESC")->fetch_all(MYSQLI_ASSOC);
    return view('admin.users.index', compact('users','search'));
});

$router->post('/operator/users/{id}/delete', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_users')) { $_SESSION['error'] = 'No permission.'; redirect('/operator'); }
    global $conn;
    $id = intval($id);
    $conn->query("DELETE FROM customers WHERE id = $id");
    audit_log('Deleted customer', 'customer', $id);
    $_SESSION['success'] = 'Customer deleted.';
    redirect('/operator/users');
});

// Operator Routes
$router->get('/operator/routes', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_routes')) { $_SESSION['error'] = 'No permission to manage routes.'; redirect('/operator'); }
    global $conn;
    $routes = $conn->query("SELECT * FROM popular_routes ORDER BY sort_order ASC, id ASC")->fetch_all(MYSQLI_ASSOC);
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

$router->post('/operator/routes', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_routes')) { $_SESSION['error'] = 'No permission.'; redirect('/operator'); }
    global $conn;
    $from     = $conn->real_escape_string(trim($_POST['from_location'] ?? ''));
    $to       = $conn->real_escape_string(trim($_POST['to_location'] ?? ''));
    $duration = $conn->real_escape_string(trim($_POST['duration'] ?? ''));
    $price    = floatval($_POST['price_from'] ?? 0);
    if (empty($from) || empty($to)) { $_SESSION['error'] = 'From and To locations are required.'; redirect('/operator/routes'); }
    $max = $conn->query("SELECT COALESCE(MAX(sort_order),0) as m FROM popular_routes")->fetch_assoc()['m'];
    $order = intval($max) + 1;
    $conn->query("INSERT INTO popular_routes (from_location, to_location, duration, price_from, sort_order) VALUES ('$from','$to','$duration',$price,$order)");
    $new_route_id = intval($conn->insert_id);
    audit_log('Created route', 'route', $new_route_id, ['from' => $from, 'to' => $to]);
    $_SESSION['success'] = 'Route added successfully.';
    redirect('/operator/routes');
});

$router->post('/operator/routes/{id}/delete', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_routes')) { $_SESSION['error'] = 'No permission.'; redirect('/operator'); }
    global $conn;
    $id = intval($id);
    $conn->query("DELETE FROM popular_routes WHERE id = $id");
    audit_log('Deleted route', 'route', $id);
    $_SESSION['success'] = 'Route deleted.';
    redirect('/operator/routes');
});

$router->post('/operator/routes/{id}/toggle', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_routes')) { redirect('/operator'); }
    global $conn;
    $id = intval($id);
    $conn->query("UPDATE popular_routes SET is_active = 1 - is_active WHERE id = $id");
    audit_log('Toggled route visibility', 'route', $id);
    redirect('/operator/routes');
});

$router->get('/operator/routes/{id}/edit', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_routes')) { $_SESSION['error'] = 'No permission.'; redirect('/operator'); }
    global $conn;
    $id = intval($id);
    $result = $conn->query("SELECT * FROM popular_routes WHERE id = $id");
    if ($result->num_rows === 0) { $_SESSION['error'] = 'Route not found.'; redirect('/operator/routes'); }
    $route = $result->fetch_assoc();
    return view('admin.routes.edit', compact('route'));
});

$router->post('/operator/routes/{id}/update', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_routes')) { $_SESSION['error'] = 'No permission.'; redirect('/operator'); }
    global $conn;
    $id       = intval($id);
    $from     = $conn->real_escape_string(trim($_POST['from_location'] ?? ''));
    $to       = $conn->real_escape_string(trim($_POST['to_location'] ?? ''));
    $duration = $conn->real_escape_string(trim($_POST['duration'] ?? ''));
    $price    = floatval($_POST['price_from'] ?? 0);
    if (empty($from) || empty($to)) { $_SESSION['error'] = 'From and To locations are required.'; redirect('/operator/routes'); }
    $conn->query("UPDATE popular_routes SET from_location='$from', to_location='$to', duration='$duration', price_from=$price WHERE id=$id");
    audit_log('Updated route', 'route', $id, ['from' => $from, 'to' => $to]);
    $_SESSION['success'] = 'Route updated successfully.';
    redirect('/operator/routes');
});

$router->post('/operator/routes/{id}/move-up', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_routes')) { redirect('/operator'); }
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
    audit_log('Moved route up', 'route', $id);
    redirect('/operator/routes');
});

$router->post('/operator/routes/{id}/move-down', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_routes')) { redirect('/operator'); }
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
    audit_log('Moved route down', 'route', $id);
    redirect('/operator/routes');
});

// Operator Advisory
$router->get('/operator/advisory', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_advisory')) { $_SESSION['error'] = 'No permission to manage advisory.'; redirect('/operator'); }
    global $conn;
    
    // Fetch advisories with author names using UNION JOIN
    $advisories = $conn->query("
        SELECT a.*, 
               COALESCE(u.name, s.name) as author_name,
               b.bus_number, b.from_location as bus_from, b.to_location as bus_to
        FROM advisories a
        LEFT JOIN admins u ON a.created_by = u.id AND a.created_by_type = 'admin'
        LEFT JOIN staff s ON a.created_by = s.id AND a.created_by_type IN ('staff', 'operator')
        LEFT JOIN buses b ON a.bus_id = b.id
        ORDER BY a.created_at DESC
    ")->fetch_all(MYSQLI_ASSOC);
    
    $buses = $conn->query("SELECT id, bus_number, from_location, to_location FROM buses ORDER BY bus_number ASC")->fetch_all(MYSQLI_ASSOC);
    $panel = 'operator';
    return view('admin.advisory.index', compact('advisories','buses','panel'));
});

$router->post('/operator/advisory', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_advisory')) { $_SESSION['error'] = 'No permission.'; redirect('/operator'); }
    global $conn;
    $title   = $conn->real_escape_string(trim($_POST['title'] ?? ''));
    $message = $conn->real_escape_string(trim($_POST['message'] ?? ''));
    $type    = in_array($_POST['type'] ?? '', ['info','warning','danger','success']) ? $_POST['type'] : 'info';
    $bus_id  = !empty($_POST['bus_id']) ? intval($_POST['bus_id']) : 'NULL';
    $status  = $conn->real_escape_string(trim($_POST['status'] ?? ''));
    $status_sql = !empty($status) ? "'$status'" : 'NULL';
    if (empty($title) || empty($message)) {
        $_SESSION['error'] = 'Title and message are required.';
        redirect('/operator/advisory');
    }
    $user_id = intval($_SESSION['user']['id']);
    $user_type = $_SESSION['user']['type']; // 'operator' or 'admin'
    $conn->query("INSERT INTO advisories (title, message, type, bus_id, status, created_by, created_by_type) VALUES ('$title', '$message', '$type', $bus_id, $status_sql, $user_id, '$user_type')");
    $new_adv_id = intval($conn->insert_id);
    audit_log('Created advisory', 'advisory', $new_adv_id, ['title' => $title, 'type' => $type]);
    $_SESSION['success'] = 'Advisory posted successfully.';
    redirect('/operator/advisory');
});

$router->post('/operator/advisory/{id}/toggle', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_advisory')) { redirect('/operator'); }
    global $conn;
    $id = intval($id);
    $advBefore = $conn->query("SELECT title, type, status, bus_id, is_active FROM advisories WHERE id = $id")->fetch_assoc();
    $conn->query("UPDATE advisories SET is_active = 1 - is_active WHERE id = $id");
    $fromState = ($advBefore && intval($advBefore['is_active']) === 1) ? 'active' : 'inactive';
    $toState = ($fromState === 'active') ? 'inactive' : 'active';
    audit_log('Toggled advisory status', 'advisory', $id, [
        'title' => $advBefore['title'] ?? 'Unknown advisory',
        'type' => $advBefore['type'] ?? null,
        'status' => $advBefore['status'] ?? null,
        'bus_id' => $advBefore['bus_id'] ?? null,
        'from' => $fromState,
        'to' => $toState,
    ]);
    $_SESSION['success'] = 'Advisory status updated.';
    redirect('/operator/advisory');
});

$router->post('/operator/advisory/{id}/delete', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_advisory')) { redirect('/operator'); }
    global $conn;
    $id = intval($id);
    $advBefore = $conn->query("SELECT title, type, status, bus_id, is_active FROM advisories WHERE id = $id")->fetch_assoc();
    $conn->query("DELETE FROM advisories WHERE id = $id");
    audit_log('Deleted advisory', 'advisory', $id, [
        'title' => $advBefore['title'] ?? 'Unknown advisory',
        'type' => $advBefore['type'] ?? null,
        'status' => $advBefore['status'] ?? null,
        'bus_id' => $advBefore['bus_id'] ?? null,
        'state' => ($advBefore && intval($advBefore['is_active']) === 1) ? 'active' : 'inactive',
    ]);
    $_SESSION['success'] = 'Advisory deleted.';
    redirect('/operator/advisory');
});

$router->post('/operator/advisory/{id}/update', function($id) use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('manage_advisory')) { redirect('/operator'); }
    global $conn;
    $id = intval($id);
    $title   = $conn->real_escape_string(trim($_POST['title'] ?? ''));
    $message = $conn->real_escape_string(trim($_POST['message'] ?? ''));
    $type    = in_array($_POST['type'] ?? '', ['info','warning','danger','success']) ? $_POST['type'] : 'info';
    $bus_id  = !empty($_POST['bus_id']) ? intval($_POST['bus_id']) : 'NULL';
    $status  = $conn->real_escape_string(trim($_POST['status'] ?? ''));
    $status_sql = !empty($status) ? "'$status'" : 'NULL';
    
    if (empty($title) || empty($message)) {
        $_SESSION['error'] = 'Title and message are required.';
        redirect('/operator/advisory');
    }
    
    $conn->query("UPDATE advisories SET title = '$title', message = '$message', type = '$type', bus_id = $bus_id, status = $status_sql WHERE id = $id");
    audit_log('Updated advisory', 'advisory', $id, ['title' => $title, 'type' => $type]);
    $_SESSION['success'] = 'Advisory updated successfully.';
    redirect('/operator/advisory');
});

// Operator Activity Logs (requires activity logs permission)
$router->get('/operator/logs', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('view_activity_logs')) { $_SESSION['error'] = 'No permission to view logs.'; redirect('/operator'); }
    global $conn;

    $role_filter = isset($_GET['role']) ? $conn->real_escape_string(trim($_GET['role'])) : '';
    $entity_filter = isset($_GET['entity']) ? $conn->real_escape_string(trim($_GET['entity'])) : '';
    $search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';

    $where = [];
    if ($role_filter && in_array($role_filter, ['admin', 'operator'])) {
        $where[] = "actor_type = '$role_filter'";
    }
    if ($entity_filter) {
        $where[] = "entity = '$entity_filter'";
    }
    if ($search) {
        $where[] = "(actor_name LIKE '%$search%' OR action LIKE '%$search%' OR details LIKE '%$search%')";
    }

    $where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
    $activity_logs = $conn->query("SELECT * FROM audit_logs $where_sql ORDER BY created_at DESC LIMIT 100")->fetch_all(MYSQLI_ASSOC);
    $entities = $conn->query("SELECT DISTINCT entity FROM audit_logs WHERE entity IS NOT NULL AND entity <> '' ORDER BY entity ASC")->fetch_all(MYSQLI_ASSOC);
    $panel = 'operator';

    return view('admin.logs.index', compact('activity_logs', 'entities', 'role_filter', 'entity_filter', 'search', 'panel'));
});

$router->get('/operator/logs/live', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('view_activity_logs')) { header('Content-Type: application/json'); echo json_encode(['logs' => []]); exit; }
    global $conn;

    $role_filter = isset($_GET['role']) ? $conn->real_escape_string(trim($_GET['role'])) : '';
    $entity_filter = isset($_GET['entity']) ? $conn->real_escape_string(trim($_GET['entity'])) : '';
    $search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
    $since_id = intval($_GET['since_id'] ?? 0);

    $where = ["id > $since_id"];
    if ($role_filter && in_array($role_filter, ['admin', 'operator'])) {
        $where[] = "actor_type = '$role_filter'";
    }
    if ($entity_filter) {
        $where[] = "entity = '$entity_filter'";
    }
    if ($search) {
        $where[] = "(actor_name LIKE '%$search%' OR action LIKE '%$search%' OR details LIKE '%$search%')";
    }

    $where_sql = 'WHERE ' . implode(' AND ', $where);
    $new_logs = $conn->query("SELECT * FROM audit_logs $where_sql ORDER BY id ASC LIMIT 50")->fetch_all(MYSQLI_ASSOC);

    header('Content-Type: application/json');
    echo json_encode(['logs' => $new_logs]);
    exit;
});

$router->get('/operator/logs/stream', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('view_activity_logs')) {
        header('HTTP/1.1 403 Forbidden');
        exit;
    }
    global $conn;

    $role_filter = isset($_GET['role']) ? $conn->real_escape_string(trim($_GET['role'])) : '';
    $entity_filter = isset($_GET['entity']) ? $conn->real_escape_string(trim($_GET['entity'])) : '';
    $search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
    $last_id = intval($_GET['since_id'] ?? 0);

    ignore_user_abort(true);
    @set_time_limit(0);
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache, no-transform');
    header('Connection: keep-alive');

    $started_at = time();
    while (!connection_aborted() && (time() - $started_at) < 300) {
        $where = ["id > $last_id"];
        if ($role_filter && in_array($role_filter, ['admin', 'operator'])) {
            $where[] = "actor_type = '$role_filter'";
        }
        if ($entity_filter) {
            $where[] = "entity = '$entity_filter'";
        }
        if ($search) {
            $where[] = "(actor_name LIKE '%$search%' OR action LIKE '%$search%' OR details LIKE '%$search%')";
        }

        $where_sql = 'WHERE ' . implode(' AND ', $where);
        $new_logs = $conn->query("SELECT * FROM audit_logs $where_sql ORDER BY id ASC LIMIT 50")->fetch_all(MYSQLI_ASSOC);

        foreach ($new_logs as $log) {
            $lid = intval($log['id'] ?? 0);
            if ($lid > $last_id) $last_id = $lid;
            echo "event: log\n";
            echo 'data: ' . json_encode($log, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n\n";
        }

        echo ": keepalive\n\n";
        @ob_flush();
        @flush();
        sleep(1);
    }
    exit;
});

// Operator Reports
$router->get('/operator/reports', function() use ($requireOperator, $hasPermission) {
    $requireOperator();
    if (!$hasPermission('view_reports')) { $_SESSION['error'] = 'No permission to view reports.'; redirect('/operator'); }
    global $conn;
    $total_bookings = $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'];
    $pending = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='pending'")->fetch_assoc()['c'];
    $confirmed = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='confirmed'")->fetch_assoc()['c'];
    $cancelled = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='cancelled'")->fetch_assoc()['c'];
    $revenue_row = $conn->query("SELECT SUM(total_price) as r FROM bookings WHERE status='confirmed'")->fetch_assoc();
    $total_revenue = $revenue_row['r'] ?? 0;
    $confirmed_bookings = $conn->query("SELECT b.*, c.name as user_name FROM bookings b JOIN users c ON b.user_id = c.id WHERE b.status='confirmed' ORDER BY b.created_at DESC LIMIT 50")->fetch_all(MYSQLI_ASSOC);
    return view('operator.reports', compact('total_bookings','pending','confirmed','cancelled','total_revenue','confirmed_bookings'));
});

// Dispatch the request
echo $router->dispatch();
