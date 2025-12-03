<?php
require_once __DIR__ . "/inc/helpers.php";
$shows = load_shows();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>SeatFlix - Sistem Reservasi Kursi Bioskop</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="header-title">🎬 SeatFlix - Sistem Reservasi Kursi Bioskop 🎬</div>
<div class="container">
  <div class="sidebar">
    <h2>Menu</h2>
    <ul>
      <li>Home</li>
      <li><a href="history.php">History</a></li>
      <li><a href="#" id="clearBtn">Clear All</a></li>
    </ul>
  </div>
  <div class="main-content">
    <div class="carousel" id="carousel">
      <?php foreach($shows as $id => $show): ?>
        <div class="card" style="background-image:url('<?= $show['poster'] ?>')">
          <div class="overlay">
            <h3><?= $show['title'] ?></h3>
            <p><?= $show['genre'] ?></p>
            <p>Jam Tayang: <?= explode(' - ',$id)[1] ?></p>
            <a href="room.php?id=<?= urlencode($id) ?>"><button>Pesan Sekarang</button></a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<div id="watermark">Admin: Putri Mazro'aturrosyada | NIM: 21120125120045</div>

<!-- Popup Clear All -->
<div id="clearPopup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,0.7); align-items:center; justify-content:center; z-index:1000;">
    <div style="background:#222; padding:20px; border-radius:10px; text-align:center;">
        <h2>Konfirmasi Clear All</h2>
        <p>Apakah kamu yakin ingin menghapus semua booking?</p>
        <button id="confirmClear" style="margin-right:10px; padding:7px 12px; background:#e50914; color:#fff; border:none; border-radius:5px;">Ya</button>
        <button id="cancelClear" style="padding:7px 12px; background:#555; color:#fff; border:none; border-radius:5px;">Batal</button>
    </div>
</div>

<script>

const carousel = document.getElementById('carousel');
let isDown = false, startX, scrollLeft;
carousel.addEventListener('mousedown', (e)=>{isDown=true; startX=e.pageX - carousel.offsetLeft; scrollLeft=carousel.scrollLeft; carousel.style.cursor='grabbing';});
carousel.addEventListener('mouseleave', ()=>{isDown=false; carousel.style.cursor='grab';});
carousel.addEventListener('mouseup', ()=>{isDown=false; carousel.style.cursor='grab';});
carousel.addEventListener('mousemove', (e)=>{if(!isDown) return; e.preventDefault(); const x=e.pageX - carousel.offsetLeft; const walk=(x-startX)*2; carousel.scrollLeft = scrollLeft - walk;});


const clearBtn = document.getElementById('clearBtn');
const clearPopup = document.getElementById('clearPopup');
const confirmClear = document.getElementById('confirmClear');
const cancelClear = document.getElementById('cancelClear');

clearBtn.addEventListener('click', (e)=>{
    e.preventDefault();
    clearPopup.style.display='flex';
});

cancelClear.addEventListener('click', ()=>{
    clearPopup.style.display='none';
});

confirmClear.addEventListener('click', ()=>{
    fetch('clear.php', {method:'POST'})
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            alert('Semua booking berhasil dihapus!');
            location.reload();
        }
    });
});
</script>
</body>
</html>