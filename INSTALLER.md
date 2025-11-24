# Installation Wizard Guide

TorrentBits 2025 includes a web-based installation wizard that makes setup quick and easy.

## Accessing the Installer

1. Navigate to `/installer` in your web browser
2. The installer will guide you through the setup process

## Installation Steps

### Step 1: System Requirements
The installer checks:
- PHP version (8.2+)
- Required PHP extensions (PDO, MySQL, mbstring, OpenSSL, JSON)
- Directory permissions (torrents/, cache/, logs/)
- Creates required directories if they don't exist

### Step 2: Database Configuration
- Enter MySQL database credentials
- The installer will:
  - Test the database connection
  - Create the database if it doesn't exist
  - Import the schema automatically

### Step 3: Application Configuration
- Site name and URL
- Environment (production/development)
- Debug mode
- SMTP email settings (optional)

### Step 4: Admin Account
- Create your first administrator account
- This account will have full Sysop privileges

## After Installation

Once installation is complete:
- A `.installed` lock file is created
- The installer is automatically locked
- You can log in with your admin account
- Visit `/admin` to access the admin panel

## Reinstalling

If you need to reinstall:
1. Delete the `.installed` file
2. Delete or rename the `.env` file
3. Visit `/installer` again

## Security Notes

- The installer automatically locks after successful installation
- Never leave the installer accessible in production
- Review your `.env` file permissions after installation
- Consider removing installer routes in production

## Manual Installation

If you prefer manual installation, see `QUICKSTART.md` for step-by-step instructions.

## Troubleshooting

**"Already Installed" message:**
- The `.installed` file exists
- Delete it to reinstall

**Database connection fails:**
- Verify MySQL is running
- Check credentials are correct
- Ensure MySQL user has CREATE DATABASE privileges

**Permission errors:**
- Make sure `torrents/`, `cache/`, and `logs/` directories are writable
- On Linux: `chmod -R 755 torrents/ cache/ logs/`

**Schema import fails:**
- Check SQL files exist in `SQL/` directory
- Verify database user has CREATE/INSERT privileges
- Check MySQL error logs

