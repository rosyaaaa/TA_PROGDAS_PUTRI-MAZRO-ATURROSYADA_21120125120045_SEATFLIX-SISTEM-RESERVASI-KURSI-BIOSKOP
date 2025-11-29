<?php
require_once __DIR__ . "/inc/helpers.php";

$shows = load_shows();
$booking_file = __DIR__."/data/bookings.json";
$bookings = [];
if(file_exists($booking_file)){
    $bookings = json_decode(file_get_contents($booking_file), true) ?: [];
}
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
if(empty($bookings)){
    echo "<p>Tidak ada history booking.</p>";
} else {
    foreach($bookings as $show_id => $list){
        $title = $shows[$show_id]['title'] ?? "Unknown Film";
        foreach($list as $b){
            $seat = $b['seat'] ?? ($b['row'].'-'.$b['col']);
            $name = $b['name'] ?? "Anonymous";
            $price = $b['price'] ?? 0;
            $time = isset($b['time']) ? date('d-m-Y H:i', strtotime($b['time'])) : "Unknown";
            echo "<div class='card'>";
            echo "<p><strong>Nama Customer:</strong> $name</p>";
            echo "<p><strong>Film:</strong> $title</p>";
            echo "<p><strong>Kursi:</strong> $seat</p>";
            echo "<p><strong>Jam:</strong> $time</p>";
            echo "<p><strong>Harga:</strong> Rp ".number_format($price,0,',','.')."</p>";
            echo "</div>";
        }
    }
}
?>

</body>
</html>
