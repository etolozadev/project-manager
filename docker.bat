@echo off
setlocal

set "COMMAND=%1"

if "%COMMAND%"=="up" goto up
if "%COMMAND%"=="down" goto down
if "%COMMAND%"=="build" goto build
if "%COMMAND%"=="restart" goto restart
if "%COMMAND%"=="shell" goto shell
if "%COMMAND%"=="artisan" goto artisan
if "%COMMAND%"=="composer" goto composer
if "%COMMAND%"=="npm" goto npm
if "%COMMAND%"=="logs" goto logs
if "%COMMAND%"=="fresh" goto fresh
if "%COMMAND%"=="optimize" goto optimize
if "%COMMAND%"=="migrate" goto migrate
if "%COMMAND%"=="install" goto install
if "%COMMAND%"=="dev" goto dev
goto help

:up
echo [Docker] Starting containers...
docker compose up -d
goto end

:down
echo [Docker] Stopping containers...
docker compose down
goto end

:build
echo [Docker] Building and starting containers...
docker compose up -d --build
goto end

:restart
echo [Docker] Restarting containers...
docker compose restart
goto end

:shell
echo [Docker] Opening shell in app container...
docker compose exec app bash
goto end

:artisan
set "ARGS=%~2 %~3 %~4 %~5 %~6 %~7 %~8 %~9"
echo [Docker] Running: php artisan %ARGS%
docker compose exec app php artisan %ARGS%
goto end

:composer
set "ARGS=%~2 %~3 %~4 %~5 %~6 %~7 %~8 %~9"
echo [Docker] Running: composer %ARGS%
docker compose exec app composer %ARGS%
goto end

:npm
set "ARGS=%~2 %~3 %~4 %~5 %~6 %~7 %~8 %~9"
echo [Docker] Running: npm %ARGS%
docker compose exec app npm %ARGS%
goto end

:logs
echo [Docker] Showing logs (Ctrl+C to stop)...
docker compose logs -f
goto end

:fresh
echo [Docker] Running fresh migrations with seeds...
docker compose exec app php artisan migrate:fresh --seed
goto end

:migrate
echo [Docker] Running migrations...
docker compose exec app php artisan migrate
goto end

:install
echo [Docker] Installing project (first time setup)...
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app npm install
docker compose exec app npm run build
goto end

:dev
echo [Docker] Starting Vite dev server...
docker compose exec app npm run dev
goto end

:optimize
echo [Docker] Optimizando Laravel (cache config/routes/views)...
docker compose exec app php artisan optimize
docker compose exec app composer dump-autoload --optimize
echo [Docker] Optimizacion completada.
goto end

:help
echo.
echo Usage: docker.bat [command] [args]
echo.
echo Commands:
echo   up              Start all containers
echo   down            Stop all containers
echo   build           Build and start containers
echo   restart         Restart containers
echo   shell           Open bash in app container
echo   artisan [cmd]   Run php artisan command
echo   composer [cmd]  Run composer command
echo   npm [cmd]       Run npm command
echo   logs            Show container logs
echo   fresh           Run migrate:fresh --seed
echo   migrate         Run migrations
echo   install         First time setup
echo   dev             Start Vite dev server
echo.
echo Examples:
echo   docker.bat up
echo   docker.bat artisan migrate
echo   docker.bat artisan make:controller UserController
echo   docker.bat composer require spatie/laravel-permission
echo   docker.bat npm install
goto end

:end
endlocal
