@echo off
echo ================================================================
echo   Laravel Train Shop - Apache Configuration Setup
echo ================================================================
echo.

echo Step 1: Apache Virtual Host Configuration
echo The configuration file has been generated: apache-my-trains.local.conf
echo.

echo Step 2: Manual Steps Required
echo.
echo 1. Copy the apache-my-trains.local.conf file to your Laragon Apache configuration directory
echo    (Usually: C:\laragon\etc\apache2\sites-enabled\ or similar)
echo.
echo 2. Add this line to your hosts file as Administrator:
echo    127.0.0.1 my-trains.local
echo    (Hosts file location: C:\Windows\System32\drivers\etc\hosts)
echo.
echo 3. Restart Apache in Laragon Control Panel
echo.
echo 4. Access your Laravel application at: http://my-trains.local
echo.

echo Current Laravel application details:
echo - Project Path: %cd%
echo - Public Path: %cd%\public
echo - Domain: my-trains.local
echo.

echo Alternative: You can also use Laragon's Auto Virtual Hosts feature
echo by renaming your project folder to "my-trains.local"
echo.

pause


