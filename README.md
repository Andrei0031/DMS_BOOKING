# BusBook - Laravel + Tailwind CSS Bus Transportation Booking System

A modern, fully-featured online bus transportation booking system built with **Laravel-style architecture** and **Tailwind CSS** for a beautiful responsive UI.

## 🚀 Features

### Modern Tech Stack
- **Laravel-inspired Architecture** - MVC pattern with models, controllers, and views
- **Tailwind CSS** - Utility-first CSS framework for responsive design
- **PHP 8+** - Modern PHP features and best practices
- **MySQL** - Reliable database backend
- **XAMPP Compatible** - Works seamlessly with XAMPP

### Core Features
- **User Authentication** - Register and login system with password hashing
- **Bus Booking** - Easy-to-use interface for booking bus tickets
- **Dashboard** - Manage all your bookings in one place
- **Real-time Pricing** - Dynamic price calculation based on bus type and seats
- **Responsive Design** - Works perfectly on desktop, tablet, and mobile

### Booking Options
- **Bus Types**: Standard, AC, Sleeper
- **Dynamic Pricing**:
  - Standard: $50/seat
  - AC: $75/seat
  - Sleeper: $100/seat
- **Up to 10 seats** per booking
- **Future journey dates** only

## 📁 Project Structure

```
DMS_BOOKING/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Application controllers
│   │   └── Middleware/         # HTTP middleware
│   ├── Models/                 # Database models
│   └── bootstrap.php           # Application bootstrap
├── resources/
│   └── views/                  # Blade templates
│       ├── layouts/
│       │   └── app.blade.php   # Master layout with Tailwind
│       ├── auth/               # Authentication views
│       └── dashboard/          # Dashboard views
├── database/
│   ├── migrations/             # Database schema
│   └── init.php                # Database initialization
├── routes/
│   ├── web.php                 # Web routes
│   └── helpers.php             # Helper functions
├── config/                     # Configuration files
├── public/
│   └── index.php               # Application entry point (Router)
├── storage/
│   └── logs/                   # Application logs
└── .env                        # Environment configuration
```

## ⚙️ Installation & Setup

### Prerequisites
- XAMPP (with Apache & MySQL)
- PHP 8.0 or higher
- MySQL 5.7 or higher

### Step 1: Extract Project
Extract the project to your XAMPP directory:
```
c:\xampp\htdocs\DMS_BOOKING\
```

### Step 2: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**

### Step 3: Access the Application
Open your browser and navigate to:
```
http://localhost/DMS_BOOKING/
```

The application will automatically:
- Create the database (`busbook_db`)
- Create all necessary tables
- Initialize the system

### Step 4: Create Your Account
1. Click **Register**
2. Fill in your details
3. Login with your credentials
4. Start booking!

## 🗄️ Database Schema

### Users Table
```
- id: Primary key
- name: Full name
- email: Unique email address
- password: Hashed password
- phone: Contact number (optional)
- timestamps: Created/Updated dates
```

### Bookings Table
```
- id: Primary key
- user_id: Foreign key to users
- from_location: Departure city
- to_location: Destination city
- journey_date: Travel date
- number_of_seats: Quantity of seats
- bus_type: Bus category (standard/ac/sleeper)
- total_price: Booking cost
- status: Booking status (pending/confirmed/cancelled)
- timestamps: Created/Updated dates
```

### Buses Table
```
- id: Primary key
- bus_number: Unique bus identifier
- from_location: Route origin
- to_location: Route destination
- journey_time: Departure time
- journey_date: Travel date
- total_seats: Bus capacity
- available_seats: Available seats
- price_per_seat: Per-seat cost
- bus_type: Bus category
- timestamps: Created/Updated dates
```

## 🎨 UI/UX Features with Tailwind CSS

### Design System
- **Color Scheme**:
  - Primary Blue: `#0066cc`
  - Secondary Blue: `#00a2e8`
  - Success Green: `#28a745`
  - Error Red: `#dc3545`
  - Background: `#f8f9fa`

### Tailwind Components
- **Gradient backgrounds** - Modern linear gradients
- **Responsive grid layouts** - Mobile-first design
- **Interactive cards** - Hover effects and transitions
- **Professional typography** - Excellent readability
- **Icon integration** - FontAwesome icons
- **Smooth animations** - CSS transitions

### Responsive Design
- **Mobile First** - Optimized for all screen sizes
- **Breakpoints**:
  - Mobile: < 640px
  - Tablet: 768px - 1024px
  - Desktop: > 1024px

## 🔐 Security Features

- **Password Hashing** - Using PHP's `password_hash()` function
- **SQL Prepared Statements** - Protection against SQL injection
- **Session Management** - Secure session handling
- **Input Validation** - XSS prevention with `htmlspecialchars()`
- **CSRF Protection** - Secure form handling

