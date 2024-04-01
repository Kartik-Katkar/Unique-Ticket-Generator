<?php
require_once "config.php";

$username = $password = $confirm_password = $city = $state = $college = $pincode = "";
$username_err = $password_err = $confirm_password_err = $city_err = $state_err = $college_err = $pincode_err = "";

if ($_SERVER['REQUEST_METHOD'] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Username cannot be blank";
    }
    else{
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if($stmt)
        {
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set the value of param username
            $param_username = trim($_POST['username']);

            // Try to execute this statement
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1)
                {
                    $username_err = "This username is already taken"; 
                }
                else{
                    $username = trim($_POST['username']);
                }
            }
            else{
                echo "Something went wrong";
            }
        }
        mysqli_stmt_close($stmt);
    }

    

    
    // Check for city
if(empty(trim($_POST['city']))){
    $city_err = "City cannot be blank";
} else {
    $city = trim($_POST['city']);
}

// Check for referral
if(empty(trim($_POST['referral']))){
    $referral_err = "Referral cannot be blank";
} else {
    $referral = trim($_POST['referral']);
}

// Check for event
if(empty(trim($_POST['event']))){
    $event_err = "Event cannot be blank";
} else {
    $event = trim($_POST['event']);
}

    // Check for password
    if(empty(trim($_POST['password']))){
        $password_err = "Password cannot be blank";
    }
    elseif(strlen(trim($_POST['password'])) < 5){
        $password_err = "Password cannot be less than 5 characters";
    }
    else{
        $password = trim($_POST['password']);
        
        // Password strength meter
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            $password_err = "Password should be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character";
        }
    }

    // Check for confirm password field
    if(trim($_POST['password']) !=  trim($_POST['confirm_password'])){
        $password_err = "Passwords should match";
    }


    // If there were no errors, go ahead and insert into the database
if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($city_err) && empty($referral_err) && empty($event_err)) {
    $sql = "INSERT INTO users (username, password, city, referral, event) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_password, $param_city, $param_referral, $param_event);

        // Set these parameters
        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_DEFAULT);
        $param_city = $city;
        $param_referral = $referral;
        $param_event = $event;

        // Try to execute the query
        if (mysqli_stmt_execute($stmt)) {
            header("location: login.php");
        } else {
            echo "Something went wrong... cannot redirect!";
        }
    }
    mysqli_stmt_close($stmt);
}
    mysqli_close($conn);
}

    

    ?>


    <!DOCTYPE html>
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

        <!-- font awesome  -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <title>Register</title>

        <style>
.select-icon {
    position: relative;
    width: 100%;
}

.select-icon select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding: 0.5rem 2rem 0.5rem 1rem; /* Adjust padding for icon alignment */
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.2);
    color: white; /* Default text color */
    border: none;
    outline: none;
    width: 100%;
    font-size: 1rem; /* Increase font size for dropdown options */
}

.select-icon select:focus {
    color: black; /* Text color on focus */
}

.select-icon::after {
    content: "\25BC";
    font-family: 'Font Awesome 5 Free'; /* Set Font Awesome family */
    font-weight: 900; /* Set Font Awesome weight */
    font-size: 0.75rem;
    position: absolute;
    top: 50%;
    right: 1rem;
    transform: translateY(-50%);
    pointer-events: none;
}
    
    body {
                background: url('../images/1.png') center/cover no-repeat fixed;
            }

            .navbar {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            }

            .glassmorphism-card {
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(10px);
                border-radius: 25px;
            }

            .glassmorphism-form input,
            .glassmorphism-form button {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                border-radius: 15px;
                margin-bottom: 10px;
                color: white;
            }

            .glassmorphism-form input::placeholder {
                color: white;
            }

            
        </style>
    </head>

    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand navbar" href="#">ZENTICKET</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                    </li> -->
                    <li class="nav-item active">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- register  -->
        <section class="vh-100">
            <div class="container h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-lg-12 col-xl-11">
                        <div class="card text-white glassmorphism-card">
                            <div class="card-body p-md-5 glassmorphism-form">
                                <div class="row justify-content-center">
                                    <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">

                                        <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Sign up</p>

                                        <form class="mx-1 mx-md-4" action="" method="post">

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                                                <div class="form-outline flex-fill mb-0">
                                                    <input type="text" class="form-control" name="username"
                                                        placeholder=" Enter Username">
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <i class="fas fa-envelope fa-lg me-3 fa-fw"></i>
                                                <div class="form-outline flex-fill mb-0">
                                                    <input type="email" id="form3Example3c" class="form-control"
                                                        placeholder="Enter Email" />
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <i class="fas fa-lock fa-lg me-3 fa-fw"></i>
                                                <div class="form-outline flex-fill mb-0">
                                                    <input type="password" class="form-control" name="password"
                                                        placeholder="Enter Password">
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <i class="fas fa-key fa-lg me-3 fa-fw"></i>
                                                <div class="form-outline flex-fill mb-0">
                                                    <input type="password" class="form-control" name="confirm_password"
                                                        placeholder="Confirm Password">
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
    <i class="fas fa-map-marker-alt fa-lg me-3 fa-fw"></i>
    <div class="form-outline flex-fill mb-0">
        <input type="text" class="form-control" name="city" placeholder="Enter City">
    </div>
</div>

<div class="mb-4 select-icon d-flex flex-row align-items-center">
    <i class="fas fa-user-friends fa-lg me-3 fa-fw"></i>
    <select class="form-select" name="referral">
        <option selected disabled>Select Referral</option>
        <option value="Vedant">Vedant</option>
        <option value="Ajay">Ajay</option>
        <option value="Brochure">Brochure</option>
        <option value="TV ad">TV ad</option>
        <option value="Other">Other</option>
    </select>
</div>

<div class="mb-4 select-icon d-flex flex-row align-items-center">
    <i class="fas fa-calendar-alt fa-lg me-3 fa-fw"></i>
    <select class="form-select" name="event">
        <option selected disabled>Select Event</option>
        <option value="hackevent">Hackevent</option>
        <option value="Codeflix">Codeflix</option>
    </select>
</div>





                                            <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                                                <button type="submit" class="btn btn-outline-light btn-lg">Register</button>
                                            </div>

                                        </form>

                                    </div>
                                    <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">

                                        <img src="../images/log.gif" class="img-fluid" alt="Sample image">

                                    </div>
                                </div>
                                <?php if(!empty($username_err) || !empty($password_err)): ?>
                                <div class="alert alert-danger mt-4">
                                    <ul>
                                        <?php if(!empty($username_err)): ?>
                                        <li><?php echo $username_err; ?></li>
                                        <?php endif; ?>
                                        <?php if(!empty($password_err)): ?>
                                        <li><?php echo $password_err; ?></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- register ends  -->
        <!-- footer  -->
        <footer class="bg-dark text-center text-white">

            <!-- Copyright -->
            <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
                © 2024 Copyright:
                <a class="text-white" href="https://mdbootstrap.com/">Built For ZENTICKET</a>
            </div>
            <!-- Copyright -->
        </footer>
        <!-- footer ends  -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>
    </body>

    </html>