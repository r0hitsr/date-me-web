<?php
session_start();
require_once("./Connector/DbConnectorPDO.php");
include("./helper/helperFunctions.php");

$connection = getConnection();
$userId = isset($_SESSION["userId"]) && !empty($_SESSION["userId"]) ? $_SESSION["userId"] : 0;

if ($userId !== 0) {
    header("Location: ./index.php");
    exit(); // Prevent further script execution
}

$errors = array();

if (isset($_POST['Submit'])) {
    $email = $_POST['email'];
    $password = $_POST["password"];

    if (!IsVariableIsSetOrEmpty($email) && !IsVariableIsSetOrEmpty($password)) {
        try {
            $query = "SELECT * FROM profile WHERE email = :email AND password = :password";
            $stmt = $connection->prepare($query);

            // Bind parameters
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password, PDO::PARAM_STR); // Assuming password is not hashed

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) { // Check if no rows were returned
                array_push($errors, 'Incorrect Email / Password');
            } else {
                $_SESSION['userId'] = $row['id'];
                $_SESSION['user'] = $row;

                if (isset($_SESSION['userId'])) {
                    header("Location: index.php");
                    exit(); // Prevent further script execution
                }
            }
        } catch (PDOException $e) {
            array_push($errors, "Database error: " . $e->getMessage());
        }
    } else {
        array_push($errors, "Email and password are required.");
    }
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Page</title>
    <?php include("./includes/header.php") ?>
    <link rel="icon" href="../datingapp/images/site_images/3.webp" width="40" height="40" style="border-radius: 50%;" alt="logo">
    <link href="./css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container-fluid wrapper">
    <?php
    include("./includes/nav-bar.php")
    ?>

    <div class="container-fluid ">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-10 col-xl-9 mx-auto">
                        <div class="row mt-5">
                            <?php
                            // if any errors are there display them
                            if (count($errors) > 0) {
                                ?>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="alert alert-danger" role="alert">
                                            <?php
                                            foreach ($errors as $error) { ?>
                                                <li><?= $error ?></li>
                                            <?php } ?>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                    <div class="card card-signin flex-row my-5">
                        <div class="login-card-img-left d-none d-md-flex">
                            <!-- Background image for card set in CSS! -->
                        </div>

                        <div class="card-body">
                            <h5 class="card-title text-center">Login</h5>
                            <form class="form-signin" action="login.php" method="post" enctype="multipart/form-data">
                                <div class="form-label-group">
                                    <input type="text" id="inputUserame" name="email" pattern="^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$" class="form-control" placeholder="Username" required autofocus>
                                    <label for="inputUserame">Username</label>
                                </div>
                               <hr>

                                <div class="form-label-group">
                                    <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
                                    <label for="inputPassword">Password</label>
                                </div>

                                <button class="btn btn-lg btn-primary btn-block text-uppercase" name="Submit" type="submit">Login</button>
                                <div class="sign-up">
                                    Don't have an account? <a href="./register.php">Register</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <?php include("./includes/footer.php") ?>
    <!-- end of footer -->
</div>

</body>
</html>