## 📱 User Workflows

### Registration
1. Navigate to `/register`
2. Enter full name, email, password, and optionally phone
3. Confirm password matches
4. Submit to create account
5. Redirected to login page

### Login
1. Navigate to `/login`
2. Enter email and password
3. System verifies credentials
4. Redirected to dashboard on success

### Booking
1. Click "Book New Ticket" in dashboard
2. Fill journey details (from, to, date, seats, type)
3. Real-time price calculation displayed
4. Confirm booking
5. Booking appears in dashboard table

### Management
1. Dashboard shows all your bookings
2. View booking details: route, date, price, status
3. Cancel pending bookings anytime
4. Confirmed/cancelled bookings cannot be modified

## 🛠️ Configuration

Edit `.env` file to customize:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=busbook_db
DB_USERNAME=root
DB_PASSWORD=

APP_NAME=BusBook
APP_ENV=local
APP_URL=http://localhost/DMS_BOOKING
```

## 🚦 Routes

### Public Routes
```
GET  /                    # Home/Landing page
GET  /register            # Registration form
POST /register            # Process registration
GET  /login               # Login form
POST /login               # Process login
```

### Authenticated Routes
```
POST /logout              # Logout and destroy session
GET  /dashboard           # User dashboard with bookings
GET  /dashboard/book      # Booking creation form
POST /dashboard/book      # Create new booking
```

## 💰 Pricing Logic

```php
Bus Types and Pricing:
- Standard:  $50 per seat
- AC Bus:    $75 per seat
- Sleeper:  $100 per seat

Calculation:
Total Cost = (Bus Type Price) × (Number of Seats)
```

**Example**:
- Route: New York → Boston
- Seats: 3
- Type: AC Bus
- Calculation: $75 × 3 = **$225**

## 🎯 Technology Stack

### Backend
- PHP 8+ with modern syntax
- MVC Architecture
- Session-based authentication
- MySQL with prepared statements

### Frontend
- HTML5
- Tailwind CSS (via CDN)
- FontAwesome 6 icons
- Vanilla JavaScript (minimal, for interactivity)

### Database
- MySQL 5.7+
- InnoDB engine
- Foreign key relationships
- Timestamps for audit trail

## 📊 Code Organization

### Controllers
- `HomeController` - Landing page
- `AuthController` - Registration, login, logout
- `BookingController` - CRUD operations for bookings

### Models
- `User` - User data and relationships
- `Booking` - Booking data and relationships
- `Bus` - Bus data and relationships

### Views
- `layouts/app.blade.php` - Master template
- `home.blade.php` - Home/landing page
- `auth/register.blade.php` - Registration page
- `auth/login.blade.php` - Login page
- `dashboard/bookings.blade.php` - Dashboard
- `dashboard/create-booking.blade.php` - Booking form

## 🐛 Troubleshooting

### Database Connection Failed
**Solution:**
- Start MySQL in XAMPP Control Panel
- Verify credentials in `.env`
- Check if port 3306 is available

### Session Not Persisting
**Solution:**
- Clear browser cookies manually
- Check `php.ini` session settings
- Ensure `/storage/` directory is writable

### Styles Not Loading
**Solution:**
- Clear browser cache (Ctrl+Shift+Del)
- Check internet connection for CDN
- Verify no ad blockers interfering with CDN

### Routes Not Working
**Solution:**
- Verify Apache rewrite module is enabled
- Check `.htaccess` is present in public folder
- Ensure correct base URL in browser

## 🚀 Performance Tips

- **Enable Caching** - Database query caching
- **Compress Assets** - Minify CSS/JS
- **Lazy Load Images** - FontAwesome icons
- **Use CDN** - Content delivery network

## 📚 Learning Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [PHP Best Practices](https://phptherightway.com/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## 🔄 Maintenance

### Regular Updates
- Keep PHP updated to latest version
- Update MySQL regularly
- Monitor error logs in `storage/logs/`

### Database Backup
Before major updates, backup:
```
c:\xampp\htdocs\DMS_BOOKING\database\
```

### Clear Cache
```
1. Delete session files
2. Clear browser cache
3. Restart Apache/MySQL
```

## 🎓 Future Enhancements

- [ ] Payment gateway (Stripe/PayPal)
- [ ] Email notifications
- [ ] SMS alerts
- [ ] Admin panel
- [ ] Advanced search filters
- [ ] Seat selection UI
- [ ] Multi-language support
- [ ] Dark mode theme
- [ ] REST API endpoints
- [ ] Mobile app integration

## 📄 License

© 2026 BusBook. All rights reserved.

This project is licensed under the MIT License.

## 👥 Support & Contact

**Email:** support@busbook.com  
**Website:** busbook.com  

---

**Happy Traveling! 🚌✨**

Built with passion using Laravel conventions and Tailwind CSS
