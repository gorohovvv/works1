<?php
include 'controllers/db.php';
include 'assets/parts/header.php';
echo '<h1>Main</h1>';
if ($_SESSION['user']) {
    echo $_SESSION['user']['name'];
}
include 'assets/parts/footer.php';
?>