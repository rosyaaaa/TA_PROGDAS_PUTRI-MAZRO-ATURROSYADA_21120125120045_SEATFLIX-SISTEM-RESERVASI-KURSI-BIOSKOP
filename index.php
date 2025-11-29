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
<style>
body, html {margin:0;padding:0;font-family:Arial,sans-serif; background:#111; color:#fff;}
.container {display:flex; height:100vh;}
.sidebar {width:200px; background:#1c1c1c; padding:20px; display:flex; flex-direction:column; border-right:2px solid #333;top:0px;position:absolute; height:100%;}
.sidebar h2 {margin:0 0 20px 0; font-size:1.5em; color:#e50914;}
.sidebar ul {list-style:none; padding:0; width:100%;}
.sidebar li {margin:15px 0; cursor:pointer; padding:5px 10px; border-radius:5px; transition:0.3s; width:100%; text-align:left;}
.sidebar li:hover {background:#e50914; color:#fff;}
.sidebar li a {color:inherit; text-decoration:none; display:block; width:100%;}
.main-content {flex:1; display:flex; align-items:center; justify-content:center; overflow:hidden; position:relative;margin-left:300px;box-sizing:border-box;}
.carousel {overflow-x:auto; scroll-behavior:smooth; gap:20px; padding:20px; height:100%; cursor:grab;box-sizing:border-box;}
.card {min-width:250px; height:350px; background-size:cover; background-position:center; float:left;margin:10px;border-radius:15px; position:relative; flex-shrink:0; transition:transform 0.3s, box-shadow 0.3s; cursor:pointer;}
.card:hover {transform:scale(1.05); box-shadow:0 10px 25px rgba(0,0,0,0.7);}
.overlay {position:absolute; bottom:0; width:100%; padding:10px; background:rgba(0,0,0,0.5); opacity:0; transition:opacity 0.3s; text-align:center; border-radius:0 0 15px 15px;}
.card:hover .overlay{opacity:1;}
.overlay button{background:#e50914;border:none;color:#fff;padding:7px 12px;border-radius:5px;cursor:pointer;margin-top:5px;}
.overlay button:hover{opacity:0.8;}
.header-title {text-align:center; font-size:2em; padding:10px; color:#e50914;width: calc(100%-300px);box-sizing:border-box;margin-left:300px;}
#watermark {position:fixed; bottom:10px; right:10px; font-size:12px; color:rgba(255,255,255,0.4);}
</style>
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
            <p><?= $show['genre'] ?? 'Genre Tidak Ada' ?></p>
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
// Carousel drag
const carousel = document.getElementById('carousel');
let isDown = false, startX, scrollLeft;
carousel.addEventListener('mousedown', (e)=>{isDown=true; startX=e.pageX - carousel.offsetLeft; scrollLeft=carousel.scrollLeft; carousel.style.cursor='grabbing';});
carousel.addEventListener('mouseleave', ()=>{isDown=false; carousel.style.cursor='grab';});
carousel.addEventListener('mouseup', ()=>{isDown=false; carousel.style.cursor='grab';});
carousel.addEventListener('mousemove', (e)=>{if(!isDown) return; e.preventDefault(); const x=e.pageX - carousel.offsetLeft; const walk=(x-startX)*2; carousel.scrollLeft = scrollLeft - walk;});

// Clear All popup
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
