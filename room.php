<?php
require_once __DIR__ . "/inc/helpers.php";

if(!isset($_GET['id'])) die('Show tidak ditemukan');
$id = $_GET['id'];
$shows = load_shows();
if(!isset($shows[$id])) die('Show tidak ditemukan');
$show = $shows[$id];


$seat_price = 50000; 
$vip_price = 75000;


$booking_file = __DIR__."/data/bookings.json";
$bookings = [];
if(file_exists($booking_file)){
    $bookings = json_decode(file_get_contents($booking_file), true) ?: [];
}


$bookedSeats = [];
if(isset($bookings[$id])){
    foreach($bookings[$id] as $b){
        $bookedSeats[$b['row'].'-'.$b['col']] = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= $show['title'] ?> - Pilih Kursi</title>
<style>
body{background:#111;color:#fff;font-family:Arial,sans-serif;margin:0;padding:0;}
.container{padding:20px;text-align:center;}
h1{color:#e50914;}
.seats{display:grid;grid-template-columns:repeat(<?= $show['cols'] ?>,50px);gap:5px;justify-content:center;margin:20px auto;}
.seat{width:50px;height:50px;background:#444;display:flex;align-items:center;justify-content:center;cursor:pointer;border-radius:5px;}
.seat.vip{background:#e50914;}
.seat.taken{background:#555;cursor:not-allowed;}
.seat.selected{outline:3px solid #ffffffff;}
#bookBtn,#backBtn{margin:10px auto;padding:10px 20px;background:#e50914;color:#fff;border:none;border-radius:5px;cursor:pointer;display:block;}
#bookBtn:hover,#backBtn:hover{opacity:0.8;}

/* Popup */
.popup-bg{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;z-index:9999;}
.popup{background:#222;padding:20px;border-radius:10px;min-width:300px;text-align:center;box-shadow:0 5px 20px rgba(0,0,0,0.5);}
.popup h2{color:#e50914;margin-top:0;}
.popup button{margin-top:10px;padding:7px 15px;border:none;border-radius:5px;background:#e50914;color:#fff;cursor:pointer;}
.popup button:hover{opacity:0.8;}
input[type=text]{width:90%;padding:5px;border-radius:5px;border:none;margin-top:5px;}
</style>
</head>
<body>

<div class="container">
<h1><?= $show['title'] ?> - Pilih Kursi</h1>

<div class="seats">
<?php
for($r=0;$r<$show['rows'];$r++){
    for($c=0;$c<$show['cols'];$c++){
        $taken = $bookedSeats[$r.'-'.$c] ?? ($show['seats'][$r][$c]??0);
        $class = $taken?'taken':(is_vip($show,$r)?'vip':'');
        echo '<div class="seat '.$class.'" data-row="'.$r.'" data-col="'.$c.'"'.($taken?' data-taken="1"':'').(is_vip($show,$r)?' data-vip="1"':'').'>'.seat_label($r,$c).'</div>';
    }
}
?>
</div>

<button id="backBtn" onclick="window.location.href='index.php'">← Kembali ke Halaman Depan</button>
<button id="bookBtn">Pesan Kursi</button>
</div>

<!-- Popup -->
<div id="popup" class="popup-bg">
    <div class="popup">
        <h2>Booking Kursi</h2>
        <div id="inputDiv">
            <p>Kursi yang dipilih: <span id="selectedSeats"></span></p>
            <input type="text" id="customerName" placeholder="Nama Customer">
            <button id="confirmBtn">Pesan</button>
            <button id="cancelBtn">Batal</button>
        </div>
        <div id="strukDiv" style="display:none;"></div>
    </div>
</div>

<script>
const seats = document.querySelectorAll('.seat');
const popup = document.getElementById('popup');
const selectedSeatsSpan = document.getElementById('selectedSeats');
const customerNameInput = document.getElementById('customerName');
const bookBtn = document.getElementById('bookBtn');
const inputDiv = document.getElementById('inputDiv');
const strukDiv = document.getElementById('strukDiv');
let selectedSeats = [];

const seat_price = <?= $seat_price ?>;
const vip_price = <?= $vip_price ?>;


seats.forEach(seat=>{
    seat.addEventListener('click',()=>{
        if(seat.dataset.taken) return;
        if(selectedSeats.includes(seat)){
            seat.classList.remove('selected');
            selectedSeats = selectedSeats.filter(s=>s!==seat);
        }else{
            seat.classList.add('selected');
            selectedSeats.push(seat);
        }
        selectedSeatsSpan.textContent = selectedSeats.map(s=>s.textContent).join(', ');
    });
});


bookBtn.addEventListener('click',()=>{
    if(selectedSeats.length===0){
        alert('Pilih minimal 1 kursi..');
        return;
    }
    customerNameInput.value=''
    inputDiv.style.display='block';
    strukDiv.style.display='none';
    popup.style.display='flex';
});


document.getElementById('cancelBtn').addEventListener('click',()=>{popup.style.display='none';});

document.getElementById('confirmBtn').addEventListener('click',()=>{
    const name = customerNameInput.value.trim()||'Anonymous';
    let total = 0;
    let strukDetail = '';
    let bookingData = [];

    selectedSeats.forEach(seat=>{
        seat.classList.add('taken');
        seat.dataset.taken = 1;
        seat.classList.remove('selected');
        let price = seat.dataset.vip ? vip_price : seat_price;
        total += price;
        strukDetail += `<p>${seat.textContent} - Rp ${price.toLocaleString('id')}</p>`;

        bookingData.push({
            row: seat.dataset.row,
            col: seat.dataset.col,
            seat: seat.textContent,
            name: name,
            price: price,
            time: new Date().toISOString()
        });
    });

    fetch('book_ajax.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({id:'<?= $id ?>', bookings: bookingData})
    });

    inputDiv.style.display='none';
    strukDiv.style.display='block';
    let now = new Date();
    strukDiv.innerHTML = `
        <h2>Struk Pemesanan</h2>
        <p><strong>Nama Customer:</strong> ${name}</p>
        <p><strong>Kursi:</strong></p>
        ${strukDetail}
        <p><strong>Total Harga:</strong> Rp ${total.toLocaleString('id')}</p>
        <p><strong>Waktu Pemesanan:</strong> ${now.toLocaleString('id-ID')}</p>
        <p><strong>Jadwal Tayang:</strong> <?= $show['title'] ?></p>
        <button onclick="popup.style.display='none'">Tutup</button>
    `;
    selectedSeats=[];
});
</script>

</body>
</html>
