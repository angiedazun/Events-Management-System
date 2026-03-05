# DreamEvents - Professional Event Planning Website

A complete event planning and management system built with PHP, MySQL, CSS, and JavaScript. This professional platform allows clients to book event packages and provides a comprehensive admin panel for full control.

## 🌟 Features

### Client-Side Features
- **Modern Responsive Design** - Beautiful, mobile-friendly interface
- **Event Packages** - Browse and select from various event packages
- **Online Booking System** - Easy-to-use booking form with date/time selection
- **Multiple Payment Options** - Credit card, PayPal, and bank transfer support
- **Gallery** - View past events and celebrations
- **Contact Form** - Get in touch with the team
- **About Page** - Learn about the company and team

### Admin Panel Features
- **Secure Login** - Password-protected admin access
- **Dashboard** - Overview of bookings, revenue, and statistics
- **Booking Management** - View, edit, and manage all bookings
- **Package Management** - Add, edit, and delete event packages
- **Message Management** - View and respond to contact messages
- **Gallery Management** - Upload and manage gallery images
- **Full Database Control** - Complete CRUD operations

## 📋 Requirements

- **Web Server**: Apache (XAMPP recommended)
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Browser**: Modern browser with JavaScript enabled

## 🚀 Installation Instructions

### Step 1: Setup XAMPP
1. Make sure XAMPP is installed and running
2. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Database Setup
1. Open your browser and go to: `http://localhost/events-management/config/install.php`
2. This will automatically:
   - Create the database (`events_management`)
   - Create all required tables
   - Insert sample data
   - Create default admin account

### Step 3: Access the Website
- **Main Website**: `http://localhost/events-management/index.php`
- **Admin Login**: `http://localhost/events-management/admin/login.php`

### Step 4: Admin Login Credentials
```
Username: admin
Password: admin123
```

**IMPORTANT**: Change the default password after first login!

## 📁 Project Structure

```
events-management/
├── admin/                      # Admin panel
│   ├── includes/              # Shared admin components
│   ├── login.php              # Admin login page
│   ├── dashboard.php          # Admin dashboard
│   ├── bookings.php           # Manage bookings
│   └── auth_check.php         # Authentication check
├── api/                       # API endpoints
│   ├── create_booking.php     # Create new booking
│   └── get_packages.php       # Fetch packages
├── assets/                    # Static assets
│   ├── css/                   # Stylesheets
│   │   ├── index.css         # Home page styles
│   │   ├── packages.css      # Packages page styles
│   │   ├── booking.css       # Booking page styles
│   │   ├── gallery.css       # Gallery page styles
│   │   ├── about.css         # About page styles
│   │   ├── contact.css       # Contact page styles
│   │   ├── admin-login.css   # Admin login styles
│   │   └── admin-dashboard.css # Admin panel styles
│   ├── js/                    # JavaScript files
│   │   ├── index.js          # Home page scripts
│   │   ├── packages.js       # Packages page scripts
│   │   ├── booking.js        # Booking page scripts
│   │   ├── gallery.js        # Gallery page scripts
│   │   ├── about.js          # About page scripts
│   │   ├── contact.js        # Contact page scripts
│   │   ├── admin-login.js    # Admin login scripts
│   │   └── admin-dashboard.js # Admin panel scripts
│   └── images/                # Image assets
├── config/                    # Configuration files
│   ├── database.php          # Database connection
│   └── install.php           # Database installer
├── index.php                  # Home page
├── packages.php               # Packages page
├── booking.php                # Booking page
├── gallery.php                # Gallery page
├── about.php                  # About page
├── contact.php                # Contact page
└── README.md                  # This file
```

## 💾 Database Schema

### Tables Created:
1. **admins** - Admin user accounts
2. **packages** - Event packages
3. **bookings** - Customer bookings
4. **gallery** - Gallery images
5. **testimonials** - Customer reviews
6. **contact_messages** - Contact form submissions

## 🎨 Customization

### Colors
Edit CSS variables in any CSS file to change the color scheme:
```css
:root {
    --primary-color: #ff6b9d;
    --secondary-color: #c44569;
    --dark-color: #2c3e50;
}
```

### Database Configuration
Edit `config/database.php` to change database settings:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'events_management');
```

### Company Information
Update contact details in:
- `index.php` (Footer section)
- `contact.php` (Contact information)
- `about.php` (Company details)

## 🔒 Security Features

- **Password Hashing** - Admin passwords are securely hashed
- **SQL Injection Prevention** - All queries use mysqli_real_escape_string
- **Session Management** - Secure session handling for admin panel
- **Input Validation** - Both client-side and server-side validation
- **XSS Protection** - All outputs use htmlspecialchars

## 📱 Responsive Design

The website is fully responsive and works on:
- Desktop computers (1200px+)
- Tablets (768px - 1199px)
- Mobile phones (320px - 767px)

## 🛠️ Features to Expand

- Email notifications for bookings
- Payment gateway integration (Stripe, PayPal API)
- Advanced booking calendar
- Customer dashboard
- Reviews and ratings system
- Blog section
- Multi-language support
- Export bookings to PDF/Excel

## 📧 Support

For questions or issues:
- Email: info@dreamevents.com
- Phone: +1 (234) 567-8900

## 📝 License

This project is created for educational and commercial purposes.

## 👨‍💻 Development

Built with:
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Icons**: Font Awesome 6.4.0
- **Fonts**: Segoe UI, System Fonts

## 🎉 Credits

Created by: **GitHub Copilot**
Version: 1.0.0
Date: January 2026

---

**Enjoy your professional event planning website! 🎊✨**
