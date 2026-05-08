<?php

declare(strict_types=1);

define('BASE_PATH', __DIR__);
define('APP_VERSION', '1.0.0');
define('APP_NAME', 'SATRIA WISATA TRANSPORT');

// JWT secret key — ubah nilai ini di production (gunakan nilai acak yang panjang)
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'SATRIA_WISATA_TRANSPORT');

// Autoloader
spl_autoload_register(function (string $class): void {
    // Mapping namespace ke folder
    $namespaceMap = [
        'Domain\\' => '02Domain/',
        'Base\\' => '01Base/',
        'Shared\\' => '03Shared/',
        'Application\\' => '04Application/',
        'Infrastructure\\' => '05Infrastructure/',
        'WebApi\\' => '06WebApi/',
        'Client\\' => '07Client/',
    ];

    $relativePath = null;

    // Cari namespace yang cocok
    foreach ($namespaceMap as $namespace => $folder) {
        if (strpos($class, $namespace) === 0) {
            $relativePath = str_replace('\\', '/', substr($class, strlen($namespace)));
            $fullPath = BASE_PATH . '/' . $folder . $relativePath . '.php';

            if (file_exists($fullPath)) {
                require_once $fullPath;
                return;
            }
        }
    }

    // Fallback: coba langsung dari root dengan namespace sebagai path
    $directPath = BASE_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($directPath)) {
        require_once $directPath;
        return;
    }
});

// Bootstrap
require_once BASE_PATH . '/05Infrastructure/AppDbContext.php';
require_once BASE_PATH . '/05Infrastructure/DatabaseMigration.php';
require_once BASE_PATH . '/05Infrastructure/DependencyInjection.php';

use Infrastructure\AppDbContext;
use Infrastructure\DatabaseMigration;
use Infrastructure\Seeders\DatabaseSeeder;
use Infrastructure\DependencyInjection;

// Run auto migration
$db = AppDbContext::getInstance();
$migration = new DatabaseMigration($db);
$migration->run();

// Run auto seeding
$db = AppDbContext::getInstance();
$seeder = new DatabaseSeeder($db);
$seeder->run();

// Dependency Injection
$container = DependencyInjection::build($db);

// Start session (digunakan untuk flash message saja)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inisialisasi JWT Auth
require_once BASE_PATH . '/01Base/Auth/JwtHelper.php';
require_once BASE_PATH . '/01Base/Auth/Auth.php';
\Base\Auth\Auth::init(JWT_SECRET);

/**
 * Helper function untuk mendapatkan base path aplikasi
 */
function getBasePath(): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $scriptDir = dirname($scriptName);

    // Jika script berada di root, base path adalah root
    if ($scriptDir === '/' || $scriptDir === '\\') {
        return '';
    }

    // Normalisasi base path
    $basePath = rtrim($scriptDir, '/');

    // Cek apakah ada konfigurasi manual dari environment
    if (getenv('APP_BASE_PATH')) {
        $basePath = rtrim(getenv('APP_BASE_PATH'), '/');
    }

    return $basePath;
}

/**
 * Helper function untuk generate URL
 */
function url(string $path = ''): string
{
    $basePath = getBasePath();
    $path = ltrim($path, '/');

    if ($basePath === '') {
        return '/' . $path;
    }

    return $basePath . '/' . $path;
}

// Simple router dengan base path detection
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = getBasePath();

