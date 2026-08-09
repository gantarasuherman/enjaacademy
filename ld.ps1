<#
.SYNOPSIS
    Laradock task runner for Windows — the PowerShell twin of the Makefile.

.DESCRIPTION
    Laradock lives in ./laradock and mounts this directory at /var/www, so every
    artisan/composer/npm command runs inside its `workspace` container. Ports and
    service versions are configured in laradock/.env, never here.

.EXAMPLE
    .\ld.ps1 install     # first-time setup
    .\ld.ps1 fresh       # rebuild the database
    .\ld.ps1 deploy-spa  # build the React SPA into public/app
#>

[CmdletBinding()]
param(
    [Parameter(Position = 0)]
    [string]$Task = 'help',

    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Rest
)

$ErrorActionPreference = 'Stop'
$Root = $PSScriptRoot
$Laradock = Join-Path $Root 'laradock'
$Services = @('nginx', 'mysql', 'redis', 'phpmyadmin', 'mailpit', 'workspace')

function Compose { docker compose --project-directory $Laradock -f (Join-Path $Laradock 'docker-compose.yml') @args }

# The workspace image already sets /var/www as its working directory.
function Ws    { Compose exec -T workspace @args }
function WsTty { Compose exec workspace @args }

function Assert-Success($what) {
    if ($LASTEXITCODE -ne 0) { throw "$what failed (exit $LASTEXITCODE)" }
}

switch ($Task) {

    # ------------------------------------------------------------- lifecycle

    'up'      { Compose up -d @Services }
    'down'    { Compose down }
    'restart' { Compose restart @Services }
    'build'   { Compose build workspace php-fpm }
    'ps'      { Compose ps }
    'logs'    { Compose logs -f nginx php-fpm }
    'queue'   { Compose up -d php-worker }

    'schedule' {
        Compose exec -d workspace php artisan schedule:work
        Write-Host 'Scheduler running in the background.'
    }

    # --------------------------------------------------------------- laravel

    'install' {
        Compose up -d @Services;              Assert-Success 'docker compose up'
        Ws composer install --no-interaction; Assert-Success 'composer install'
        Ws php artisan key:generate --force
        Ws php artisan storage:link
        Ws php artisan migrate --force;       Assert-Success 'migrate'
        Ws php artisan db:seed --force;       Assert-Success 'seed'
        & $PSCommandPath assets

        Write-Host ''
        Write-Host '  App / admin : http://localhost:8000/admin'
        Write-Host '  phpMyAdmin  : http://localhost:8080'
        Write-Host '  Mailpit     : http://localhost:8025'
        Write-Host '  Login       : superadmin@nihongo.test / password'
    }

    'migrate' { Ws php artisan migrate --force }
    'fresh'   { Ws php artisan migrate:fresh --seed --force }
    'seed'    { Ws php artisan db:seed --force }
    'backup'  { Ws php artisan backup:run }
    'test'    { Ws php artisan test }
    'lint'    { Ws ./vendor/bin/pint }
    'shell'   { WsTty bash }
    'tinker'  { WsTty php artisan tinker }

    'cache-clear' {
        Ws php artisan optimize:clear
        Ws php artisan menu:clear
    }

    # -------------------------------------------------------------- frontend

    'assets' {
        Ws npm install --no-audit --no-fund; Assert-Success 'npm install'
        Ws npm run build;                    Assert-Success 'npm run build'
    }

    'spa-build' {
        Ws bash -lc 'cd /var/www/frontend && npm install --no-audit --no-fund && npm run build'
        Assert-Success 'spa build'
    }

    'spa-dev' {
        WsTty bash -lc 'cd /var/www/frontend && npm run dev -- --host 0.0.0.0'
    }

    'deploy-spa' {
        & $PSCommandPath spa-build; Assert-Success 'spa build'
        Ws bash -lc 'rm -rf /var/www/public/app && mkdir -p /var/www/public/app && cp -r /var/www/frontend/dist/. /var/www/public/app/'
        Write-Host 'SPA published to public/app — learner routes now serve the build.'
    }

    # ------------------------------------------------------------------ help

    default {
        @'
Laradock task runner

  Lifecycle
    up            Start the stack
    down          Stop the stack
    restart       Restart the stack
    build         Rebuild workspace and php-fpm images
    ps            Container status
    logs          Tail nginx + php-fpm logs
    queue         Start the queue worker (php-worker)
    schedule      Run the Laravel scheduler in the background

  Laravel
    install       First-time setup (deps, key, migrate, seed, assets)
    migrate       Run pending migrations
    fresh         Drop everything and re-seed (destructive)
    seed          Re-run seeders
    cache-clear   Clear config/route/view + menu cache
    backup        Create a database backup
    shell         Bash inside the workspace container
    tinker        Open tinker
    test          Run the test suite
    lint          Format PHP with Pint

  Frontend
    assets        Build admin-panel assets
    spa-build     Build the React SPA
    spa-dev       React SPA dev server (http://localhost:5173)
    deploy-spa    Build the SPA and publish it into public/app

Usage:  .\ld.ps1 <task>
'@ | Write-Host
    }
}
