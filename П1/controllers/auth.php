<?php

include 'db.php';

$login = $_POST['login'];
$pass = $_POST['pass'];
$submit = $_POST['submit'];

if ($submit) {
    
    $user = $pdo -> query("SELECT * FROM `users` WHERE login='$login'");

    while ($row = $user->fetch())
    {
    
        if($row['password'] == $pass) {
            $_SESSION['user'] = array(
                'name' => $row['name'],
                'login' => $row['login'],
                'role' => $row['role'],
            );

                if ($row['role'] == 1) {
                    header('Location: ../admin/index.php');
                }
                else {
                    header('Location: /');
                }
            }
            else {
                header('Location: /');
            }
        }
       
    }
else {
    header('Location: /');
}






?>