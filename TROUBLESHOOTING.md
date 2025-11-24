# 🔧 Troubleshooting Connection Issues

## Problem: "ERR_CONNECTION_REFUSED" or "localhost refused to connect"

This means the PHP server isn't running. Here's how to fix it:

## ✅ Solution 1: Start the Server Manually

### Option A: Use the Batch File
1. Double-click **`START_SERVER.bat`** in the project folder
2. A window will open showing the server running
3. **Keep this window open!** (Don't close it)
4. Open browser to: http://localhost:8000/installer

### Option B: Use PowerShell/Command Prompt
```powershell
cd C:\Users\leigh\Desktop\TorrentBits-master
C:\xampp\php\php.exe -S localhost:8000 -t public
```

**Important:** Keep the terminal window open while using the site!

## ✅ Solution 2: Check if Port is Already in Use

If port 8000 is busy, use a different port:

```powershell
C:\xampp\php\php.exe -S localhost:8080 -t public
```

Then access: http://localhost:8080/installer

## ✅ Solution 3: Verify PHP is Working

Test PHP:
```powershell
C:\xampp\php\php.exe -v
```

Should show: `PHP 8.2.x`

## ✅ Solution 4: Check Firewall

Windows Firewall might be blocking:
1. Open Windows Defender Firewall
2. Allow PHP through firewall if prompted
3. Or temporarily disable firewall for testing

## ✅ Solution 5: Use XAMPP Apache Instead

If PHP built-in server doesn't work:

1. **Start XAMPP Control Panel**
2. **Start Apache**
3. **Configure Apache:**
   - Edit `C:\xampp\apache\conf\httpd.conf`
   - Find `DocumentRoot` and change to:
     ```
     DocumentRoot "C:/Users/leigh/Desktop/TorrentBits-master/public"
     <Directory "C:/Users/leigh/Desktop/TorrentBits-master/public">
     ```
   - Restart Apache
4. **Access:** http://localhost/installer

## ✅ Solution 6: Check Directory

Make sure you're in the right directory:
```powershell
cd C:\Users\leigh\Desktop\TorrentBits-master
dir public\index.php
```

Should show the file exists.

## 🔍 Common Issues

### "Port already in use"
- Another application is using port 8000
- Use port 8080 instead: `php -S localhost:8080 -t public`

### "PHP not found"
- PHP path might be different
- Find PHP: `dir C:\xampp\php\php.exe`
- Or install PHP properly

### "Server starts then stops"
- Check for errors in the terminal
- Make sure `public/index.php` exists
- Check PHP error logs

### "Can't access from another device"
- Use: `php -S 0.0.0.0:8000 -t public`
- This allows connections from network

## ✅ Quick Fix Checklist

- [ ] Server command is running (terminal window open)
- [ ] Using correct URL: http://localhost:8000
- [ ] Port 8000 is not blocked by firewall
- [ ] PHP is installed and working
- [ ] `public/index.php` file exists
- [ ] No other application using port 8000

## 🚀 Recommended: Use Docker

If you have Docker Desktop, this is easier:

```powershell
docker-compose up -d
```

Then access: http://localhost:8000

No need to manage PHP server manually!

## 📞 Still Having Issues?

1. Check the terminal window for error messages
2. Verify `public/index.php` exists
3. Try a different port (8080, 3000, etc.)
4. Check Windows Firewall settings
5. Make sure XAMPP MySQL is running (for database)

