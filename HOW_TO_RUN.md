# 🚀 How to Run TorrentBits 2025

Complete step-by-step guide to get your tracker running.

## 📋 Prerequisites

Before starting, make sure you have:

- **PHP 8.2+** - [Download PHP](https://windows.php.net/download/)
- **MySQL 8.0+** - [Download MySQL](https://dev.mysql.com/downloads/mysql/) or use XAMPP/WAMP
- **Composer** - [Download Composer](https://getcomposer.org/download/)
- **Node.js 20+** - [Download Node.js](https://nodejs.org/)
- **Git** (optional) - For cloning

### Quick Check

Open PowerShell and verify:

```powershell
php -v          # Should show PHP 8.2 or higher
mysql --version # Should show MySQL 8.0 or higher
composer -V     # Should show Composer version
node -v         # Should show Node.js 20 or higher
npm -v          # Should show npm version
```

## 🎯 Method 1: Using the Installer Wizard (Easiest - Recommended)

### Step 1: Install Dependencies

Open PowerShell in the project directory:

```powershell
# Install PHP dependencies
composer install

# Install frontend dependencies
npm install

# Build frontend assets
npm run build
```

### Step 2: Start PHP Development Server

```powershell
# Start PHP built-in server
php -S localhost:8000 -t public
```

**Keep this terminal open!** The server is now running.

### Step 3: Run the Installer

1. Open your web browser
2. Go to: `http://localhost:8000/installer`
3. Follow the 4-step wizard:
   - **Step 1**: System requirements check
   - **Step 2**: Database configuration
   - **Step 3**: Application settings
   - **Step 4**: Create admin account

### Step 4: Access Your Site

After installation:
- Homepage: `http://localhost:8000`
- Login: `http://localhost:8000/login`
- Admin Panel: `http://localhost:8000/admin`

## 🐳 Method 2: Using Docker (Alternative)

If you have Docker installed:

```powershell
# Copy environment file
Copy-Item env.example .env

# Edit .env file with your settings (optional for first run)
# Then start Docker containers
docker-compose up -d

# Access at http://localhost:8000
# Run installer at http://localhost:8000/installer
```

## 🔧 Method 3: Manual Setup (Advanced)

### Step 1: Install Dependencies

```powershell
composer install
npm install
npm run build
```

### Step 2: Create Database

```powershell
# Connect to MySQL
mysql -u root -p

# In MySQL prompt:
CREATE DATABASE TBDev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Step 3: Configure Environment

```powershell
# Copy example file
Copy-Item env.example .env

# Edit .env file (use Notepad or your editor)
notepad .env
```

Update these values in `.env`:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=TBDev
DB_USER=root
DB_PASS=your_password

APP_URL=http://localhost:8000
APP_NAME=TorrentBits

JWT_SECRET=generate_with: php -r "echo bin2hex(random_bytes(32));"
```

### Step 4: Import Database Schema

```powershell
mysql -u root -p TBDev < SQL/tb.sql
mysql -u root -p TBDev < SQL/notifications.sql
mysql -u root -p TBDev < SQL/polls.sql
```

### Step 5: Set Permissions (if on Linux/WSL)

```bash
chmod -R 755 torrents/
chmod -R 755 cache/
chmod -R 755 logs/
```

### Step 6: Start Server

```powershell
php -S localhost:8000 -t public
```

### Step 7: Create Admin Account

1. Go to `http://localhost:8000/signup`
2. Register - first user becomes admin automatically
3. Or use the installer at `http://localhost:8000/installer`

## 💻 Development Mode

For development with hot reload:

### Terminal 1: Frontend (Hot Reload)
```powershell
npm run dev
```

### Terminal 2: Backend (PHP Server)
```powershell
php -S localhost:8000 -t public
```

Now changes to CSS/JS will reload automatically!

## 🌐 Production Deployment

### Using Apache

1. Point Apache document root to `public/` directory
2. Enable mod_rewrite
3. Set `APP_ENV=production` in `.env`
4. Set `APP_DEBUG=false` in `.env`

### Using Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/torrentbits/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

## 🔍 Troubleshooting

### "Composer not found"
- Install Composer: https://getcomposer.org/download/
- Or use: `php composer.phar install`

### "npm not found"
- Install Node.js: https://nodejs.org/
- This includes npm automatically

### "Database connection failed"
- Check MySQL is running
- Verify credentials in `.env`
- Test connection: `mysql -u root -p`

### "404 errors"
- Make sure you're accessing `http://localhost:8000` (not root directory)
- Check `public/index.php` exists
- Verify PHP server is running

### "Permission denied" (Linux/WSL)
```bash
chmod -R 755 torrents/ cache/ logs/
```

### "Class not found" errors
```powershell
# Regenerate autoloader
composer dump-autoload
```

### Port 8000 already in use
```powershell
# Use different port
php -S localhost:8080 -t public
# Then access at http://localhost:8080
```

## ✅ Quick Start Checklist

- [ ] PHP 8.2+ installed
- [ ] MySQL running
- [ ] Composer installed
- [ ] Node.js installed
- [ ] Dependencies installed (`composer install` + `npm install`)
- [ ] Frontend built (`npm run build`)
- [ ] Server started (`php -S localhost:8000 -t public`)
- [ ] Installer completed (`http://localhost:8000/installer`)
- [ ] Can log in with admin account

## 🎉 You're Ready!

Once the installer completes, you can:
- Browse torrents
- Upload torrents
- Manage users (admin panel)
- Configure settings
- Use the API

**Default Admin Access:**
- URL: `http://localhost:8000/admin`
- Use the account you created in the installer

## 📚 Next Steps

- Read `README.md` for full documentation
- Check `API.md` for API usage
- Review `INSTALLER.md` for installer details
- Customize in `views/` and `src/`

Happy tracking! 🚀

