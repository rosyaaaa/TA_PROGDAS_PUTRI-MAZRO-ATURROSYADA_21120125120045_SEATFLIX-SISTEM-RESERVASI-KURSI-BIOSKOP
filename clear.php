<?php
header('Content-Type: application/json');
require_once __DIR__ . "/inc/helpers.php";

if($_SERVER['REQUEST_METHOD']==='POST'){
    save_bookings([]);

    $shows = load_shows();

    $keys = array_keys($shows);
    $count = count($keys);

    for ($i=0; $i < $count; $i++){
        $key = $keys[$i];

        if (!isset($shows[$key]['rows']) || !isset($shows[$key]['cols'])) continue;

        $rMax = $shows[$key]['rows'];
        $cMax = $shows[$key]['cols'];

        for($r=0;$r<$rMax;$r++){
            if (!isset($shows[$key]['seats'][$r])) {
                $shows[$key]['seats'][$r] = [];
            }
            for($c=0;$c<$cMax;$c++){
                $shows[$key]['seats'][$r][$c] = 0;
            }
        }
    }

    save_shows($shows);
    echo json_encode(['success'=>true]);
    exit;
}

echo json_encode(['success'=>false]);
