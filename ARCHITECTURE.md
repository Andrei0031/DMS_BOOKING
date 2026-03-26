# 🏗️ BusBook - Architecture & Technology Stack

## Technology Stack

### Backend
```
✓ PHP 8.0+              Modern PHP with type hints
✓ MySQL 5.7+            Reliable relational database
✓ Sessions              Server-side session management
✓ Prepared Statements   SQL injection prevention
```

### Frontend
```
✓ HTML5                 Semantic markup
✓ Tailwind CSS          Utility-first CSS framework
✓ FontAwesome 6         Icon library
✓ Vanilla JavaScript    Minimal, lightweight JS
```

### Architecture Pattern
```
✓ MVC                   Model-View-Controller pattern
✓ Laravel-style         Laravel conventions and structure
✓ Blade Templates       PHP template engine-inspired views
✓ Routing               Clean URL routing system
```

---

## Project Structure Breakdown

### 📂 `/app` - Application Logic

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php         # Base controller
│   │   ├── HomeController.php     # Home page
│   │   ├── AuthController.php     # Auth operations
│   │   └── BookingController.php  # Booking CRUD
│   ├── Middleware/                # HTTP middleware
│   └── bootstrap.php              # Framework bootstrap
├── Models/
│   ├── User.php                   # User model
│   ├── Booking.php                # Booking model
│   └── Bus.php                    # Bus model
└── Illuminate/                    # Laravel compatibility layer
```

### 📂 `/resources/views` - User Interface

```
resources/views/
├── layouts/
│   └── app.blade.php              # Master template with navigation
├── auth/
│   ├── register.blade.php         # Registration page
│   └── login.blade.php            # Login page
├── dashboard/
│   ├── bookings.blade.php         # Bookings overview
│   └── create-booking.blade.php   # Booking form
└── home.blade.php                 # Home/landing page
```

### 📂 `/database` - Data Layer

```
database/
├── init.php                       # Auto-initialize database
├── migrations/                    # Schema definitions
└── seeders/                       # Sample data
```

### 📂 `/routes` - Routing

```
routes/
├── web.php                        # Web routes definition
└── helpers.php                    # Helper functions
```

### 📂 `/config` - Configuration

```
config/
├── app.php                        # Application settings
└── database.php                   # Database configuration
```

### 📂 `/public` - Web Root

```
public/
├── index.php                      # Router & entry point
├── .htaccess                      # Apache rewrite rules
├── css/                           # Stylesheets
└── js/                            # JavaScript files
```

---

## MVC Architecture Flow

```
┌─────────────────────────────────────────────────────────┐
│                  User Request                            │
│              (http://localhost/...)                      │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
            ┌──────────────────────┐
            │   Router             │
            │ (public/index.php)   │
            └──────────┬───────────┘
                       │
         ┌─────────────┴─────────────┐
         │     Route Matching         │
         │  (routes/web.php)          │
         └─────────────┬─────────────┘
                       │
         ┌─────────────▼──────────────┐
         │   Controller               │
         │ (app/Http/Controllers)     │
         └─────────────┬──────────────┘
                       │
         ┌─────────────┴──────────────┐
         │                            │
         ▼                            ▼
    ┌─────────┐              ┌──────────────┐
    │  Model  │◄────────────►│  Database    │
    │         │              │ (MySQL)      │
    └────┬────┘              └──────────────┘
         │
         ▼
    ┌──────────────┐
    │  View        │
    │ (Blade       │
    │  Template)   │
    └──────┬───────┘
           │
           ▼
    ┌──────────────────┐
    │  HTML Response   │
    │  (to Browser)    │
    └──────────────────┘
```

---

## Database Relationships

```
┌─────────────┐
│   users     │
├─────────────┤
│ id (PK)     │
│ name        │
│ email       │
│ password    │
│ phone       │
└──────┬──────┘
       │
       │ 1─────────────────M
       │
       ▼
┌──────────────────┐
│   bookings       │
├──────────────────┤
│ id (PK)          │
│ user_id (FK)     │
│ from_location    │
│ to_location      │
│ journey_date     │
│ number_of_seats  │
│ bus_type         │
│ total_price      │
│ status           │
└─┬────────────────┘
  │
  │ N─────────────M
  │
  ▼
┌──────────────────┐
│   buses          │
├──────────────────┤
│ id (PK)          │
│ bus_number       │
│ from_location    │
│ to_location      │
│ journey_time     │
│ journey_date     │
│ total_seats      │
│ available_seats  │
│ price_per_seat   │
│ bus_type         │
└──────────────────┘
```

---

## Request Lifecycle

### 1. Authentication Routes
```
GET  /register  ──► RegisterPage
POST /register  ──► Validate ──► SaveUser ──► RedirectLogin
GET  /login     ──► LoginPage
POST /login     ──► Verify ──► CreateSession ──► RedirectDashboard
POST /logout    ──► DestroySession ──► RedirectHome
```

### 2. Dashboard Routes (Protected)
```
GET  /dashboard      ──► FetchUserBookings ──► ShowDashboard
GET  /dashboard/book ──► ShowBookingForm
POST /dashboard/book ──► ValidateInput ──► SaveBooking ──► RedirectDashboard
```

---

## Tailwind CSS Usage

### Color Variables
```css
--primary-color:   #0066cc
--secondary-color: #00a2e8
--success-color:   #28a745
--error-color:     #dc3545
--text-dark:       #333333
--text-light:      #666666
--bg-light:        #f8f9fa
```

### Common Utility Classes
```
Spacing:   px-4, py-8, mb-6, gap-8, max-w-7xl
Grid:      grid grid-cols-1 md:grid-cols-3 gap-8
Flexbox:   flex justify-between items-center
Typography: font-bold text-lg, text-center
Colors:    bg-blue-600, text-white, border-gray-300
Effects:   rounded-lg, shadow, hover:shadow-lg
Responsive: md:grid-cols-2, lg:grid-cols-3
```

---

## Security Layers

```
┌─────────────────────────────────────────────────────┐
│              Input Layer                             │
│  ├─ Input Validation (required fields)              │
│  ├─ Type Checking (email, date, etc)                │
│  └─ Length Limits (max 255 chars)                   │
└────────────────┬────────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────────┐
│              Processing Layer                        │
│  ├─ htmlspecialchars() (XSS prevention)             │
│  ├─ real_escape_string() (SQL injection)            │
│  ├─ password_hash() (password security)             │
│  └─ password_verify() (secure comparison)           │
└────────────────┬────────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────────┐
│              Database Layer                          │
│  ├─ Prepared Statements  (?parameter binding)       │
│  ├─ Foreign Key Constraints                         │
│  ├─ Indexes on search fields                        │
│  └─ User/role-based access                          │
└────────────────┬────────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────────┐
│              Session Layer                           │
│  ├─ Session Regeneration after login                │
│  ├─ User ID verification on actions                 │
│  ├─ Logout destroys session completely             │
│  └─ Cookie flags (secure, httponly)                 │
└─────────────────────────────────────────────────────┘
```

---

## Performance Optimizations

### Frontend
```
✓ Tailwind CSS CDN       Minimal HTML overhead
✓ FontAwesome CDN        Icon delivery via CDN
✓ Vanilla JavaScript     Lightweight (no frameworks)
✓ CSS compression        Built-in Tailwind minification
✓ Browser caching        Static assets cached
```

### Backend
```
✓ Database indexes       Fast queries
✓ Prepared statements    Prevent SQL injection
✓ Session caching        Reduced database hits
✓ Efficient queries      SELECT only needed fields
✓ Lazy loading           Resources loaded on demand
```

---

## Scalability Features

### Database
```
✓ Foreign keys           Maintain data integrity
✓ Indexes               Fast lookups
✓ Partitioning ready    Support for large datasets
✓ Transaction support   Data consistency
```

### Application
```
✓ Modular structure     Easy to extend
✓ Separation of concerns Clear responsibility
✓ Reusable components   DRY principle
✓ Configuration files   Easy environment switching
```

---

## Deployment Checklist

Before going to production:

```
□ Set APP_DEBUG=false in .env
□ Set APP_ENV=production
□ Enable HTTPS
□ Set strong DB_PASSWORD
□ Configure backups
□ Enable query logging
□ Set up error monitoring
□ Configure email service
□ Optimize database queries
□ Enable caching layer
□ Set up CDN for assets
□ Configure load balancing
```

---

## Development Commands

### Database Setup (Automatic)
```
Simply access: http://localhost/DMS_BOOKING/
```

### Manual Database Reset
```sql
DROP DATABASE busbook_db;
-- Reload page to recreate
```

### Clear Session
```
Browser: Ctrl+Shift+Del (Clear Browsing Data)
```

---

## Testing Accounts

After setup, you can create test accounts:

```
Email:    test@example.com
Password: password123
Name:     Test User
Phone:    +1-800-TEST

Email:    admin@busbook.com
Password: admin12345
Name:     Admin User
Phone:    +1-800-ADMIN
```

---

## File Upload & Storage

Currently: No file uploads
Ready for: 
- Profile pictures
- ID/passport scans
- Payment receipts
- Invoice PDFs

Add storage setup when needed.

---

## API Ready

Current: Server-side rendering
Ready to convert to:
- REST API (JSON responses)
- GraphQL (complex queries)
- WebSocket (real-time updates)

---

## Summary

**BusBook** combines:
- **Modern PHP** architecture
- **Laravel** conventions
- **Tailwind CSS** for beautiful UI
- **MySQL** for reliable data
- **Security** best practices
- **Scalability** in mind

Perfect for learning and production use! 🚌✨
