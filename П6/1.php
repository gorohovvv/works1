<!DOCTYPE html>
<html>
<head>
    <title>XSS Vulnerable Page</title>
</head>
<body>
    <h1>Welcome to our website!</h1>
    <p>Hello, <?php echo $_GET['name']; ?>!</p>
</body>
</html>
