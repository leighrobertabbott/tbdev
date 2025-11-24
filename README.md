# TorrentBits 2025

A modernized BitTorrent tracker application, rebuilt from the ground up for 2025. This is a complete modernization of the classic mid-2000s era TorrentBits (TBDev) tracker, designed to be a simple, functional BitTorrent tracker.

## ⚠️ Alpha Status

**This is an extremely alpha implementation.** This project is still in active development and should be considered experimental. Bugs, omissions, and incomplete features are to be expected. Use at your own risk.

## Overview

TorrentBits 2025 is a complete rewrite and modernization of the classic TBDev tracker system. It maintains the core functionality of a simple BitTorrent tracker while bringing the codebase into the modern era with:

- PHP 8.2+ with PSR-4 autoloading and namespaces
- Modern MVC architecture
- RESTful API with JWT authentication
- Modern frontend built with Tailwind CSS and Alpine.js
- Secure by default (PDO, CSRF protection, XSS prevention)
- Docker support for easy deployment
- Environment-based configuration

## Features

### Core Functionality
- BitTorrent tracker (announce/scrape endpoints)
- Torrent upload and management
- User authentication and authorization
- User profiles and reputation system
- Private messaging system
- Forums with hierarchical structure
- Comments and ratings
- Advanced search functionality
- Poll system
- News system
- Admin panel

### Modern Features
- RESTful API with JWT authentication
- Recommendation system
- Achievement system
- Activity logging
- Two-factor authentication
- Queue system for background jobs
- Caching system
- Rate limiting
- Email notifications

## Requirements

- PHP 8.2 or higher
- MySQL 8.0 or higher (or MariaDB 10.3+)
- Composer (PHP dependency manager)
- Node.js 20+ and npm (for frontend assets)
- Web server (Apache/Nginx) or PHP built-in server
- (Optional) Docker and Docker Compose

### PHP Extensions
- PDO
- PDO_MySQL
- mbstring
- OpenSSL
- JSON
- GD (for image processing)
- cURL (for external API calls)

## Installation

### Quick Start (Web Installer)

The easiest way to install TorrentBits is using the built-in web installer:

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/torrentbits-2025.git
   cd torrentbits-2025
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install frontend dependencies**
   ```bash
   npm install
   ```

4. **Build frontend assets**
   ```bash
   npm run build
   ```

5. **Set up web server**
   - Point your web server document root to the `public/` directory
   - Or use PHP's built-in server:
     ```bash
     php -S localhost:8000 -t public
     ```

6. **Run the installer**
   - Navigate to `http://localhost:8000/installer` in your browser
   - Follow the step-by-step installation wizard:
     - Step 1: System Requirements Check
     - Step 2: Database Configuration
     - Step 3: Application Settings
     - Step 4: Create Admin Account

### Manual Installation

If you prefer to install manually:

1. **Clone and install dependencies** (same as above)

2. **Create environment file**
   ```bash
   cp env.example .env
   ```

3. **Configure database**
   Edit `.env` and set your database credentials:
   ```env
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=tbdev
   DB_USER=root
   DB_PASS=your_password
   ```

4. **Create database**
   ```sql
   CREATE DATABASE tbdev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. **Import database schema**
   The installer handles this automatically, but you can import manually:
   ```bash
   mysql -u root -p tbdev < SQL/tb.sql
   mysql -u root -p tbdev < SQL/advanced_features.sql
   mysql -u root -p tbdev < SQL/forum_structure.sql
   # ... and other SQL files in SQL/
   ```

6. **Set permissions**
   ```bash
   chmod -R 775 torrents/
   chmod -R 775 cache/
   chmod -R 775 logs/
   ```

7. **Configure application**
   Edit `.env` with your settings:
   ```env
   APP_NAME=TorrentBits
   APP_URL=http://localhost:8000
   APP_ENV=production
   APP_DEBUG=false
   JWT_SECRET=your_random_secret_here
   ```

8. **Create admin user**
   You can use the installer or create manually via SQL.

9. **Create lock file** (prevents re-installation)
   ```bash
   touch .installed
   ```

### Docker Installation

1. **Clone repository**
   ```bash
   git clone https://github.com/yourusername/torrentbits-2025.git
   cd torrentbits-2025
   ```

2. **Configure environment**
   ```bash
   cp env.example .env
   # Edit .env with your settings
   ```

3. **Build and start containers**
   ```bash
   docker-compose up -d
   ```

4. **Install dependencies**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app npm install
   docker-compose exec app npm run build
   ```

