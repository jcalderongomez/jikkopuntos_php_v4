@echo off
echo ========================================
echo   Instalador de Jikko Puntos Dashboard
echo ========================================
echo.

echo [1/4] Verificando PostgreSQL...
psql --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PostgreSQL no esta instalado o no esta en el PATH
    echo Por favor instale PostgreSQL desde https://www.postgresql.org/download/
    pause
    exit /b 1
)
echo PostgreSQL encontrado!
echo.

echo [2/4] Creando base de datos jikkopuntos_v4...
psql -U postgres -c "CREATE DATABASE jikkopuntos_v4;" 2>nul
if %errorlevel% equ 0 (
    echo Base de datos creada exitosamente!
) else (
    echo La base de datos ya existe o hubo un error.
)
echo.

echo [3/4] Ejecutando script SQL para crear tablas...
psql -U postgres -d jikkopuntos_v4 -f database.sql
if %errorlevel% neq 0 (
    echo ERROR: No se pudieron crear las tablas
    pause
    exit /b 1
)
echo Tablas creadas exitosamente!
echo.

echo [4/4] Verificando extension PHP PostgreSQL...
php -m | findstr pgsql >nul
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Extension PHP PostgreSQL (pgsql) no encontrada
    echo Por favor habilite la extension en php.ini:
    echo   extension=pgsql
    echo   extension=pdo_pgsql
    echo Luego reinicie Apache.
    pause
) else (
    echo Extension PHP PostgreSQL encontrada!
)
echo.

echo ========================================
echo   Instalacion completada!
echo ========================================
echo.
echo Acceda al dashboard en:
echo http://localhost/jikkopuntos_v4/
echo.
echo No olvide configurar Google Sheets API
echo en la seccion de Sincronizacion.
echo.
pause
