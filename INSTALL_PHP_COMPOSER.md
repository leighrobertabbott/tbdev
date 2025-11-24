# Installing PHP and Composer on Windows

## Option 1: Using XAMPP (Easiest - Recommended)

XAMPP includes PHP, MySQL, and Apache all in one package.

### Steps:

1. **Download XAMPP**
   - Go to: https://www.apachefriends.org/download.html
   - Download XAMPP for Windows (PHP 8.2+ version)
   - Run the installer

2. **Add PHP to PATH**
   - XAMPP usually installs to: `C:\xampp\php`
   - Add to PATH:
     - Open "Environment Variables" (search in Start menu)
     - Edit "Path" variable
     - Add: `C:\xampp\php`
     - Click OK

3. **Verify PHP**
   ```powershell
   php -v
   ```

4. **Install Composer**
   - Download: https://getcomposer.org/Composer-Setup.exe
   - Run installer (it will detect PHP automatically)
   - Or use manual method below

## Option 2: Manual PHP Installation

1. **Download PHP**
   - Go to: https://windows.php.net/download/
   - Download PHP 8.2+ Thread Safe ZIP
   - Extract to: `C:\php`

2. **Add to PATH**
   - Add `C:\php` to your PATH environment variable

3. **Enable Extensions**
   - Copy `php.ini-development` to `php.ini`
   - Uncomment these lines in `php.ini`:
     ```
     extension=pdo_mysql
     extension=mbstring
     extension=openssl
     extension=json
     ```

4. **Verify**
   ```powershell
   php -v
   php -m  # Should show pdo_mysql, mbstring, etc.
   ```

## Installing Composer

### Method 1: Windows Installer (Easiest)
1. Download: https://getcomposer.org/Composer-Setup.exe
2. Run installer
3. It will detect PHP automatically

### Method 2: Manual Installation
```powershell
# Download composer.phar
Invoke-WebRequest -Uri https://getcomposer.org/composer-stable.phar -OutFile composer.phar

# Use it directly
php composer.phar install
```

Or add to PATH:
```powershell
# Create composer.bat
@echo off
php "%~dp0composer.phar" %*
```

## Quick Verification

After installation, restart PowerShell and run:

```powershell
php -v          # Should show PHP 8.2+
composer -V     # Should show Composer version
php -m          # Should list extensions including pdo_mysql
```

## Alternative: Use Docker

If you have Docker Desktop installed, you can skip PHP/MySQL installation:

```powershell
docker-compose up -d
```

This will run everything in containers!

