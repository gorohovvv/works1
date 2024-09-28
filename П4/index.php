<?php

function readIPsFromFile($filename) {
    $ips = [];
    if (file_exists($filename)) {
        $ips = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
    return $ips;
}
$white_list_file = "white.txt"; 
$black_list_file = "black.txt"; 

$white_list = readIPsFromFile($white_list_file);
$black_list = readIPsFromFile($black_list_file);

$user_ip = $_SERVER['REMOTE_ADDR'];

echo $user_ip;

if (in_array($user_ip, $white_list)) {
    echo "Content";
} elseif (in_array($user_ip, $black_list)) {
    header("Location: error.php");
    exit();
} else {
    echo "Content";
}

?>
