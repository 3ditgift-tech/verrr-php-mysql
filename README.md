# VERCUL Business Onboarding - Complete PHP Application

**A full-stack PHP/MySQL business onboarding system with NO React, NO Firebase - pure server-side rendering.**

This is a complete conversion of the original React/TypeScript/Firebase project to a traditional LAMP stack architecture.

## ✨ Features

### Public Features
- **Landing Page** - Modern, professional homepage with features, testimonials, FAQ
- **Application Form** - Multi-step business account application with validation
- **Application Tracking** - Real-time status tracking by application ID
- **Email Notifications** - Automated emails for all status changes

### Admin Features
- **Secure Login** - Password-protected admin panel
- **Dashboard** - Statistics and overview of all applications
- **Application Management** - View, review, and update application statuses
- **Settings Panel** - Configure SMTP, change admin password
- **Email Templates** - Customizable notification templates

## 💻 Tech Stack

- **Frontend**: Pure PHP (server-side rendering, no JavaScript framework)
- **Backend**: PHP 7.4+ with PDO
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Email**: PHPMailer
- **Server**: Apache with mod_rewrite
- **Authentication**: PHP Sessions

## 🚀 Quick Start

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite
- Composer

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/3ditgift-tech/verrr-php-mysql.git
cd verrr-php-mysql

# 2. Install dependencies
composer install

# 3. Create database
mysql -u root -p -e "CREATE DATABASE vercul_business;"

# 4. Import schema
mysql -u root -p vercul_business < database/schema.sql

# 5. Configure database
# Edit config/database.php with your credentials

# 6. Configure base URL
# Edit includes/config.php and set BASE_URL

# 7. Set permissions
chmod -R 755 .

# 8. Enable Apache mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Test Installation

Visit: `http://your-domain.com/public/index.php`

**Default Admin Login:**
- URL: `http://your-domain.com/public/admin/login.php`
- Password: `1234`

⚠️ **Change the default password immediately!**

## 📁 Project Structure

```
verrr-php-mysql/
├── public/                    # Web-accessible files
│   ├── index.php              # Landing page
│   ├── apply.php              # Application form
│   ├── track.php              # Track application
│   ├── admin/                 # Admin panel
│   │   ├── login.php          # Admin login
│   │   ├── dashboard.php      # Admin dashboard
│   │   ├── view.php           # View/edit application
│   │   ├── settings.php       # Admin settings
│   │   └── logout.php         # Logout
│   └── assets/
│       └── css/
│           ├── style.css      # Main styles
│           └── admin.css      # Admin styles
├── templates/                # Reusable templates
│   ├── header.php            # Public header
│   ├── footer.php            # Public footer
│   ├── admin_header.php      # Admin header
│   └── admin_footer.php      # Admin footer
├── includes/                 # Helper files
│   ├── config.php            # App configuration
│   └── functions.php         # Helper functions
├── api/                      # REST API (optional)
│   ├── controllers/          # API controllers
│   └── index.php             # API router
├── config/                   # Configuration
│   ├── config.php            # API config
│   └── database.php          # Database config
├── utils/                    # Utility classes
│   ├── EmailService.php      # Email sender
│   ├── Validator.php         # Input validator
│   ├── IdGenerator.php       # ID generator
│   └── Response.php          # API responses
├── database/
│   └── schema.sql            # Database schema
├── composer.json
├── .htaccess
└── README.md
```

## 🔑 Pages & Features

### Public Pages

| Page | File | Description |
|------|------|-------------|
| **Home** | `public/index.php` | Landing page with features, process, testimonials, FAQ |
| **Apply** | `public/apply.php` | Business application form with validation |
| **Track** | `public/track.php` | Track application status by ID |

### Admin Pages

| Page | File | Description |
|------|------|-------------|
| **Login** | `public/admin/login.php` | Admin authentication |
| **Dashboard** | `public/admin/dashboard.php` | Overview with statistics and application list |
| **View Application** | `public/admin/view.php` | View and manage individual applications |
| **Settings** | `public/admin/settings.php` | Change password, configure SMTP |

### Optional REST API

The project also includes a complete REST API for integration. See INSTALLATION.md for details.

## 📧 Email Configuration

1. Login to admin panel
2. Go to Settings
3. Configure SMTP:
   - **Gmail**: `smtp.gmail.com`, port `587`, TLS
   - **SendGrid**: `smtp.sendgrid.net`, port `587`, TLS
4. Test email configuration

## 🔒 Security Features

- SQL injection protection (prepared statements)
- Password hashing with bcrypt
- Session-based authentication
- Input validation and sanitization
- .htaccess security rules
- Secure password requirements

## ⚙️ Configuration

### Database Connection

Edit `config/database.php`:

```php
private $host = 'localhost';
private $database = 'vercul_business';
private $username = 'your_username';
private $password = 'your_password';
```

### Base URL

Edit `includes/config.php`:

```php
define('BASE_URL', 'http://yourdomain.com/public');
```

## 🐞 Troubleshooting

### 404 Errors
- Ensure mod_rewrite is enabled
- Check .htaccess file exists
- Verify Apache AllowOverride is set to All

### Database Connection Failed
- Verify MySQL is running
- Check credentials in config/database.php
- Ensure database exists and schema is imported

### Emails Not Sending
- Verify SMTP settings in admin panel
- Check firewall allows outbound SMTP port
- Review PHP error logs

## 🚀 Production Deployment

### Security Checklist
- [ ] Change default admin password
- [ ] Disable error display (`display_errors = 0`)
- [ ] Enable HTTPS/SSL
- [ ] Set secure cookie flags
- [ ] Configure firewall
- [ ] Set up database backups
- [ ] Set proper file permissions (755/644)

## 🔄 Migration from React/Firebase

### Key Differences

| Original | New PHP Version |
|----------|----------------|
| React SPA | Server-side PHP rendering |
| Firebase Firestore | MySQL database |
| Firebase Auth | PHP Sessions |
| Cloud Functions | PHP controllers |
| Real-time listeners | Page reloads |
| Client-side routing | Server-side routing |

### Benefits of PHP Version

✅ No JavaScript build process  
✅ Better SEO (server-rendered)  
✅ Full control over hosting  
✅ No vendor lock-in  
✅ Lower costs (no Firebase fees)  
✅ Easier to customize  
✅ Works without JavaScript  

## 📝 License

MIT License

## 👥 Support

For issues and questions:
- Open an issue on GitHub
- Email: support@vercul.com

## 🚀 What's Included

✅ Complete PHP frontend (no React)  
✅ Full MySQL backend  
✅ Admin panel  
✅ Email notifications  
✅ Application tracking  
✅ Status management  
✅ SMTP configuration  
✅ Responsive design  
✅ Security features  
✅ REST API (optional)  
✅ Documentation  

---

**Made with ❤️ - A complete PHP alternative to React/Firebase**
