<?php
session_start();

if(isset($_SESSION['form_data'])) {
    $formData = $_SESSION['form_data'];
    unset($_SESSION['form_data']); 
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.6/inputmask.min.js"></script>
</head>

<body>
    <style>
        form {
            display: flex;
            gap: 10px;
            flex-direction: column;
            max-width: 200px;
        }
    </style>
    <form action="reg.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
        <input type="text" name="name" id="name" placeholder="Name" value="<?php echo isset($formData['name']) ? $formData['name'] : ''; ?>">
        <div id="nameError" style="color: red;">
        <?php
            if(isset($_GET['error']) && $_GET['error'] == 'name_exists') {
                echo "<p style='color: red;'>Name already exists, please choose a different name.</p>";
            }
        ?>
        </div>
        <input type="email" name="email" id="email" placeholder="Email" value="<?php echo isset($formData['email']) ? $formData['email'] : ''; ?>">
        <div id="emailError" style="color: red;"></div>
        <input type="password" name="password" id="password" placeholder="Password" value="<?php echo isset($formData['password']) ? $formData['password'] : ''; ?>">
        <div id="passwordError" style="color: red;"></div>
        <input type="password" name="password_c" id="password_c" placeholder="Password confirm" value="<?php echo isset($formData['password_c']) ? $formData['password_c'] : ''; ?>">
        <div id="password_cError" style="color: red;"></div>
        <input type="text" name="phone" id="phone" placeholder="Phone" value="<?php echo isset($formData['phone']) ? $formData['phone'] : ''; ?>">
        <div id="phoneError" style="color: red;"></div>
        <input type="date" name="date" id="date" placeholder="Date of Birth" value="<?php echo isset($formData['date']) ? $formData['date'] : ''; ?>">
        <div id="dateError" style="color: red;"></div>
        <label for="">Выберите пол</label>
        <p>М</p>
        <input type="radio" name="gender" id="man" value="m" <?php if($formData['gender'] == "m") {echo "checked";} ?>>
        <p>Ж</p>
        <input type="radio" name="gender" id="woman" value="w" <?php if($formData['gender'] == "w") {echo "checked";} ?>>
        <div id="genderError" style="color: red;"></div>
        <input type="file" name="image" id="image" value="<?php echo isset($formData['image']) ? $formData['image'] : ''; ?>">
        <div id="imageError" style="color: red;"></div>
        <input type="submit" id="submit" value="Submit">
    </form>

    <script src="/validation.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/inputmask"></script>
</body>

</html>