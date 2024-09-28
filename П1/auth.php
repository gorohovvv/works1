<?php
include 'assets/parts/header.php';
?>

<form action="controllers/auth.php" method="POST">
    <input name="login" type="text">
    <input name="pass" type="password">
    <input name="submit" type="submit">
</form>

<?php
include 'assets/parts/footer.php';
?>