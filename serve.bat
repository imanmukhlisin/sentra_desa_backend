@echo off
REM Jalankan Laravel dev server dengan CORS support untuk storage files
php -S 127.0.0.1:8000 -t public/ server.php
