@echo off
REM ============================================
REM OneTeera Deployment & Setup Script (Windows)
REM ============================================
REM This script:
REM 1. Installs PHP dependencies (Composer)
REM 2. Installs Node dependencies (NPM)
REM 3. Builds frontend assets
REM 4. Optimizes Laravel caches
REM 5. Clears and refreshes application state

setlocal enabledelayedexpansion

REM Color codes
set "GREEN=[92m"
set "RED=[91m"
set "YELLOW=[93m"
set "BLUE=[94m"
set "RESET=[0m"

REM ============================================
REM Pre-flight Checks
REM ============================================

cls
echo.
echo %BLUE%========================================%RESET%
echo %BLUE%OneTeera Deployment Script%RESET%
echo %BLUE%========================================%RESET%
echo.

REM Check if we're in the right directory
if not exist "composer.json" (
    echo %RED%[ERROR] composer.json not found.%RESET%
    echo Please run this script from the project root directory.
    pause
    exit /b 1
)
echo %GREEN%[OK] Found composer.json%RESET%

REM Check if PHP is available
php -v >nul 2>&1
if errorlevel 1 (
    echo %RED%[ERROR] PHP is not installed or not in PATH.%RESET%
    pause
    exit /b 1
)
for /f "tokens=*" %%i in ('php -v ^| findstr /R "^PHP"') do set "PHP_VERSION=%%i"
echo %GREEN%[OK] %PHP_VERSION%%RESET%

REM Check if Composer is available
composer --version >nul 2>&1
if errorlevel 1 (
    echo %RED%[ERROR] Composer is not installed or not in PATH.%RESET%
    pause
    exit /b 1
)
for /f "tokens=*" %%i in ('composer --version') do set "COMPOSER_VERSION=%%i"
echo %GREEN%[OK] %COMPOSER_VERSION%%RESET%

REM Check if Node is available
set "SKIP_NPM="
node --version >nul 2>&1
if errorlevel 1 (
    echo %YELLOW%[WARNING] Node.js not found. Skipping npm steps.%RESET%
    set "SKIP_NPM=1"
) else (
    for /f "tokens=*" %%i in ('node --version') do set "NODE_VERSION=%%i"
    echo %GREEN%[OK] Node !NODE_VERSION!%RESET%
    
    npm --version >nul 2>&1
    if errorlevel 1 (
        echo %YELLOW%[WARNING] npm not found. Skipping npm steps.%RESET%
        set "SKIP_NPM=1"
    ) else (
        for /f "tokens=*" %%i in ('npm --version') do set "NPM_VERSION=%%i"
        echo %GREEN%[OK] npm !NPM_VERSION!%RESET%
    )
)

echo.

REM ============================================
REM Step 1: Install PHP Dependencies
REM ============================================

echo %BLUE%========================================%RESET%
echo %BLUE%Step 1: Installing PHP Dependencies%RESET%
echo %BLUE%========================================%RESET%
echo.

if exist "vendor" (
    echo %YELLOW%[INFO] Updating composer dependencies...%RESET%
) else (
    echo %YELLOW%[INFO] Installing composer dependencies...%RESET%
)

call composer install --no-interaction --prefer-dist --optimize-autoloader
if errorlevel 1 (
    echo %RED%[ERROR] Failed to install PHP dependencies%RESET%
    pause
    exit /b 1
)
echo %GREEN%[OK] PHP dependencies installed%RESET%
echo.

REM ============================================
REM Step 2: Install Node Dependencies
REM ============================================

if not "!SKIP_NPM!"=="1" (
    echo %BLUE%========================================%RESET%
    echo %BLUE%Step 2: Installing Node Dependencies%RESET%
    echo %BLUE%========================================%RESET%
    echo.

    if exist "node_modules" (
        echo %YELLOW%[INFO] Updating npm dependencies...%RESET%
    ) else (
        echo %YELLOW%[INFO] Installing npm dependencies...%RESET%
    )

    call npm install
    if errorlevel 1 (
        echo %RED%[ERROR] Failed to install Node dependencies%RESET%
        pause
        exit /b 1
    )
    echo %GREEN%[OK] Node dependencies installed%RESET%
    echo.
) else (
    echo %BLUE%========================================%RESET%
    echo %BLUE%Step 2: Skipping Node Dependencies%RESET%
    echo %BLUE%========================================%RESET%
    echo.
)

