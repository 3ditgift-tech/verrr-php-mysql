# Installation Guide

## Quick Start

Follow these steps to get the VERCUL Business Onboarding system running on your server.

## Prerequisites

### Required Software

- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher (or MariaDB 10.3+)
- **Apache**: Version 2.4 or higher with mod_rewrite enabled
- **Composer**: For dependency management

### Check PHP Version

```bash
php -v
```

### Check MySQL Version

```bash
mysql --version
```

## Step-by-Step Installation

### 1. Download the Project

```bash
# Clone from GitHub
git clone https://github.com/3ditgift-tech/verrr-php-mysql.git

# Navigate to project directory
cd verrr-php-mysql
```

### 2. Install PHP Dependencies

```bash
composer install
```

This will install PHPMailer and other required packages.

### 3. Create MySQL Database

#### Option A: Using MySQL Command Line

```bash
mysql -u root -p
```

Then run:

```sql
CREATE DATABASE vercul_business CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'vercul_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON vercul_business.* TO 'vercul_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Option B: Using phpMyAdmin

1. Open phpMyAdmin
2. Click "New" to create a database
3. Name it `vercul_business`
4. Set collation to `utf8mb4_unicode_ci`
5. Click "Create"

### 4. Import Database Schema

```bash
mysql -u vercul_user -p vercul_business < database/schema.sql
```

Or import via phpMyAdmin:
1. Select `vercul_business` database
2. Click "Import" tab
3. Choose `database/schema.sql`
4. Click "Go"

### 5. Configure Database Connection

Edit `config/database.php`:

```php
private $host = 'localhost';
private $database = 'vercul_business';
private $username = 'vercul_user';
private $password = 'your_secure_password';
```

### 6. Configure Application Settings

Edit `config/config.php`:

```php
// Your domain
define('BASE_URL', 'http://yourdomain.com');

// Admin email
define('ADMIN_EMAIL', 'admin@yourdomain.com');
```

### 7. Set File Permissions

```bash
# Make all files readable
chmod -R 755 .

# Create logs directory (optional)
mkdir logs
chmod 777 logs
```

### 8. Configure Apache

#### Enable mod_rewrite

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Configure Virtual Host (Recommended)

Create `/etc/apache2/sites-available/vercul.conf`:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/verrr-php-mysql
    
    <Directory /var/www/verrr-php-mysql>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/vercul_error.log
    CustomLog ${APACHE_LOG_DIR}/vercul_access.log combined
</VirtualHost>
```

Enable the site:

```bash
sudo a2ensite vercul.conf
sudo systemctl reload apache2
```

### 9. Test Installation

Visit: `http://yourdomain.com/api/health`

Expected response:
```json
{
  "success": true,
  "message": "API is running",
  "data": {
    "status": "ok",
    "timestamp": 1234567890
  }
}
```

### 10. Configure SMTP (Optional but Recommended)

1. Login to admin (default password: `1234`)
2. Navigate to Settings
3. Configure SMTP settings:
   - **Gmail Example**:
     - Host: `smtp.gmail.com`
     - Port: `587`
     - Security: `TLS`
     - Username: your Gmail address
     - Password: App-specific password
   - **SendGrid Example**:
     - Host: `smtp.sendgrid.net`
     - Port: `587`
     - Security: `TLS`
     - Username: `apikey`
     - Password: Your SendGrid API key

4. Test email configuration

### 11. Change Default Admin Password

⚠️ **IMPORTANT**: Change the default password immediately!

1. Login with default password: `1234`
2. Go to Settings > Admin Password
3. Set a strong password

## Verify Installation

### Test Checklist

- [ ] API health check responds: `/api/health`
- [ ] Can submit application: `POST /api/applications/submit`
- [ ] Can login to admin: `POST /api/auth/verify`
- [ ] Can fetch applications: `GET /api/applications/all`
- [ ] Email notifications work (if SMTP configured)

### Test Submit Application

Using curl:

```bash
curl -X POST http://yourdomain.com/api/applications/submit \
  -H "Content-Type: application/json" \
  -d '{
    "companyName": "Test Company",
    "registrationNumber": "12345",
    "country": "United Kingdom",
    "businessAddress": "123 Test St",
    "city": "London",
    "postalCode": "SW1A 1AA",
    "applicantName": "John Doe",
    "applicantRole": "Director",
    "applicantDob": "1990-01-01",
    "applicantEmail": "john@example.com",
    "applicantPhone": "+44 20 1234 5678"
  }'
```

### Test Admin Login

```bash
curl -X POST http://yourdomain.com/api/auth/verify \
  -H "Content-Type: application/json" \
  -d '{"password": "1234"}'
```

## Troubleshooting

### Problem: 404 Error on API Calls

**Solution**:
```bash
# Enable mod_rewrite
sudo a2enmod rewrite

# Ensure AllowOverride All in Apache config
sudo nano /etc/apache2/apache2.conf

# Find <Directory /var/www/> and change AllowOverride None to:
AllowOverride All

# Restart Apache
sudo systemctl restart apache2
```

### Problem: Database Connection Error

**Solution**:
1. Verify MySQL is running: `sudo systemctl status mysql`
2. Test connection: `mysql -u vercul_user -p vercul_business`
3. Check credentials in `config/database.php`

### Problem: Emails Not Sending

**Solution**:
1. Verify SMTP settings
2. Check firewall: `sudo ufw status`
3. Enable outbound port 587: `sudo ufw allow out 587`
4. Test with: `POST /api/settings/test-email`

### Problem: Permission Denied Errors

**Solution**:
```bash
# Set correct ownership
sudo chown -R www-data:www-data /var/www/verrr-php-mysql

# Set correct permissions
find /var/www/verrr-php-mysql -type d -exec chmod 755 {} \;
find /var/www/verrr-php-mysql -type f -exec chmod 644 {} \;
```

### Problem: PHP Version Too Old

**Solution** (Ubuntu/Debian):
```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php7.4 php7.4-mysql php7.4-mbstring php7.4-xml
```

## Next Steps

1. **Integrate Frontend**: Update your React frontend to use these API endpoints
2. **Configure Email**: Set up SMTP for notifications
3. **Customize**: Modify email templates and frontend settings
4. **Secure**: Enable HTTPS with Let's Encrypt
5. **Monitor**: Set up logging and monitoring

## Production Deployment

For production deployment, see the main README.md file for:
- Security hardening checklist
- Performance optimization tips
- Backup strategies
- Monitoring setup

## Support

If you encounter issues:
1. Check the Troubleshooting section above
2. Review Apache/PHP error logs: `/var/log/apache2/error.log`
3. Open an issue on GitHub
4. Contact: support@vercul.com
