<?php
if($_SERVER['REQUEST_METHOD']!=='POST') die('Invalid');

$data = json_decode(file_get_contents('php://input'), true);
if(!$data || !isset($data['id']) || !isset($data['bookings'])) die('Invalid data');

$id = $data['id'];
$newBookings = $data['bookings'];
$file = __DIR__.'/data/bookings.json';

$all = [];
if(file_exists($file)){
    $all = json_decode(file_get_contents($file), true) ?: [];
}

if(!isset($all[$id])) $all[$id] = [];
$all[$id] = array_merge($all[$id], $newBookings);

file_put_contents($file, json_encode($all, JSON_PRETTY_PRINT));
echo json_encode(['success'=>true]);
