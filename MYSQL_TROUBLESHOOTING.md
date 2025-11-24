# MySQL Connection Troubleshooting

## Error: Access denied for user

This means MySQL rejected your username/password combination.

## XAMPP Default Settings

For XAMPP, the default MySQL credentials are usually:

- **Username:** `root`
- **Password:** (empty - leave blank)

## Solutions

### Option 1: Use Default XAMPP Credentials

In the installer Step 2, enter:
- **Database Host:** `localhost`
- **Database Port:** `3306`
- **Database Username:** `root`
- **Database Password:** (leave empty)
- **Database Name:** `TBDev` (or any name you want)

### Option 2: Check Your MySQL Password

If you set a password for MySQL:

1. **Open XAMPP Control Panel**
2. **Make sure MySQL is running** (green "Running" status)
3. **Open phpMyAdmin:**
   - Go to: http://localhost/phpmyadmin
   - Try logging in with:
     - Username: `root`
     - Password: (your password or empty)
4. **If you can't log in:**
   - You may need to reset the MySQL root password

### Option 3: Reset MySQL Root Password (XAMPP)

If you forgot your MySQL password:

1. **Stop MySQL** in XAMPP Control Panel
2. **Open Command Prompt as Administrator**
3. **Navigate to XAMPP MySQL:**
   ```powershell
   cd C:\xampp\mysql\bin
   ```
4. **Start MySQL in safe mode:**
   ```powershell
   mysqld --skip-grant-tables
   ```
5. **Open another Command Prompt** and run:
   ```powershell
   cd C:\xampp\mysql\bin
   mysql -u root
   ```
6. **Reset password:**
   ```sql
   USE mysql;
   UPDATE user SET authentication_string=PASSWORD('') WHERE User='root';
   FLUSH PRIVILEGES;
   EXIT;
   ```
7. **Restart MySQL** from XAMPP Control Panel

### Option 4: Create a New MySQL User

If you want to use a different user:

1. **Open phpMyAdmin:** http://localhost/phpmyadmin
2. **Go to "User accounts" tab**
3. **Click "Add user account"**
4. **Create user:**
   - Username: `torrentbits` (or any name)
   - Host: `localhost`
   - Password: (set a password)
   - Privileges: Select "Grant all privileges"
5. **Click "Go"**
6. **Use these credentials in the installer**

## Quick Test

Test your MySQL connection from command line:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -p
```

- If it asks for a password, enter your password (or press Enter if no password)
- If it connects, your credentials are correct
- If it fails, you need to fix the password

## Common Issues

### "Access denied" with correct password
- MySQL might not be running - check XAMPP Control Panel
- User might not have permissions - grant privileges in phpMyAdmin

### "Can't connect to MySQL server"
- MySQL is not running - start it in XAMPP Control Panel
- Wrong port - make sure it's 3306

### "Unknown database"
- This is OK! The installer will create it automatically
- Just make sure the username/password are correct

## Recommended: Use Root with No Password (Development)

For local development with XAMPP:
- Username: `root`
- Password: (leave empty)
- This is the simplest setup

## After Fixing Credentials

1. Go back to installer Step 2
2. Enter the correct credentials
3. Click "Test Connection & Continue"
4. The installer will create the database if it doesn't exist