5. **Run installer**
   Navigate to `http://localhost:8000/installer` and complete the installation.

## Configuration

### Environment Variables

Key configuration options in `.env`:

```env
# Database
DB_HOST=localhost
DB_PORT=3306
DB_NAME=tbdev
DB_USER=root
DB_PASS=

# Application
APP_NAME=TorrentBits
APP_URL=http://localhost:8000
APP_ENV=production
APP_DEBUG=false

# Security
JWT_SECRET=your_random_secret_key_here

# Email (SMTP)
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USER=
MAIL_PASS=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME=TorrentBits

# Uploads
MAX_TORRENT_SIZE=1048576
TORRENT_DIR=./torrents
```

### Site Customization

After installation, you can customize your site through the admin panel:
- Site name, tagline, description
- Logo and favicon
- Footer text
- Social media links
- Meta tags

## Usage

### Starting the Server

**Development (PHP built-in server):**
```bash
php -S localhost:8000 -t public
```

**Production:**
Configure your web server (Apache/Nginx) to point to the `public/` directory.

### Tracker URL

After installation, your tracker announce URL will be:
```
http://your-domain.com/announce
```

Share this URL with users for torrent creation.

### Admin Panel

Access the admin panel at `/admin` (requires admin privileges):
- User management
- Torrent management
- News management
- Category management
- Forum management
- Site settings
- Statistics and analytics

## Development

### Project Structure

```
torrentbits-2025/
├── public/          # Web root (document root)
│   ├── index.php   # Entry point
│   ├── css/        # Compiled CSS
│   ├── js/         # Compiled JavaScript
│   └── torrents/   # Uploaded torrent files
├── src/            # Application source code
│   ├── Controllers/ # MVC Controllers
│   ├── Models/     # Data models
│   ├── Services/   # Business logic services
│   └── Core/       # Core framework classes
├── views/          # View templates
├── routes/         # Route definitions
├── SQL/            # Database schemas
├── scripts/        # Utility scripts
└── resources/      # Source assets (CSS, JS)
```

### Building Assets

```bash
# Development (watch mode)
npm run dev

# Production build
npm run build
```

### Code Style

- PSR-4 autoloading
- PSR-12 coding standards
- Namespaced classes
- Type hints where applicable

## API

TorrentBits includes a RESTful API with JWT authentication. API documentation is available at `/api/docs` (when implemented).

### Authentication

```bash
POST /api/auth/login
{
  "username": "user",
  "password": "pass"
}

# Returns JWT token
```

### Example API Call

```bash
curl -H "Authorization: Bearer YOUR_JWT_TOKEN" \
     http://localhost:8000/api/torrents
```

## Troubleshooting

### Common Issues

**"CSRF token mismatch"**
- Ensure sessions are working correctly
- Check that cookies are enabled
- Verify session directory is writable

**"Database connection failed"**
- Verify database credentials in `.env`
- Check MySQL is running
- Ensure database exists

**"Permission denied" errors**
- Check directory permissions for `torrents/`, `cache/`, `logs/`
- Ensure web server user has write access

**"Class not found" errors**
- Run `composer dump-autoload`
- Clear cache: `rm -rf cache/*`

### Getting Help

Since this is an alpha release, expect bugs and incomplete features. For issues:
1. Check existing GitHub issues
2. Create a new issue with detailed information
3. Include PHP version, error messages, and steps to reproduce

## Contributing

Contributions are welcome! However, please note this is an alpha release and the codebase is still evolving.

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is open source. Please check the LICENSE file for details.

## Acknowledgments

- Original TBDev/TorrentBits developers
- Modern PHP community
- All contributors and testers

## Disclaimer

This software is provided "as is" without warranty of any kind. As an alpha release, it should not be used in production environments without thorough testing. The developers are not responsible for any data loss or security issues.

---

**Remember: This is an extremely alpha implementation. Bugs and omissions are expected. Use at your own risk.**
