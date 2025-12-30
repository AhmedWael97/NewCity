# Regenerate Swagger API Documentation
Write-Host "Regenerating Swagger API Documentation..." -ForegroundColor Green

# Clear cache
Write-Host "`nClearing cache..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Generate Swagger documentation
Write-Host "`nGenerating Swagger documentation..." -ForegroundColor Yellow
php artisan l5-swagger:generate

Write-Host "`nSwagger documentation regenerated successfully!" -ForegroundColor Green
Write-Host "`nYou can access it at: http://your-domain/api/documentation" -ForegroundColor Cyan
Write-Host "`nMake sure your .env file has:" -ForegroundColor Yellow
Write-Host "APP_URL=https://your-actual-domain.com" -ForegroundColor White
Write-Host "L5_SWAGGER_GENERATE_ALWAYS=true" -ForegroundColor White
