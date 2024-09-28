<?php
$filename = $_GET['filename'];

if (preg_match('/^[a-zA-Z0-9_.]+$/', $filename)) {
    $directory = "/path/to/directory/";

    $full_path = $directory . $filename;

    if (file_exists($full_path)) {
        $output = shell_exec("ls " . escapeshellcmd($full_path));
        echo "<pre>$output</pre>";
    } else {
        echo "Файл не найден!";
    }
} else {
    echo "Недопустимое имя файла!";
}
?>

