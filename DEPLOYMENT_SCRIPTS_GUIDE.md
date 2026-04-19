# Deployment Scripts Guide

## Overview

Two deployment scripts are provided to automate the setup and optimization process:

- **`deploy.sh`** - For Linux/macOS/Unix systems
- **`deploy.bat`** - For Windows systems

Both scripts perform the following tasks:
1. Install PHP dependencies (via Composer)
2. Install Node dependencies (via npm)
3. Build frontend assets (npm build)
4. Generate/verify application key
5. Clear application caches
6. Optimize Laravel application

---

## Windows Users

### Using `deploy.bat`

1. **Open Command Prompt or PowerShell**
   - Navigate to your project directory
   - Or right-click → "Open in Terminal" (in your project folder)

2. **Run the script**
   ```bash
   deploy.bat
   ```

3. **Wait for completion**
   - The script will display colored status messages
   - It will pause at the end to show results

### Troubleshooting on Windows

**"deploy.bat is not recognized"**
- Make sure you're in the project root directory
- Try: `.\deploy.bat`

**"php is not recognized"**
- PHP is not in your PATH
- Add PHP to Windows PATH or use full path: `C:\php\php.exe`

**"composer is not recognized"**
- Composer is not installed or not in PATH
- Install from: https://getcomposer.org/download/

**"npm is not recognized"**
- Node.js is not installed or not in PATH
- Install from: https://nodejs.org/
- The script will skip npm steps if not available

---

## Linux/macOS Users

### Using `deploy.sh`

1. **Open Terminal**
   - Navigate to your project directory

2. **Make the script executable** (first time only)
   ```bash
   chmod +x deploy.sh
   ```

3. **Run the script**
   ```bash
   ./deploy.sh
   ```

4. **Wait for completion**
   - The script will display colored status messages

### Troubleshooting on Linux/macOS

**"Permission denied"**
```bash
chmod +x deploy.sh
```

**"php command not found"**
- Install PHP or ensure it's in your PATH
- Check: `which php`

**"composer command not found"**
- Install Composer from: https://getcomposer.org/download/

**"npm command not found"**
- Install Node.js from: https://nodejs.org/
- The script will skip npm steps if not available

---

## What Each Script Does

### Step 1: Pre-flight Checks
- Verifies PHP, Composer, and optionally Node/npm are installed
- Checks for required `composer.json` file
- Reports versions of installed tools

### Step 2: Install PHP Dependencies
- Runs `composer install --prefer-dist --optimize-autoloader`
- Downloads and installs all PHP packages
- Generates autoloader

### Step 3: Install Node Dependencies
- Runs `npm install`
- Downloads and installs all JavaScript packages
- Skipped if Node/npm not available

### Step 4: Build Frontend Assets
- Runs `npm run build`
- Compiles CSS, JavaScript, and other assets
- Skipped if no build script in package.json

### Step 5: Generate Application Key
- Runs `php artisan key:generate --force`
- Creates `APP_KEY` in .env if missing
- Skipped if already set

### Step 6: Clear Caches
- Clears all application caches
- Removes old cached configurations and routes

### Step 7: Optimize Application
- Runs `php artisan config:cache`
- Runs `php artisan route:cache`
- Runs `php artisan view:cache`
- Runs `php artisan optimize`

### Step 8: Set Permissions (Linux/macOS only)
- Sets proper permissions on `storage/` directory
- Sets proper permissions on `bootstrap/cache/` directory
- Skipped on Windows

---

## After Running the Script

Once the script completes successfully:

1. **Configure `.env` file** (if not already done)
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_user
   DB_PASSWORD=your_password
   ```

2. **Run database migrations**
   ```bash
   php artisan migrate
   ```

3. **Optional: Seed the database**
   ```bash
   php artisan db:seed
   ```

4. **Start the development server**
   ```bash
   php artisan serve
   ```

5. **Or deploy to production**
   - Copy your project to your VPS/server
   - Run the script again
   - Configure your web server (Nginx/Apache)

---

## What Gets Ignored

The `.gitignore` file ensures these are NOT committed to git:
- `/vendor` - PHP dependencies
- `/node_modules` - JavaScript dependencies
- `/storage/logs/*` - Application logs
- `/storage/framework/cache/*` - Cache files
- `.env` - Environment configuration
- `*.log` - Log files
- `/public/hot` - Vite dev server file

---

## Manual Alternative

If you prefer to run commands manually instead of using the scripts:

```bash
# Install dependencies
composer install --prefer-dist --optimize-autoloader
npm install

# Build assets
npm run build

# Generate key
php artisan key:generate --force

# Clear and optimize caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## Environment Variables

The scripts expect these files to exist or be created:

- **`.env`** - Application configuration
  - Copy from `.env.example` if doesn't exist
  - Set your database credentials here

- **`composer.json`** - PHP dependencies (should already exist)
- **`package.json`** - JavaScript dependencies (should already exist)

---

## Performance Notes

- **First Run**: Takes longer (downloads all dependencies)
- **Subsequent Runs**: Much faster (only updates changed packages)
- **Build Step**: Compiles frontend assets (only if changes detected)
- **Optimization**: Cache building improves production performance by ~30-50%

---

## Success Indicators

The script is successful when you see:
- ✓ All steps show GREEN [OK] status
- ✓ No RED [ERROR] messages
- ✓ Final "Deployment Complete!" message
- ✓ "Happy coding!" message at the end

---

## Getting Help

If something goes wrong:

1. **Check the error message** - It will tell you what failed
2. **Check PHP version** - Must be >= 8.1
3. **Check Node version** - Must be >= 14.0 (if using npm)
4. **Check disk space** - Must have >= 1GB free
5. **Check internet connection** - Scripts need to download packages
6. **Run individual commands** manually to see detailed errors

---

## Script Comparison

| Feature | Linux/macOS (deploy.sh) | Windows (deploy.bat) |
|---------|---|---|
| Colored output | ✓ Yes | ✓ Yes |
| Error handling | ✓ Yes | ✓ Yes |
| Pre-flight checks | ✓ Yes | ✓ Yes |
| Composer | ✓ Yes | ✓ Yes |
| npm | ✓ Yes | ✓ Yes |
| Build frontend | ✓ Yes | ✓ Yes |
| Cache optimization | ✓ Yes | ✓ Yes |
| Permissions setup | ✓ Yes | — No (Windows) |

---

## Version

- **Created**: April 2026
- **Laravel Version**: ^11.0
- **Tested On**: Windows 10+, Ubuntu 20.04+, macOS 10.15+

---

**Ready to deploy? Just run the script for your operating system!**
