<?php
require_once __DIR__ . "/inc/helpers.php";

// pastikan timezone konsisten (jika belum diset di helpers.php)
date_default_timezone_set('Asia/Jakarta');

$shows = load_shows();
$booking_file = __DIR__ . "/data/bookings.json";
$bookings = [];

// Load file booking
if (file_exists($booking_file)) {
    $bookings = json_decode(file_get_contents($booking_file), true) ?: [];
}

// Gabungkan dan urutkan (terbaru di atas)
$merged = [];
foreach ($bookings as $show_id => $list) {
    foreach ($list as $b) {
        $b['show_id'] = $show_id;
        $merged[] = $b;
    }
}
usort($merged, function ($a, $b) {
    $ta = isset($a['time']) ? strtotime($a['time']) : 0;
    $tb = isset($b['time']) ? strtotime($b['time']) : 0;
    return $tb - $ta;
});
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>History Booking</title>
<style>
body{background:#111;color:#fff;font-family:Arial,sans-serif;padding:20px;}
h1{color:#e50914;text-align:center;}
.card{background:#222;padding:15px;margin:10px 0;border-radius:8px;box-shadow:0 3px 10px rgba(0,0,0,0.3);}
.button-home{display:inline-block;padding:10px 20px;margin-bottom:20px;background:#e50914;color:#fff;border:none;border-radius:5px;text-decoration:none;cursor:pointer;}
.button-home:hover{opacity:0.8;}
</style>
</head>
<body>

<a href="index.php" class="button-home">← Home</a>

<h1>History Booking</h1>

<?php
if (empty($merged)) {
    echo "<p>Tidak ada history booking.</p>";
} else {
    foreach ($merged as $b) {

        $show_id = $b['show_id'];
        $title = $shows[$show_id]['title'] ?? "Unknown Film";

        $seat = $b['seat'] ?? ($b['row'].'-'.$b['col']);
        $name = $b['name'] ?? "Anonymous";
        $price = $b['price'] ?? 0;

        // Format jam yang sama dengan struk booking (local Asia/Jakarta)
        if (isset($b['time']) && !empty($b['time'])) {
            try {
                $dt = new DateTime($b['time']);
                $dt->setTimezone(new DateTimeZone('Asia/Jakarta'));
                $time = $dt->format('d-m-Y H:i:s');
            } catch (Exception $e) {
                $time = $b['time'];
            }
        } else {
            $time = "Unknown";
        }

        echo "<div class='card'>";
        echo "<p><strong>Nama Customer:</strong> $name</p>";
        echo "<p><strong>Film:</strong> $title</p>";
        echo "<p><strong>Kursi:</strong> $seat</p>";
        echo "<p><strong>Jam:</strong> $time</p>";
        echo "<p><strong>Harga:</strong> Rp ".number_format($price,0,',','.')."</p>";
        echo "</div>";
    }
}
?>

</body>
</html>
