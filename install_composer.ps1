# Install-Composer.ps1
# Recursively find and run "composer install" in each folder with a composer.json

$rootPath = Get-Location

# Find all composer.json files recursively
$composerFiles = Get-ChildItem -Path $rootPath -Recurse -Filter composer.json

foreach ($file in $composerFiles) {
    $projectDir = $file.DirectoryName
    Write-Host "`n==> Running 'composer install' in $projectDir" -ForegroundColor Cyan

    Push-Location $projectDir
    try {
	composer config --no-plugins allow-plugins.kylekatarnls/update-helper true
	composer config --no-plugins allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
    composer install 
    }
    catch {
        Write-Warning "Composer install failed in $projectDir"
    }
    finally {
        Pop-Location
    }
}