// Parse request URI tanpa base path
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove base path dari URI
if ($basePath !== '' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Normalize path
$path = '/' . ltrim($path, '/');
$path = strtok($path, '?'); // Remove query string
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Route map: [method, path] => [controller, action]
$routes = [
    // Auth
    'GET:/'                           => ['AuthController', 'loginPage'],
    'GET:/login'                      => ['AuthController', 'loginPage'],
    'POST:/login'                     => ['AuthController', 'login'],
    'GET:/logout'                     => ['AuthController', 'logout'],

    // Dashboard
    'GET:/dashboard'                  => ['DashboardController', 'index'],

    // Jabatan
    'GET:/master/jabatan'             => ['JabatanController', 'index'],
    'POST:/master/jabatan/create'     => ['JabatanController', 'create'],
    'POST:/master/jabatan/update'     => ['JabatanController', 'update'],
    'POST:/master/jabatan/delete'     => ['JabatanController', 'delete'],
    'GET:/master/jabatan/get'         => ['JabatanController', 'getById'],

    // Golongan
    'GET:/master/golongan'            => ['GolonganController', 'index'],
    'POST:/master/golongan/create'    => ['GolonganController', 'create'],
    'POST:/master/golongan/update'    => ['GolonganController', 'update'],
    'POST:/master/golongan/delete'    => ['GolonganController', 'delete'],
    'GET:/master/golongan/get'        => ['GolonganController', 'getById'],

    // User
    'GET:/master/user'                => ['UserController', 'index'],
    'POST:/master/user/create'        => ['UserController', 'create'],
    'POST:/master/user/update'        => ['UserController', 'update'],
    'POST:/master/user/delete'        => ['UserController', 'delete'],
    'GET:/master/user/get'            => ['UserController', 'getById'],

    // Absensi
    'GET:/transaksi/absensi'               => ['AbsensiController', 'index'],
    'POST:/transaksi/absensi/create'       => ['AbsensiController', 'create'],
    'POST:/transaksi/absensi/create-bulk'  => ['AbsensiController', 'createBulk'],
    'POST:/transaksi/absensi/update'       => ['AbsensiController', 'update'],
    'POST:/transaksi/absensi/delete'       => ['AbsensiController', 'delete'],
    'GET:/transaksi/absensi/get'           => ['AbsensiController', 'getById'],
    'GET:/transaksi/absensi/status-hari'   => ['AbsensiController', 'statusHari'],

    // Penggajian
    'GET:/transaksi/penggajian'                    => ['PenggajianController', 'index'],
    'POST:/transaksi/penggajian/create'            => ['PenggajianController', 'create'],
    'POST:/transaksi/penggajian/create-bulk'       => ['PenggajianController', 'createBulk'],
    'POST:/transaksi/penggajian/update'            => ['PenggajianController', 'update'],
    'POST:/transaksi/penggajian/delete'            => ['PenggajianController', 'delete'],
    'GET:/transaksi/penggajian/get'                => ['PenggajianController', 'getById'],
    'GET:/transaksi/penggajian/slip'               => ['PenggajianController', 'slipGaji'],
    'GET:/transaksi/penggajian/getUserData'        => ['PenggajianController', 'getUserData'],
    'GET:/transaksi/penggajian/status-periode'     => ['PenggajianController', 'statusPeriode'],

    // Laporan
    'GET:/transaksi/laporan'             => ['LaporanController', 'index'],
    'GET:/transaksi/laporan/export'      => ['LaporanController', 'export'],
    'GET:/transaksi/laporan/absensi'     => ['LaporanController', 'absensi'],

    // Profile
    'GET:/profile'                    => ['ProfileController', 'index'],
    'POST:/profile/update'            => ['ProfileController', 'update'],
];

// Debug mode (set false di production)
define('DEBUG_MODE', true);

// Route matching dengan wildcard support
function matchRoute(string $method, string $path, array $routes): ?array
{
    $routeKey = $method . ':' . $path;

    // Exact match
    if (isset($routes[$routeKey])) {
        return $routes[$routeKey];
    }

    // Wildcard matching (untuk dynamic routes)
    foreach ($routes as $key => $handler) {
        list($routeMethod, $routePath) = explode(':', $key, 2);

        if ($routeMethod !== $method) {
            continue;
        }

        // Convert route pattern ke regex
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $path)) {
            return $handler;
        }
    }

    return null;
}

$routeMatch = matchRoute($method, $path, $routes);

if (!$routeMatch) {
    http_response_code(404);
    require BASE_PATH . '/08Bsui/errors/404.php';
    exit;
}

[$controllerName, $action] = $routeMatch;

// Map controller name to class
$controllerMap = [
    'AuthController'       => \WebApi\AuthController::class,
    'DashboardController'  => \WebApi\DashboardController::class,
    'JabatanController'    => \WebApi\Master\Jabatan\JabatanController::class,
    'GolonganController'   => \WebApi\Master\Golongan\GolonganController::class,
    'UserController'       => \WebApi\Master\User\UserController::class,
    'AbsensiController'    => \WebApi\Transaction\Absensi\AbsensiController::class,
    'PenggajianController' => \WebApi\Transaction\Penggajian\PenggajianController::class,
    'LaporanController'    => \WebApi\Transaction\Laporan\LaporanController::class,
    'ProfileController'    => \WebApi\ProfileController::class,
];

$className = $controllerMap[$controllerName] ?? null;

if (!$className) {
    http_response_code(500);
    require BASE_PATH . '/08Bsui/errors/500.php';
    exit;
}

// Require controller files dynamically
function requireControllerFile(string $className): void
{
    $basePath = BASE_PATH;
    $classPath = str_replace('\\', '/', $className);
    $filePath = $basePath . '/06WebApi/' . preg_replace('/^WebApi\\//', '', $classPath) . '.php';

    if (file_exists($filePath)) {
        require_once $filePath;
    } else {
        // Alternative path
        $altPath = $basePath . '/' . $classPath . '.php';
        if (file_exists($altPath)) {
            require_once $altPath;
        }
    }
}

requireControllerFile($className);

// Create controller instance
if (!class_exists($className)) {
    http_response_code(500);
    require BASE_PATH . '/08Bsui/errors/500.php';
    exit;
}

$controller = $container->make($className);

if (!method_exists($controller, $action)) {
    http_response_code(500);
    require BASE_PATH . '/08Bsui/errors/500.php';
    exit;
}

// Execute controller action
$controller->$action();

// Helper function untuk base URL di views
function base(string $path = ''): string
{
    return url($path);
}

// Helper function untuk redirect
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}