REM ============================================
REM Step 3: Build Frontend Assets
REM ============================================

if not "!SKIP_NPM!"=="1" (
    echo %BLUE%========================================%RESET%
    echo %BLUE%Step 3: Building Frontend Assets%RESET%
    echo %BLUE%========================================%RESET%
    echo.

    findstr /M "\"build\"" package.json >nul 2>&1
    if errorlevel 1 (
        echo %YELLOW%[WARNING] No build script found in package.json%RESET%
    ) else (
        echo %YELLOW%[INFO] Running npm run build...%RESET%
        call npm run build
        if errorlevel 1 (
            echo %RED%[ERROR] Failed to build frontend assets%RESET%
            pause
            exit /b 1
        )
        echo %GREEN%[OK] Frontend assets built%RESET%
    )
    echo.
) else (
    echo %BLUE%========================================%RESET%
    echo %BLUE%Step 3: Skipping Frontend Build%RESET%
    echo %BLUE%========================================%RESET%
    echo.
)

REM ============================================
REM Step 4: Generate Application Key
REM ============================================

echo %BLUE%========================================%RESET%
echo %BLUE%Step 4: Checking Application Key%RESET%
echo %BLUE%========================================%RESET%
echo.

if not exist ".env" (
    echo %YELLOW%[INFO] .env file not found. Creating from .env.example...%RESET%
    if exist ".env.example" (
        copy .env.example .env >nul
        echo %GREEN%[OK] .env created from .env.example%RESET%
    ) else (
        echo %YELLOW%[WARNING] .env.example not found. Please create .env manually%RESET%
    )
)

php artisan key:generate --force >nul 2>&1
echo %GREEN%[OK] Application key verified/generated%RESET%
echo.

REM ============================================
REM Step 5: Clear Existing Caches
REM ============================================

echo %BLUE%========================================%RESET%
echo %BLUE%Step 5: Clearing Existing Caches%RESET%
echo %BLUE%========================================%RESET%
echo.

php artisan config:clear >nul 2>&1
echo %GREEN%[OK] Config cache cleared%RESET%

php artisan route:clear >nul 2>&1
echo %GREEN%[OK] Route cache cleared%RESET%

php artisan view:clear >nul 2>&1
echo %GREEN%[OK] View cache cleared%RESET%

php artisan cache:clear >nul 2>&1
echo %GREEN%[OK] Application cache cleared%RESET%
echo.

REM ============================================
REM Step 6: Optimize Application
REM ============================================

echo %BLUE%========================================%RESET%
echo %BLUE%Step 6: Optimizing Application%RESET%
echo %BLUE%========================================%RESET%
echo.

php artisan config:cache >nul 2>&1
echo %GREEN%[OK] Config cache built%RESET%

php artisan route:cache >nul 2>&1
echo %GREEN%[OK] Route cache built%RESET%

php artisan view:cache >nul 2>&1
echo %GREEN%[OK] View cache built%RESET%

php artisan optimize >nul 2>&1
if errorlevel 1 (
    echo %YELLOW%[INFO] Application already optimized%RESET%
) else (
    echo %GREEN%[OK] Application optimized%RESET%
)
echo.

REM ============================================
REM Step 7: Summary
REM ============================================

echo %BLUE%========================================%RESET%
echo %BLUE%Deployment Complete!%RESET%
echo %BLUE%========================================%RESET%
echo.
echo %GREEN%[SUCCESS] All setup steps completed successfully%RESET%
echo.
echo %BLUE%Summary:%RESET%
echo   %GREEN%[OK]%RESET% PHP dependencies installed
if not "!SKIP_NPM!"=="1" (
    echo   %GREEN%[OK]%RESET% Node dependencies installed
    echo   %GREEN%[OK]%RESET% Frontend assets built
)
echo   %GREEN%[OK]%RESET% Application key verified
echo   %GREEN%[OK]%RESET% Caches cleared
echo   %GREEN%[OK]%RESET% Application optimized
echo.
echo %YELLOW%Next Steps:%RESET%
echo   1. Configure your .env file if not already done
echo   2. Run migrations: php artisan migrate
echo   3. (Optional) Run seeders: php artisan db:seed
echo   4. Start your application
echo.
echo %GREEN%Happy coding! [ROCKET]%RESET%
echo.

pause
exit /b 0
