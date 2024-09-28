<!DOCTYPE html>
<html>
<head>
    <title>XSS Protected Page</title>
</head>
<body>
    <h1>Welcome to our website!</h1>
    <p>Hello, <?php echo htmlspecialchars($_GET['name']); ?>!</p>
</body>
</html>
