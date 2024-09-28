<?php
session_start(); 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mydb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (($name) || ($email) || ($_POST['password']) || ($_POST['password_c']) || ($_POST['phone']) || ($_POST['date']) || ($_POST['gender']) || ($_POST['image'])) {
        $name = $_POST['name'];

        if (preg_match('/\d/', $name)) {
            $_SESSION['form_data'] = $_POST;
            exit();
        }
        else {
            $sql = "SELECT * FROM user WHERE name = '$name'";
        $result = $conn->query($sql);
    
        if ($result->num_rows > 0) {
            $_SESSION['form_data'] = $_POST;
            exit();
        }
        else {
            $email = $_POST['email'];
            if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                $_SESSION['form_data'] = $_POST;
                exit();
            }
            else {
                $password = $_POST['password'];
                $currentMonth = date('F'); 
                $passwordRegex = "/^(?=.{8,})" . 
                "(?!.*(.).*\\1)" . 
                "(?=.*\\W.*\\W)" . 
                "(?=.*" . preg_quote($currentMonth) . ")". 
                "(?=.*[a-zA-Z])" .
                "(?=.*[а-яА-Я])" . 
                "(?=.*[^\w\s])" . 
                "(?!.*\\W{3,})/i"; 



                                
                if (!preg_match($passwordRegex, $password)) {
                    $_SESSION['form_data'] = $_POST;
                    exit();
                }
                else {

                    if ($_POST['password'] !== $_POST['password_c']) {
                        $_SESSION['form_data'] = $_POST;
                    
                        exit();
                    }
                    else {

                        $dateOfBirth = $_POST['date'];
                        $currentDate = date('Y-m-d');
                        $minDate = date('Y-m-d', strtotime('-111 years'));
                        
                        if ($dateOfBirth > $currentDate || $dateOfBirth < $minDate) {
                            $_SESSION['form_data'] = $_POST;
                          
                            exit();
                        }
                        else {

                            $imageName = $_FILES['image']['name'];
                            $imageSize = $_FILES['image']['size'];
                            $imageType = $_FILES['image']['type'];

                            if (strlen($imageName) > 15) {
                                $_SESSION['form_data'] = $_POST;
                              
                                exit();
                            }
                            else {
                                if ($imageType !== 'image/png') {
                                    $_SESSION['form_data'] = $_POST;
                                  
                                    exit();
                                }
                                else {
                                    if ($imageSize < 1024 || $imageSize > 1024 * 1024 * 10) {
                                        $_SESSION['form_data'] = $_POST;
                                       
                                        exit();
                                    }
                                    else {
                                        $birthdate_timestamp = strtotime($_POST['date']);
                                        $age = floor((time() - $birthdate_timestamp) / 31556926); 
                                        if ($age > 18) {
                                            if ($_POST['gender'] == "m") {
                                                $gender = "Мужчина";
                                            }
                                            else {
                                                $gender = "Женщина";
                                            }
                                        }
                                        else {
                                            if ($_POST['gender'] == "m") {
                                                $gender = "Мальчик";
                                            }
                                            else {
                                                $gender = "Девочка";
                                            }
                                        }
                                
                                        $sql = "INSERT INTO `user`(`name`, `password`, `email`, `phone`, `date`, `gender`, `image`) VALUES ('$_POST[name]','$_POST[password]','$_POST[email]','$_POST[phone]','$_POST[date]','$_POST[gender]','$_POST[image]')";
                                
                                        if ($conn->query($sql) === TRUE) {
                                            header("Location: success.php");
                                            exit();
                                        } else {
                                            echo "Error: " . $sql . "<br>" . $conn->error;
                                        }
                                
                                        $conn->close();
                                    }
                                }
                            }

                        }

                    }

                }
            }
                }
        }
    }
    else {
        $_SESSION['form_data'] = $_POST;
       
        exit();
    }
}
else {
    $_SESSION['form_data'] = $_POST;
   
    exit();
}
?>
