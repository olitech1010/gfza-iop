[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
$baseUrl = "https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights"
$dest = "public/models"
$jsDest = "public/js"

# Create directories if not exist
if (!(Test-Path $dest)) { New-Item -ItemType Directory -Force -Path $dest | Out-Null }
if (!(Test-Path $jsDest)) { New-Item -ItemType Directory -Force -Path $jsDest | Out-Null }

# Function to download file
function Download-File($url, $output) {
    Write-Host "Downloading $output..."
    try {
        $wc = New-Object System.Net.WebClient
        $wc.DownloadFile($url, $output)
        Write-Host "  Success" -ForegroundColor Green
    } catch {
        Write-Host "  Failed to download $url" -ForegroundColor Red
        Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Download face-api.js
Download-File "https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js" "$jsDest/face-api.min.js"

# Download Models
$models = @(
    "ssd_mobilenetv1_model-weights_manifest.json",
    "ssd_mobilenetv1_model-shard1",
    "ssd_mobilenetv1_model-shard2",
    "face_landmark_68_model-weights_manifest.json",
    "face_landmark_68_model-shard1",
    "face_recognition_model-weights_manifest.json",
    "face_recognition_model-shard1",
    "face_recognition_model-shard2"
)

foreach ($model in $models) {
    Download-File "$baseUrl/$model" "$dest/$model"
}
