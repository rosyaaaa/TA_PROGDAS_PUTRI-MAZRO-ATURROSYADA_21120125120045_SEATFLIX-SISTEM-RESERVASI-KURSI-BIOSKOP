<?php


define("DATA_PATH", __DIR__ . "/../data");
if (!file_exists(DATA_PATH)) mkdir(DATA_PATH, 0777, true);

$SHOWS_FILE = DATA_PATH . "/shows.json";
$BOOKINGS_FILE = DATA_PATH . "/bookings.json";


if (!file_exists($SHOWS_FILE)) create_default_shows();
if (!file_exists($BOOKINGS_FILE)) {
    file_put_contents($BOOKINGS_FILE, json_encode([], JSON_PRETTY_PRINT));
}


function create_default_shows()
{
    $shows = [
        "Screen 1 - 19:00" => [
            "title" => "Film 1 Avengers",
            "genre" => "Action",
            "poster" => "poster/film1.jpg",
            "rows" => 6,
            "cols" => 8,
            "vip_rows" => 1,
            "seats" => array_fill(0, 6, array_fill(0, 8, 0))
        ],

        "Screen 2 - 20:30" => [
            "title" => "Film 2 Jumbo",
            "genre" => "Adventure",
            "poster" => "poster/film2.jpg",
            "rows" => 5,
            "cols" => 7,
            "vip_rows" => 1,
            "seats" => array_fill(0, 5, array_fill(0, 7, 0))
        ],

        "Screen 3 - 18:00" => [
            "title" => "Film 3 Sore",
            "genre" => "Drama",
            "poster" => "poster/film3.jpg",
            "rows" => 4,
            "cols" => 6,
            "vip_rows" => 1,
            "seats" => array_fill(0, 4, array_fill(0, 6, 0))
        ]
    ];

    global $SHOWS_FILE;
    file_put_contents($SHOWS_FILE, json_encode($shows, JSON_PRETTY_PRINT));
}


function json_load($file, $default = null)
{
    if (!file_exists($file)) return $default;
    $data = json_decode(file_get_contents($file), true);
    return $data ?? $default;
}

function json_save($file, $data)
{
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}


function load_shows()
{
    global $SHOWS_FILE;
    return json_load($SHOWS_FILE, []);
}

function save_shows($shows)
{
    global $SHOWS_FILE;
    return json_save($SHOWS_FILE, $shows);
}

function load_bookings()
{
    global $BOOKINGS_FILE;
    return json_load($BOOKINGS_FILE, []);
}

function save_bookings($bookings)
{
    global $BOOKINGS_FILE;
    return json_save($BOOKINGS_FILE, $bookings);
}


function seat_label($r, $c)
{
    return chr(65 + $r) . ($c + 1);
}

function is_vip($show, $r)
{
    return $r < ($show["vip_rows"] ?? 0);
}


function book_seat($show_key, $r, $c, $name = "Anonymous")
{
    global $SHOWS_FILE, $BOOKINGS_FILE;

    $shows = json_load($SHOWS_FILE, []);

    if (!isset($shows[$show_key])) return false;


    if ($shows[$show_key]["seats"][$r][$c] !== 0) return false;

    
    $shows[$show_key]["seats"][$r][$c] = 1;
    json_save($SHOWS_FILE, $shows);

    
    $bookings = json_load($BOOKINGS_FILE, []);
    $bookings[] = [
        "show" => $show_key,
        "seat" => seat_label($r, $c),
        "row" => $r,
        "col" => $c,
        "name" => $name,
        "time" => date("d-m-Y H:i")
    ];

    json_save($BOOKINGS_FILE, $bookings);

    return true;
}

?>
