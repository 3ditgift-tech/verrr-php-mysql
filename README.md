# VERCUL Business Onboarding - PHP/MySQL Version

A complete PHP/MySQL backend implementation of the VERCUL business onboarding application. This is a conversion from the original React/TypeScript/Firebase stack to a traditional LAMP stack architecture.

## Features

- **Application Management**: Submit, track, and manage business applications
- **Admin Dashboard**: Review applications, update statuses, add notes
- **Email Notifications**: Automated email notifications using PHPMailer
- **Settings Management**: Configure frontend settings, email templates, and SMTP
- **Secure Authentication**: Admin password authentication with session management
- **RESTful API**: Clean API endpoints for frontend integration
- **MySQL Database**: Optimized database schema with proper indexing

## Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Email**: PHPMailer
- **Server**: Apache with mod_rewrite

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled
- Composer (for dependencies)

### Step 1: Clone the Repository

```bash
git clone https://github.com/3ditgift-tech/verrr-php-mysql.git
cd verrr-php-mysql
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Configure Database

1. Create a MySQL database:

```sql
CREATE DATABASE vercul_business;
```

2. Import the database schema:

```bash
mysql -u your_username -p vercul_business < database/schema.sql
```

3. Update database credentials in `config/database.php`:

```php
private $host = 'localhost';
private $database = 'vercul_business';
private $username = 'your_username';
private $password = 'your_password';
```

### Step 4: Configure Application

Update `config/config.php` with your settings:

```php
define('BASE_URL', 'http://your-domain.com');
define('ADMIN_EMAIL', 'admin@your-domain.com');
```

### Step 5: Set Permissions

```bash
chmod -R 755 .
chmod -R 777 logs/ # If you create a logs directory
```

### Step 6: Test the Installation

Visit: `http://your-domain.com/api/health`

You should see:
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

## API Endpoints

### Applications

- `POST /api/applications/submit` - Submit a new application
- `GET /api/applications/all` - Get all applications (admin)
- `GET /api/applications/get/{id}` - Get application by ID
- `POST /api/applications/update-status` - Update application status (admin)
- `POST /api/applications/update-notes` - Update application notes (admin)
- `GET /api/applications/stats` - Get dashboard statistics (admin)
- `GET /api/applications/pending-count` - Get pending applications count

### Authentication

- `POST /api/auth/verify` - Verify admin password
- `POST /api/auth/update-password` - Update admin password
- `GET /api/auth/check` - Check authentication status
- `POST /api/auth/logout` - Logout admin

### Settings

- `GET /api/settings/frontend` - Get frontend settings
- `POST /api/settings/frontend` - Save frontend settings
- `GET /api/settings/email-templates` - Get email templates
- `POST /api/settings/email-templates` - Update email template
- `GET /api/settings/smtp` - Get SMTP settings
- `POST /api/settings/smtp` - Save SMTP settings
- `POST /api/settings/test-email` - Send test email

## Default Credentials

**Admin Password**: `1234`

⚠️ **Important**: Change the default password immediately after installation!

## Database Schema

The application uses the following main tables:

- `applications` - Stores application metadata
- `application_files` - Stores uploaded documents (separate for performance)
- `settings` - Frontend configuration settings
- `admin_settings` - Admin authentication
- `email_templates` - Email notification templates
- `smtp_settings` - Email server configuration

## Email Configuration

Configure SMTP settings through the admin panel or directly in the database:

1. Login to admin panel
2. Go to Settings > Email Configuration
3. Enter your SMTP details:
   - Host (e.g., smtp.gmail.com)
   - Port (587 for TLS, 465 for SSL)
   - Username
   - Password
   - Security (TLS/SSL)

## Security Features

- **SQL Injection Protection**: Uses prepared statements with PDO
- **Password Hashing**: Bcrypt password hashing
- **Session Management**: Secure session handling
- **Input Validation**: Server-side validation for all inputs
- **CORS Configuration**: Configurable CORS headers
- **File Protection**: .htaccess rules to protect sensitive files

## Frontend Integration

To integrate with a React frontend:

1. Update API URLs in your frontend to point to this PHP backend
2. Replace Firebase SDK calls with standard HTTP requests (fetch/axios)
3. Update authentication to use session-based authentication
4. Modify file upload to send base64 data or use FormData

### Example API Call (JavaScript)

```javascript
// Submit application
const response = await fetch('http://your-domain.com/api/applications/submit', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(applicationData)
});

const result = await response.json();
```

## Development

### Enable Error Reporting

In `config/config.php`, set:

```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Disable in Production

```php
ini_set('display_errors', 0);
error_reporting(0);
```

## Troubleshooting

### API Returns 404

- Ensure mod_rewrite is enabled: `sudo a2enmod rewrite`
- Check .htaccess file is present
- Verify Apache configuration allows .htaccess overrides

### Database Connection Fails

- Verify database credentials in `config/database.php`
- Ensure MySQL service is running
- Check user has proper permissions

### Emails Not Sending

- Verify SMTP configuration
- Check firewall allows outbound connections on SMTP port
- Test with `api/settings/test-email`
- Check PHP error logs

## File Structure

```
verrr-php-mysql/
├── api/
│   ├── controllers/
│   │   ├── ApplicationController.php
│   │   ├── AuthController.php
│   │   └── SettingsController.php
│   └── index.php
├── config/
│   ├── config.php
│   └── database.php
├── database/
│   └── schema.sql
├── utils/
│   ├── EmailService.php
│   ├── IdGenerator.php
│   ├── Response.php
│   └── Validator.php
├── .htaccess
├── composer.json
└── README.md
```

## Differences from Original Project

### Architecture Changes

1. **Backend**: Firebase → PHP/MySQL
2. **Database**: Firestore → MySQL
3. **Authentication**: Firebase Auth → Session-based
4. **File Storage**: Firebase Storage → Base64 in database
5. **Real-time Updates**: Firestore listeners → Polling or WebSocket (optional)

### API Structure

The API endpoints mirror the original Firebase API methods but use REST conventions.

## Production Deployment

### Security Checklist

- [ ] Change default admin password
- [ ] Disable error display in config.php
- [ ] Set secure session cookies (HTTPS only)
- [ ] Configure firewall rules
- [ ] Regular database backups
- [ ] Use environment variables for sensitive data
- [ ] Enable HTTPS/SSL
- [ ] Set proper file permissions (755 for directories, 644 for files)

### Performance Optimization

- Enable PHP OpCache
- Use MySQL query caching
- Implement API rate limiting
- Add Redis for session storage (optional)
- Use CDN for static assets

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License.

## Support

For issues and questions:
- Open an issue on GitHub
- Email: support@vercul.com

## Acknowledgments

Converted from the original React/TypeScript/Firebase project: [verrr](https://github.com/3ditgift-tech/verrr)
