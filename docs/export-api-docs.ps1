# API Documentation Export Script
# This script generates fresh API documentation and exports it for AI agents

Write-Host "=== City API Documentation Generator ===" -ForegroundColor Cyan
Write-Host ""

# Step 1: Generate fresh Swagger documentation
Write-Host "[1/4] Generating OpenAPI documentation..." -ForegroundColor Yellow
php artisan l5-swagger:generate

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Documentation generated successfully" -ForegroundColor Green
} else {
    Write-Host "✗ Failed to generate documentation" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Step 2: Show file location
$apiDocsPath = "storage\api-docs\api-docs.json"
Write-Host "[2/4] API Documentation Location:" -ForegroundColor Yellow
Write-Host "  File: $((Get-Item $apiDocsPath).FullName)" -ForegroundColor Cyan
Write-Host "  Size: $([math]::Round((Get-Item $apiDocsPath).Length / 1KB, 2)) KB" -ForegroundColor Cyan

Write-Host ""

# Step 3: Extract endpoints summary
Write-Host "[3/4] Extracting API Endpoints..." -ForegroundColor Yellow

$apiJson = Get-Content $apiDocsPath | ConvertFrom-Json
$endpoints = @()

foreach ($path in $apiJson.paths.PSObject.Properties) {
    $pathName = $path.Name
    foreach ($method in $path.Value.PSObject.Properties) {
        $methodName = $method.Name.ToUpper()
        $operation = $method.Value
        
        # Check if endpoint requires authentication
        $isProtected = $false
        if ($operation.security) {
            $isProtected = $true
        }
        
        $endpoints += [PSCustomObject]@{
            Method = $methodName
            Path = $pathName
            Summary = $operation.summary
            Tags = ($operation.tags -join ", ")
            Protected = $isProtected
        }
    }
}

Write-Host ""
Write-Host "Total Endpoints: $($endpoints.Count)" -ForegroundColor Green
Write-Host ""

# Group by tags
$grouped = $endpoints | Group-Object Tags | Sort-Object Name

foreach ($group in $grouped) {
    Write-Host "📁 $($group.Name)" -ForegroundColor Cyan
    foreach ($endpoint in $group.Group) {
        $lockIcon = if ($endpoint.Protected) { "🔒" } else { "🌐" }
        $methodColor = switch ($endpoint.Method) {
            "GET" { "Green" }
            "POST" { "Yellow" }
            "PUT" { "Magenta" }
            "DELETE" { "Red" }
            default { "White" }
        }
        Write-Host "  $lockIcon " -NoNewline
        Write-Host "$($endpoint.Method.PadRight(7))" -NoNewline -ForegroundColor $methodColor
        Write-Host " $($endpoint.Path)" -ForegroundColor Gray
        if ($endpoint.Summary) {
            Write-Host "           $($endpoint.Summary)" -ForegroundColor DarkGray
        }
    }
    Write-Host ""
}

# Step 4: Export options
Write-Host "[4/4] Export Options:" -ForegroundColor Yellow
Write-Host ""

# Option A: Copy to Flutter project
Write-Host "Option A - Copy to Flutter Project:" -ForegroundColor Cyan
Write-Host "  Copy-Item '$apiDocsPath' 'C:\path\to\flutter\project\openapi.json'" -ForegroundColor Gray
Write-Host ""

# Option B: View in browser
$webUrl = "http://localhost/api/documentation"
Write-Host "Option B - View in Browser:" -ForegroundColor Cyan
Write-Host "  Start-Process '$webUrl'" -ForegroundColor Gray
Write-Host ""

# Option C: Export for AI agent
$exportPath = "api-spec-for-ai.json"
Copy-Item $apiDocsPath $exportPath
Write-Host "Option C - AI Agent Ready File:" -ForegroundColor Cyan
Write-Host "  ✓ Exported to: $((Get-Item $exportPath).FullName)" -ForegroundColor Green
Write-Host ""

# Step 5: Generate endpoint summary for AI
Write-Host "[BONUS] Creating AI-Friendly Endpoint Summary..." -ForegroundColor Yellow

$aiSummary = @"
# City Shop Directory API - Endpoint Summary
Generated: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")

## Base URL
/api/v1

## Authentication
Type: Bearer Token (Laravel Sanctum)
Header: Authorization: Bearer {token}

## Endpoints Summary

"@

foreach ($group in $grouped) {
    $aiSummary += "`n### $($group.Name)`n`n"
    foreach ($endpoint in $group.Group) {
        $auth = if ($endpoint.Protected) { "[🔒 Protected]" } else { "[🌐 Public]" }
        $aiSummary += "- **$($endpoint.Method)** ``$($endpoint.Path)`` $auth`n"
        if ($endpoint.Summary) {
            $aiSummary += "  - $($endpoint.Summary)`n"
        }
    }
}

$aiSummary += @"

## Quick Start

### 1. Get Token
``````bash
POST /api/v1/auth/login
{
  "email": "user@example.com",
  "password": "password123"
}
``````

Response:
``````json
{
  "data": {
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
``````

### 2. Use Token
``````
Authorization: Bearer 1|abc123...
``````

### 3. City Landing Page Flow
``````
1. GET /api/v1/cities - List cities
2. GET /api/v1/cities/{id}/banners - Get city banners
3. GET /api/v1/cities/{id}/featured-shops - Get featured shops
4. GET /api/v1/cities/{id}/statistics - Get city stats
``````

## Full Specification
See: $((Get-Item $apiDocsPath).FullName)
OpenAPI Version: 3.0.0
"@

$aiSummaryPath = "API_ENDPOINTS_SUMMARY.md"
$aiSummary | Out-File -FilePath $aiSummaryPath -Encoding UTF8

Write-Host "  ✓ Summary created: $((Get-Item $aiSummaryPath).FullName)" -ForegroundColor Green
Write-Host ""

# Final instructions
Write-Host "=== Ready for AI Agent Integration ===" -ForegroundColor Green
Write-Host ""
Write-Host "Files Created:" -ForegroundColor Yellow
Write-Host "  1. storage/api-docs/api-docs.json - Full OpenAPI spec" -ForegroundColor White
Write-Host "  2. api-spec-for-ai.json - Copy for AI tools" -ForegroundColor White
Write-Host "  3. API_ENDPOINTS_SUMMARY.md - Human-readable summary" -ForegroundColor White
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "  • Share api-spec-for-ai.json with your AI agent" -ForegroundColor White
Write-Host "  • Use API_ENDPOINTS_SUMMARY.md for quick reference" -ForegroundColor White
Write-Host "  • Review FLUTTER_API_INTEGRATION.md for Flutter setup" -ForegroundColor White
Write-Host ""
Write-Host "Web Interface: $webUrl" -ForegroundColor Cyan
Write-Host ""

# Ask if user wants to open browser
$openBrowser = Read-Host "Open Swagger UI in browser? (y/n)"
if ($openBrowser -eq 'y' -or $openBrowser -eq 'Y') {
    Start-Process $webUrl
}
