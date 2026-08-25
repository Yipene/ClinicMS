# Installation Clinic MS (Windows / PowerShell)
$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot\..

Write-Host "=== Clinic MS - Installation ===" -ForegroundColor Cyan

if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    Write-Host ".env créé depuis .env.example"
}

composer install --no-interaction
php artisan key:generate --force
php artisan migrate --seed --force
npm install
npm run build

Write-Host ""
Write-Host "Installation terminée." -ForegroundColor Green
Write-Host "Lancez: php artisan serve"
Write-Host "Comptes démo: admin@clinic.local / password"
