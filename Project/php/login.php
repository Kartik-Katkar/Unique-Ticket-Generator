<?php
session_start();

if(isset($_SESSION['username']))
{
    header("location: welcome.php");
    exit;
}
require_once "config.php";

if (!$conn) {
    $err = "Connection failed: " . mysqli_connect_error();
}

$username = $password = "";
$err = "";

// if request method is post
if ($_SERVER['REQUEST_METHOD'] == "POST"){
    if(empty(trim($_POST['username'])) || empty(trim($_POST['password'])))
    {
        $err = "Please enter username + password";
    }
    else{
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
    }


    if(empty($err))
    {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = $username;


        // Try to execute this statement
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 1)
            {
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                if(mysqli_stmt_fetch($stmt))
                {
                    if(password_verify($password, $hashed_password))
                    {
                        // this means the password is correct. Allow user to login
                        session_start();
                        $_SESSION["username"] = $username;
                        $_SESSION["id"] = $id;
                        $_SESSION["loggedin"] = true;

                        //Redirect user to welcome page
                        if ($username == "admin" && $password == "Admin@12") {
                            header("location: admin.php");
                        } else {
                            header("location: welcome.php");
                        }
                        exit;
                    }
                    else
                    {
                        $err = "Invalid password";
                    }
                }
            }
            else
            {
                $err = "Invalid username";
            }
        }
        else
        {
            $err = "Something went wrong. Please try again later.";
        }
    }
}


?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- for icons  -->
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <title>Login</title>
</head>

<body>
<style>
    
    .navbar {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        }
</style>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar navbar-brand" href="#">ZENTICKET</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Register</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
            </ul>
        </div>
    </nav>

    <section class="background-radial-gradient overflow-hidden" style="margin-top: 70px;">
        <style>

        .bg-glass {
            background-color: hsla(0, 0%, 100%, 0.9) !important;
            backdrop-filter: saturate(200%) blur(25px);
        }

        .navbar{
            opacity: 0.7 !important;
        }

        body {
        background-image: url('../images/login.png');
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
        /* Add other styles as needed */
    }
        </style>

        <div class="container px-4 py-5 px-md-5 text-center text-lg-center my-5">
            <div class="row gx-lg-5 align-items-center mb-5">
                
                <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                    <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
                        Download Your Ticket <br />
                        <span style="color: hsl(218, 81%, 75%)">For the Event</span>
                    </h1>
                    <h6 class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
                    
                     Get ready for an exciting CodeFlix Hackathon experience! Your personalized ticket is your key to a day packed with coding challenges, innovation, and networking. Keep it accessible on your mobile or in print.

                     Spread the word among your fellow participants and use our official event hashtag #CodeFlixHackathon on social media. Let's make this event unforgettable! For any ticket-related issues or questions, reach out to our support team at [Support Email/Phone]. Enjoy the hackathon!
                    </h6>
                </div>

                <div class="col-lg-6 mb-5 mb-lg-0 position-relative">                

                    <div class="card bg-glass">
                        <div class="card-body px-4 py-5 px-md-5">
                            <form action="" method="post">

                                <!-- Error message -->
                                <?php if(!empty($err)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $err; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Username input -->
                                <div class="form-outline mb-4">
                                    <label for="exampleInputEmail1">Username</label>
                                    <input type="text" name="username" class="form-control" id="exampleInputEmail1"
                                        aria-describedby="emailHelp" placeholder="Enter Username">
                                </div>

                                <!-- Password input -->
                                <div class="form-outline mb-4">
                                    <label for="exampleInputPassword1">Password</label>
                                    <input type="password" name="password" class="form-control"
                                        id="exampleInputPassword1" placeholder="Enter Password">
                                </div>


                                <!-- Submit button -->
                                <button type="submit" class="btn btn-outline-dark btn-lg">Login </button>
                            </form>
                            <a href="forgot_password.php">Forgot Password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>