<?php
session_start();

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base paths
define('BASE_PATH', dirname(dirname(__FILE__)));
define('APP_PATH', BASE_PATH . '/app');
define('RESOURCES_PATH', BASE_PATH . '/resources');

// Include database initialization
require_once BASE_PATH . '/database/init.php';

// Database connection
$_conn = new mysqli('localhost', 'root', '', 'DMS_BOOKING');
if ($_conn->connect_error) {
    die("Database connection failed");
}

// Simple routing and view engine
class AppBootstrap
{
    private static $handlers = [];
    private static $conn;

    public static function setConnection($conn)
    {
        self::$conn = $conn;
    }

    public static function getConnection()
    {
        return self::$conn;
    }

    public static function registerRoute($method, $path, $handler)
    {
        self::$handlers[strtoupper($method) . ':' . $path] = $handler;
    }

    public static function view($view, $data = [])
    {
        extract($data);
        $viewPath = RESOURCES_PATH . '/views/' . str_replace('.', '/', $view) . '.blade.php';
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }

    public static function auth()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function isAuthenticated()
    {
        return isset($_SESSION['user']);
    }
}

AppBootstrap::setConnection($_conn);

// Store connection globally for controllers
$GLOBALS['db_connection'] = $_conn;
$GLOBALS['auth_user'] = $_SESSION['user'] ?? null;

return new class {
    public function make($class)
    {
        if ($class === 'Illuminate\Contracts\Http\Kernel') {
            return new class {
                public function handle($request)
                {
                    return new class {
                        public function send() {}
                    };
                }
                public function terminate($request, $response) {}
            };
        }
    }
};
