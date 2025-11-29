<?php
require_once __DIR__ . "/inc/helpers.php";
if($_SERVER['REQUEST_METHOD']==='POST'){
    save_bookings([]);
    $shows = load_shows();
    foreach($shows as &$s){
        for($r=0;$r<$s['rows'];$r++){
            for($c=0;$c<$s['cols'];$c++) $s['seats'][$r][$c]=0;
        }
    }
    save_shows($shows);
    echo json_encode(['success'=>true]);
}
