@echo off
setlocal
REM ============================================================
REM  CREDISOPORTE - Instalador de base de datos (XAMPP Windows)
REM  Ejecutar desde la raiz del proyecto:
REM      database\credisistema\INSTALAR.bat
REM  DB_NAME debe coincidir con DB_DATABASE de backend\.env
REM ============================================================

set DB_HOST=localhost
set DB_USER=root
set DB_PASS=
set DB_NAME=credisoportecom_credisopo
set MYSQL=C:\xampp\mysql\bin\mysql.exe
set PHP=php

if not exist "%MYSQL%" (
    echo ERROR: no se encuentra %MYSQL%
    echo Ajusta la variable MYSQL dentro de este archivo.
    exit /b 1
)

if "%DB_PASS%"=="" (
    set AUTH=-h %DB_HOST% -u %DB_USER%
) else (
    set AUTH=-h %DB_HOST% -u %DB_USER% -p%DB_PASS%
)

set BASE=%~dp0
echo [1/5] Creando base %DB_NAME% ...
"%MYSQL%" %AUTH% -e "CREATE DATABASE IF NOT EXISTS `%DB_NAME%` CHARACTER SET utf8 COLLATE utf8_spanish_ci;"
if errorlevel 1 ( echo ERROR creando la base. & exit /b 1 )

echo [2/5] Importando 01_schema.sql (puede tardar unos minutos) ...
"%MYSQL%" %AUTH% %DB_NAME% < "%BASE%01_schema.sql"
if errorlevel 1 ( echo ERROR en schema. & exit /b 1 )

echo [3/5] Importando 02_procedimientos.sql ...
"%MYSQL%" %AUTH% %DB_NAME% < "%BASE%02_procedimientos.sql"
if errorlevel 1 ( echo ERROR en procedimientos. & exit /b 1 )

echo [4/5] Aplicando migraciones ...
for %%f in (01_evaluacion_datetime 02_galeria_credito 03_home_stats 04_origen_cliente 05_produccion_consolidada 06_tipos_dirigido_a_imagen) do (
    echo   - %%f
    "%MYSQL%" %AUTH% %DB_NAME% < "%BASE%03_migraciones\%%f.sql"
    if errorlevel 1 ( echo ERROR en migracion %%f. & exit /b 1 )
)

echo [5/5] Ejecutando seed de autorizacion ...
"%PHP%" "%BASE%04_seeds\seed_authorization.php"
if errorlevel 1 ( echo ERROR en seed. & exit /b 1 )

echo.
echo === BD %DB_NAME% lista ===
