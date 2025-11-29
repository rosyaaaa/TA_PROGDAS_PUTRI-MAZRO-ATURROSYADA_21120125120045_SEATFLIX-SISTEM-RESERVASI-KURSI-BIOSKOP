Sistem reservasi kursi bioskop dengan fitur pemilihan kursi, popup struk, dan history pesanan.

bioskop_php/
│
├── index.php               → Halaman Home (Menu + Carousel Film)
├── room.php                → Halaman pemilihan kursi + popup booking
├── history.php             → Menampilkan riwayat booking
├── clear.php               → Menghapus seluruh data bookings.json
├── book_ajax.php           → Endpoint AJAX untuk menyimpan booking (dipanggil dari JS)
├── style.css               → Tampilan $ GUI
│
├── inc/
│   ├── helpers.php         → File pusat
│
├── data/
│   ├── shows.json          → Data film + kursi + VIP
│   ├── bookings.json       → Riwayat pemesanan kursi
│
├── poster/
│   ├── film1.jpg
│   ├── film2.jpg
│   ├── film3.jpg
│
└── README.md               
