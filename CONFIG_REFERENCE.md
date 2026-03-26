# ⚙️ Configuration Reference

## Environment Variables (`.env` file)

Located at: `c:\xampp\htdocs\DMS_BOOKING\.env`

```env
# Application Settings
APP_NAME=BusBook
APP_ENV=local
APP_DEBUG=true

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=busbook_db
DB_USERNAME=root
DB_PASSWORD=

# Session Configuration
SESSION_DRIVER=files
SESSION_LIFETIME=120

# Booking Settings
BOOKING_CURRENCY=USD
BOOKING_TAX_RATE=0.10

# Email Configuration (future)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls

# API Keys (future)
STRIPE_PUBLIC_KEY=
STRIPE_SECRET_KEY=
PAYPAL_CLIENT_ID=
PAYPAL_SECRET=
```

---

## Application Configuration (`config/app.php`)

### Global Settings

```php
<?php
return [
    'name'       => 'BusBook - Bus Reservation System',
    'url'        => 'http://localhost/DMS_BOOKING',
    'timezone'   => 'UTC',
    
    'currency'   => 'USD',
    'locale'     => 'en',
    
    // Pagination
    'per_page'   => 15,
    
    // Bus Types & Pricing
    'bus_types'  => [
        'standard'  => ['name' => 'Standard Bus', 'price' => 50],
        'ac'        => ['name' => 'AC Bus', 'price' => 75],
        'sleeper'   => ['name' => 'Sleeper Bus', 'price' => 100],
    ],
    
    // Booking Status
    'booking_status' => [
        'pending'   => 'Pending Payment',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
    ],
];
?>
```

### Bus Type Configuration

Update pricing in `config/app.php`:

```php
'bus_types' => [
    'standard'  => ['name' => 'Standard Bus', 'price' => 50],
    'ac'        => ['name' => 'AC Bus', 'price' => 75],
    'sleeper'   => ['name' => 'Sleeper Bus', 'price' => 100],
],
```

Then update `public/index.php` router handler:

```php
$router->post('/dashboard/book', function() {
    // ... validation ...
    
    $price_map = [
        'standard' => 50,
        'ac'       => 75,
        'sleeper'  => 100,
    ];
    
    $total = $price_map[$_POST['bus_type']] * $_POST['number_of_seats'];
    // ... insert booking ...
});
```

---

## Database Configuration (`config/database.php`)

```php
<?php
return [
    'driver'    => 'mysql',
    'host'      => env('DB_HOST', '127.0.0.1'),
    'port'      => env('DB_PORT', 3306),
    'database'  => env('DB_DATABASE', 'busbook_db'),
    'username'  => env('DB_USERNAME', 'root'),
    'password'  => env('DB_PASSWORD', ''),
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
    'strict'    => true,
];
?>
```

### Connecting to Remote Database

Update `.env`:
```env
DB_HOST=your-remote-host.com
DB_PORT=3306
DB_USERNAME=remote_user
DB_PASSWORD=remote_password
DB_DATABASE=remote_busbook_db
```

### Backup Configuration

Add to `config/app.php`:
```php
'backup' => [
    'enabled'   => true,
    'frequency' => 'daily',
    'path'      => '/backups/',
],
```

---

## Styling Configuration (Tailwind CSS)

### Color Scheme

In `resources/views/layouts/app.blade.php`:

```html
<!-- Primary Colors -->
<div class="bg-blue-600">Primary</div>      <!-- #0066cc -->
<div class="bg-blue-700">Dark Primary</div> <!-- #0052a3 -->

<!-- Semantic Colors -->
<div class="bg-green-500">Success</div>     <!-- #28a745 -->
<div class="bg-red-500">Error</div>         <!-- #dc3545 -->
<div class="bg-yellow-500">Warning</div>    <!-- #ffc107 -->
<div class="bg-blue-500">Info</div>         <!-- #17a2b8 -->
```

### Custom Tailwind Configuration

To customize, add `tailwind.config.js`:

```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: '#0066cc',
        secondary: '#00a2e8',
        success: '#28a745',
        error: '#dc3545',
      },
      fontFamily: {
        sans: ['Segoe UI', 'Roboto', 'sans-serif'],
      },
      spacing: {
        '128': '32rem',
        '144': '36rem',
      },
    },
  },
  plugins: [],
};
```

---

## Session Configuration

### Session Lifetime

In `.env`:
```env
SESSION_LIFETIME=120  # Minutes
```

To change to 24 hours:
```env
SESSION_LIFETIME=1440
```

### Session Storage

Current: File-based (default)

Add to `.env` for database sessions (requires migration):
```env
SESSION_DRIVER=database
```

---

## Security Configuration

### Password Hashing

In `app/Http/Controllers/AuthController.php`:

```php
// Using bcrypt (default)
$password = password_hash($_POST['password'], PASSWORD_BCRYPT, [
    'cost' => 10,
]);
```

Increase security:
```php
'cost' => 12  // Higher = slower = more secure
```

### CORS Headers

To enable cross-origin requests in `public/index.php`:

```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
```

### HTTPS Enforcement

In `public/.htaccess`:

