<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=my_database', 'username', 'password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
    exit();
}

$userInput = $_POST['user_input']; 

$sql = "SELECT * FROM users WHERE username = '$userInput'"; 

$result = $pdo->query($sql);

if ($result) {
    foreach ($result as $row) {
        echo "Username: " . $row['username'] . "<br>";
        echo "Email: " . $row['email'] . "<br>";
    }
} else {
    echo "Пользователь не найден.";
}
?>