```apache
# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## Logging Configuration

### Enable Query Logging

In `database/init.php`:

```php
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_log('Query: ' . $sql);
    error_log('Time: ' . microtime(true));
}
```

### Log File Location

Logs saved to: `storage/logs/app.log`

Create directory:
```bash
mkdir storage/logs
chmod 755 storage/logs
```

---

## Email Configuration (Future)

### SMTP Setup

Update `.env`:
```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@busbook.com
MAIL_FROM_NAME=BusBook
```

### Enable Notifications

In `app/Http/Controllers/BookingController.php`:

```php
// Add after booking creation
$this->sendConfirmationEmail($user->email, $booking);
```

---

## Payment Gateway Configuration (Future)

### Stripe Integration

Update `.env`:
```env
STRIPE_PUBLIC_KEY=pk_live_your_public_key
STRIPE_SECRET_KEY=sk_live_your_secret_key
```

Add to `config/app.php`:
```php
'stripe' => [
    'public_key'  => env('STRIPE_PUBLIC_KEY'),
    'secret_key'  => env('STRIPE_SECRET_KEY'),
    'currency'    => 'usd',
],
```

### PayPal Integration

Update `.env`:
```env
PAYPAL_CLIENT_ID=your_client_id
PAYPAL_SECRET=your_secret
PAYPAL_MODE=live  # or sandbox
```

---

## Feature Flags

Add to `config/app.php`:

```php
'features' => [
    'registration'      => true,
    'email_verification' => false,
    'social_login'      => false,
    'payment_required'  => false,
    'admin_dashboard'   => false,
    'api_enabled'       => false,
],
```

Use in code:
```php
if (config('features.email_verification')) {
    $this->sendVerificationEmail($user);
}
```

---

## API Configuration (Future)

### Rate Limiting

Add to `config/app.php`:

```php
'api' => [
    'rate_limit' => [
        'enabled'     => false,
        'requests'    => 1000,
        'per_minutes' => 60,
    ],
],
```

### API Versioning

```php
'api' => [
    'version'    => 'v1',
    'prefix'     => '/api/',
    'middleware' => ['api', 'auth:api'],
],
```

---

## Cache Configuration (Future)

### Redis Cache

Update `.env`:
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### File Cache

Update `.env`:
```env
CACHE_DRIVER=file
CACHE_PATH=storage/framework/cache
```

---

## Queue Configuration (Future)

For email sending & notifications:

Update `.env`:
```env
QUEUE_DRIVER=database
QUEUE_CONNECTION=database
```

---

## Environment Examples

### Development (`.env.local`)
```env
APP_DEBUG=true
APP_ENV=local
DB_HOST=127.0.0.1
MAIL_DRIVER=log
```

### Staging (`.env.staging`)
```env
APP_DEBUG=true
APP_ENV=staging
DB_HOST=staging.db.example.com
MAIL_DRIVER=smtp
```

### Production (`.env.production`)
```env
APP_DEBUG=false
APP_ENV=production
DB_HOST=prod.db.example.com
MAIL_DRIVER=smtp
SESSION_LIFETIME=1440
```

---

## Performance Tuning

### Database Optimization

Add indexes to frequently searched fields:

```sql
ALTER TABLE bookings ADD INDEX user_id_idx (user_id);
ALTER TABLE bookings ADD INDEX status_idx (status);
ALTER TABLE bookings ADD INDEX date_idx (journey_date);
```

### Query Optimization

Enable query caching:
```php
define('QUERY_CACHE_SIZE', '64M');
```

### PHP Configuration

Update `php.ini`:
```ini
max_execution_time = 60
memory_limit = 256M
post_max_size = 100M
upload_max_filesize = 100M
```

---

## Debugging

### Xdebug Setup

In `php.ini`:
```ini
zend_extension=xdebug
xdebug.mode=develop,debug
xdebug.start_with_request=yes
xdebug.log=/tmp/xdebug.log
```

### Enable Development Mode

In `.env`:
```env
APP_DEBUG=true
APP_ENV=development
```

View errors in browser and detailed logs.

---

## Configuration Precedence

```
1. .env file (highest priority)
   ↓
2. config/app.php defaults
   ↓
3. Hardcoded values (lowest priority)
```

Override order for database:
```
Environment: DB_HOST from .env
             ↓
Config:      config('database.host')
             ↓
Fallback:    'localhost'
```

---

## Validation Configuration

### Add Custom Validators

In `routes/helpers.php`:

```php
function validate($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        if ($rule === 'required' && empty($data[$field])) {
            $errors[$field] = "$field is required";
        }
        // Add more validation logic
    }
    
    return $errors;
}
```

### Common Rules

```php
[
    'email'      => 'required|email',
    'password'   => 'required|min:8|confirmed',
    'age'        => 'required|numeric|min:18',
    'phone'      => 'required|regex:/^[0-9-+]+$/',
    'date'       => 'required|date|after:today',
]
```

---

## Complete Setup Checklist

Configure in order:

```
□ 1. Copy .env.example to .env
□ 2. Update database credentials
□ 3. Set APP_KEY (if needed)
□ 4. Configure email (if needed)
□ 5. Set up session configuration
□ 6. Enable/disable features
□ 7. Configure security headers
□ 8. Set up logging
□ 9. Initialize database
□ 10. Test application
```

---

## Quick Reference

| Setting | File | Default | Purpose |
|---------|------|---------|---------|
| APP_DEBUG | .env | false | Show errors |
| DB_HOST | .env | localhost | Database host |
| SESSION_LIFETIME | .env | 120 | Session timeout |
| MAIL_DRIVER | .env | smtp | Email provider |
| CACHE_DRIVER | .env | file | Cache backend |

---

**Note**: After making configuration changes, clear any active sessions and restart the server for changes to take effect.
