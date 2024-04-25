<?php

session_start();

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !==true)
{
    header("location: login.php");
    exit;
}

require_once "config.php";

// Function to fetch all users
function getAllUsers($conn) {
    $users = array();
    $sql = "SELECT id, username FROM users";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    return $users;
}

// Function to create a new user
function createUser($conn, $username, $password, $city, $referral, $event) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, city, referral, event) VALUES (?, ?, ?, ?, ?)"; // Include event in the query
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $username, $hashed_password, $city, $referral, $event); // Bind parameters for event
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
}


// Function to update user details
function updateUser($conn, $userId, $newUsername) {
    $sql = "UPDATE users SET username = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $newUsername, $userId);
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
}

// Function to delete a user
function deleteUser($conn, $userId) {
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
}

// Function to create a new event
function createEvent($conn, $name, $date, $time, $price) {
    $sql = "INSERT INTO eventdata (name, date, time, price) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $name, $date, $time, $price);
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
}

// Function to update event details
function updateEvent($conn, $eventId, $name, $date, $time, $price) {
    $sql = "UPDATE eventdata SET name = ?, date = ?, time = ?, price = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssii", $name, $date, $time, $price, $eventId);
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
}

// Function to delete an event
function deleteEvent($conn, $eventId) {
    $sql = "DELETE FROM eventdata WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $eventId);
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
}

// Fetch all events
function getAllEvents($conn) {
    $events = array();
    $sql = "SELECT id, name FROM eventdata";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
    return $events;
}

// Fetch all events
$events = getAllEvents($conn);

// Handle form submissions for events
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_event'])) {
        // Code to create event (already present)
    } elseif (isset($_POST['update_event'])) {
        $eventId = $_POST['event_id'];
        $eventName = $_POST['updated_event_name'];
        $eventDate = $_POST['updated_event_date'];
        $eventTime = $_POST['updated_event_time'];
        $eventPrice = $_POST['updated_event_price'];
        if (updateEvent($conn, $eventId, $eventName, $eventDate, $eventTime, $eventPrice)) {
            // Event updated successfully
            header("Location: admin.php");
            exit;
        } else {
            // Error updating event
            $errorMessage = "Error updating event.";
        }
    } elseif (isset($_POST['delete_event'])) {
        $eventId = $_POST['event_id_delete'];
        if (deleteEvent($conn, $eventId)) {
            // Event deleted successfully
            header("Location: admin.php");
            exit;
        } else {
            // Error deleting event
            $errorMessage = "Error deleting event.";
        }
    }
}


// Fetch all users
$users = getAllUsers($conn);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['create_user'])) {
            $newUsername = $_POST['new_username'];
            $newPassword = $_POST['new_password'];
            $city = $_POST['city']; // Capture city value
            $referral = $_POST['referral']; // Capture referral value
            $event = $_POST['event']; // Capture event value
            if (createUser($conn, $newUsername, $newPassword, $city, $referral, $event)) { // Pass event as argument
                // User created successfully
                header("Location: admin.php");
                exit;
            } else {
                // Error creating user
                $errorMessage = "Error creating user.";
            }        
        }
        elseif (isset($_POST['update_user'])) {
        $userId = $_POST['user_id'];
        $newUsername = $_POST['username'];
        if (updateUser($conn, $userId, $newUsername)) {
            // User updated successfully
            header("Location: admin.php");
            exit;
        } else {
            // Error updating user
            $errorMessage = "Error updating user.";
        }
    } elseif (isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];
        if (deleteUser($conn, $userId)) {
            // User deleted successfully
            header("Location: admin.php");
            exit;
        } else {
            // Error deleting user
            $errorMessage = "Error deleting user.";
        }
    }
}

// Fetch data for analytics
// Count of users registered this month
$currentMonth = date('Y-m');
$sql = "SELECT COUNT(*) AS userCount FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $currentMonth);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $userCount);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Fetch event-wise distribution of registrations
$sql = "SELECT event, COUNT(*) AS registrationCount FROM users GROUP BY event";
$result = mysqli_query($conn, $sql);
$eventWiseData = array();
while ($row = mysqli_fetch_assoc($result)) {
    $eventWiseData[] = $row;
}
mysqli_free_result($result);


// Time of day with maximum registrations
$sql = "SELECT HOUR(created_at) AS registrationHour, COUNT(*) AS registrationCount FROM users GROUP BY HOUR(created_at)";
$result = mysqli_query($conn, $sql);
$registrationData = array();
while ($row = mysqli_fetch_assoc($result)) {
    $registrationData[] = $row;
}
mysqli_free_result($result);

// Day-wise distribution of registrations
$sql = "SELECT DAYNAME(created_at) AS registrationDay, COUNT(*) AS registrationCount FROM users GROUP BY DAYNAME(created_at)";
$result = mysqli_query($conn, $sql);
$dayWiseData = array();
while ($row = mysqli_fetch_assoc($result)) {
    $dayWiseData[] = $row;
}
mysqli_free_result($result);

// City-wise distribution of registrations
$sql = "SELECT city, COUNT(*) AS registrationCount FROM users GROUP BY city";
$result = mysqli_query($conn, $sql);
$cityWiseData = array();
while ($row = mysqli_fetch_assoc($result)) {
    $cityWiseData[] = $row;
}
mysqli_free_result($result);

// Referral-wise distribution of registrations
$sql = "SELECT referral, COUNT(*) AS registrationCount FROM users GROUP BY referral";
$result = mysqli_query($conn, $sql);
$referralWiseData = array();
while ($row = mysqli_fetch_assoc($result)) {
    $referralWiseData[] = $row;
}
mysqli_free_result($result);


?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="/favicon.ico" href="../images/tickett.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Admin Panel</title>
    </head>

    <body>
      

        <style>

        body {
        background-image: url('../images/bg.png');
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
    }
        .navbar {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            }

                /* Glassmorphism effect */
    .glassmorphism {
        background-color: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        text-align: center; /* Center align contents */
    }
    .glassmorphism button {
        margin-top: 20px; /* Add space between form and buttons */
        background-color: white;
        color: black;
        border: 1px solid black;
        border-radius: 0;
        font-family: 'keyboard', sans-serif; /* Assuming 'keyboard' is a valid font */
    }

    /* Style the buttons on hover */
    .glassmorphism button:hover {
        background-color: black;
        color: white;
    }

    /* Form styling */
    .form-group {
        margin-bottom: 20px;
    }

        /* Form styling */
        .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        border-radius: 5px;
    }

    .btn {
        border-radius: 5px;
    }
        </style>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand navbar" href="#">ZENTICKET</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="#"> <img src="https://img.icons8.com/metro/26/000000/guest-male.png">
                                <?php echo "Welcome ". $_SESSION['username']?></a>
                        </li>
                    </ul>
                </div>


            </div>
        </nav>

        <br>
        <hr>
        <div class="container mt-4 text-center">
            <h3><?php echo "Welcome ". $_SESSION['username']?> </h3>
            
        </div>
        <hr>

        <!-- added content for crud  -->
        <div class="container mt-5">
        
            
        <div class="glassmorphism">
        <h3>Create New User</h3>
        <form method="post" action="">
            <div class="form-group">
                <label for="new_username">Username:</label>
                <input type="text" class="form-control" id="new_username" name="new_username" required>
            </div>
            <div class="form-group">
                <label for="new_password">Password:</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" class="form-control" id="city" name="city">
            </div>

            <div class="form-group">
                <label for="referral">Referral:</label>
                <input type="text" class="form-control" id="referral" name="referral">
            </div>

            <div class="form-group">
                <label for="event">Event:</label>
                <input type="text" class="form-control" id="event" name="event" value="Codeflix">
            </div>


            <button type="submit" class="btn btn-primary" name="create_user">Create User</button>
        </form>
        </div>

        <hr>
        <div class="glassmorphism">
        <h3>Update User</h3>
        <form method="post" action="">
            <div class="form-group">
                <label for="user_id">Select User:</label>
                <select class="form-control" id="user_id" name="user_id">
                    <?php foreach ($users as $user) : ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="username">New Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <button type="submit" class="btn btn-primary" name="update_user">Update User</button>
        </form>
        </div>

        <hr>
        <div class="glassmorphism">
        <h3>Delete User</h3>
        <form method="post" action="">
            <div class="form-group">
                <label for="user_id">Select User:</label>
                <select class="form-control" id="user_id" name="user_id">
                    <?php foreach ($users as $user) : ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-danger" name="delete_user">Delete User</button>
        </form>
        
        </div>

        <div class="glassmorphism">
    <h3>Create New Event</h3>
    <form method="post" action="">
        <div class="form-group">
            <label for="event_name">Event Name:</label>
            <input type="text" class="form-control" id="event_name" name="event_name" required>
        </div>
        <div class="form-group">
            <label for="event_date">Event Date:</label>
            <input type="date" class="form-control" id="event_date" name="event_date" required>
        </div>
        <div class="form-group">
            <label for="event_time">Event Time:</label>
            <input type="time" class="form-control" id="event_time" name="event_time" required>
        </div>
        
        <div class="form-group">
            <label for="event_price">Event Price:</label>
            <input type="number" class="form-control" id="event_price" name="event_price" required>
        </div>
        <button type="submit" class="btn btn-primary" name="create_event">Create Event</button>
    </form>
</div>

<div class="glassmorphism">
    <h3>Update Event</h3>
    <form method="post" action="">
    <div class="form-group">
    <label for="event_id">Select Event:</label>
    <select class="form-control" id="event_id" name="event_id">
        <?php foreach ($events as $event) : ?>
            <option value="<?php echo $event['id']; ?>"><?php echo $event['name']; ?></option>
        <?php endforeach; ?>
    </select>
</div>

        <div class="form-group">
            <label for="updated_event_name">New Event Name:</label>
            <input type="text" class="form-control" id="updated_event_name" name="updated_event_name" required>
        </div>
        <div class="form-group">
            <label for="updated_event_date">New Event Date:</label>
            <input type="date" class="form-control" id="updated_event_date" name="updated_event_date" required>
        </div>
        <div class="form-group">
            <label for="updated_event_time">New Event Time:</label>
            <input type="time" class="form-control" id="updated_event_time" name="updated_event_time" required>
        </div>
        <div class="form-group">
            <label for="updated_event_price">New Event Price:</label>
            <input type="number" class="form-control" id="updated_event_price" name="updated_event_price" required>
        </div>
        <button type="submit" class="btn btn-primary" name="update_event">Update Event</button>
    </form>
</div>


<div class="glassmorphism">
    <h3>Delete Event</h3>
    <form method="post" action="">
        <div class="form-group">
            <label for="event_id_delete">Select Event:</label>
            <select class="form-control" id="event_id_delete" name="event_id_delete">
                <?php foreach ($events as $event) : ?>
                    <option value="<?php echo $event['id']; ?>"><?php echo $event['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-danger" name="delete_event">Delete Event</button>
    </form>
    
</div>


        <?php if (isset($errorMessage)) : ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
    </div>
        <!-- done content for crud  -->
        <br><br><br>
        <!-- Display analytics -->
        <div class="glassmorphism">
<div class="container mt-5">
    <h3>Analytics</h3>
    
    <div>
        <h5>Number of Users Registered This Month: <?php echo $userCount; ?></h5>
    </div>

    <div class="row justify-content-center">
    <div class="col-md-6">
        <h5 class="text-center">Time of Day with Registrations</h5>
        <canvas id="registrationTimeChart" width="200" height="200"></canvas>
    </div>

    <div class="col-md-6">
    <h5 class="text-center">Event-wise Distribution of Registrations</h5>
    <canvas id="registrationEventChart" width="200" height="200"></canvas>
    </div>
    </div>

    <div class="row justify-content-center">
    <div class="col-md-6">
        <h5 class="text-center">Day-wise Distribution of Registrations</h5>
        <canvas id="registrationDayChart" width="200" height="200"></canvas>
    </div>
    </div>

    <div class="row justify-content-center">
    <div class="col-md-6">
        <h5 class="text-center">City-wise Distribution of Registrations</h5>
        <canvas id="cityRegistrationChart" width="200" height="200"></canvas>
    </div>
    <div class="col-md-6">
        <h5 class="text-center">Referral-wise Distribution of Registrations</h5>
        <canvas id="referralRegistrationChart" width="200" height="200"></canvas>
    </div>
    <h3><div class="container mt-4 text-center" id="generated-text"></h3>
    </div>
</div>

</div>
        </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script type="importmap">
    {
      "imports": {
        "@google/generative-ai": "https://esm.run/@google/generative-ai"
      }
    }
  </script>
  <script type="module">
    import { GoogleGenerativeAI } from "@google/generative-ai";

    // Replace "... with your actual API key from Google AI Studio
    const API_KEY = "AIzaSyAumIRKhFyHl47hcBpBUn_4OnorT_nY0qo";

    const genAI = new GoogleGenerativeAI(API_KEY);

    const generateButton = document.getElementById("generate-button");
    const promptInput = document.getElementById("prompt-input");
    const generatedText = document.getElementById("generated-text");

    async function generateText() {
    //   const prompt = promptInput.value;
    const prompt = "Generate analytics based on the data collected from the database Number of Users Registered This Month: <?php echo $userCount; ?>. Include the following:Provide insights on ticket sales based on factors like current temperature, city, time of the day, etc Analyze the impact of variables such as city, time of the day, and weather conditions on ticket purchases.Offer recommendations for optimizing ticket sales based on the identified patterns. give a simple summary in one paragraph"

      // For text-only input, use the gemini-pro model
      const model = genAI.getGenerativeModel({ model: "gemini-1.0-pro" });

      const generationConfig = {
      temperature: 0.9,
      topK: 1,
      topP: 1,
      maxOutputTokens: 2048,
    };

      const modelWithConfig = genAI.getGenerativeModel({ model: "gemini-1.0-pro", generationConfig });

      const result = await modelWithConfig.generateContent(prompt);
      const response = await result.response;
      const text = response.text();
      generatedText.innerText = text;
    }
    generateText();
  </script>
<script>

    
    // JavaScript code to render the time of day chart
    var ctx1 = document.getElementById('registrationTimeChart').getContext('2d');
    var registrationTimeChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($registrationData, 'registrationHour')); ?>,
            datasets: [{
                label: 'Registrations',
                data: <?php echo json_encode(array_column($registrationData, 'registrationCount')); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Registrations'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Hour of the Day'
                    }
                }
            }
        }
    });

    // javascript code to render the event-wise distribution chart
    var ctx3 = document.getElementById('registrationEventChart').getContext('2d');
    var registrationEventChart = new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($eventWiseData, 'event')); ?>,
            datasets: [{
                label: 'Registrations',
                data: <?php echo json_encode(array_column($eventWiseData, 'registrationCount')); ?>,
                backgroundColor: 'rgba(255, 159, 64, 0.5)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Registrations'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Event'
                    }
                }
            }
        }
    });

    // JavaScript code to render the day-wise distribution chart
    var ctx2 = document.getElementById('registrationDayChart').getContext('2d');
    var registrationDayChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_column($dayWiseData, 'registrationDay')); ?>,
            datasets: [{
                label: 'Registrations',
                data: <?php echo json_encode(array_column($dayWiseData, 'registrationCount')); ?>,
                backgroundColor: [
                    'rgba(255, 255, 0, 0.5)',       
'rgba(128, 0, 128, 0.5)',       
'rgba(0, 255, 255, 0.5)',       
'rgba(255, 165, 0, 0.5)',       
'rgba(128, 128, 128, 0.5)',     
'rgba(255, 192, 203, 0.5)',     
                ],
                borderColor: [
                    'rgba(255, 255, 0, 0.5)',       
'rgba(128, 0, 128, 0.5)',       
'rgba(0, 255, 255, 0.5)',       
'rgba(255, 165, 0, 0.5)',       
'rgba(128, 128, 128, 0.5)',     
'rgba(255, 192, 203, 0.5)'     
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Registrations'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Day of the Week'
                    }
                }
            }
        }
    });

    // JavaScript code to render the city-wise distribution chart
var ctx3 = document.getElementById('cityRegistrationChart').getContext('2d');
var cityRegistrationChart = new Chart(ctx3, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($cityWiseData, 'city')); ?>,
        datasets: [{
            label: 'Registrations',
            data: <?php echo json_encode(array_column($cityWiseData, 'registrationCount')); ?>,
            backgroundColor: [
                'rgba(0, 255, 0, 0.5)',         
'rgba(255, 0, 255, 0.5)',       
'rgba(0, 0, 128, 0.5)',         
'rgba(128, 0, 0, 0.5)',         
'rgba(255, 215, 0, 0.5)',       
'rgba(255, 69, 0, 0.5)'         
            ],
            borderColor: [
                'rgba(0, 255, 0, 1)',         
'rgba(255, 0, 255, 1)',       
'rgba(0, 0, 128, 1)',         
'rgba(128, 0, 0, 1)',         
'rgba(255, 215, 0, 1)',       
'rgba(255, 69, 0, 1)'         
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Number of Registrations'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'City'
                }
            }
        }
    }
});

// JavaScript code to render the referral-wise distribution chart
var ctx4 = document.getElementById('referralRegistrationChart').getContext('2d');
var referralRegistrationChart = new Chart(ctx4, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($referralWiseData, 'referral')); ?>,
        datasets: [{
            label: 'Registrations',
            data: <?php echo json_encode(array_column($referralWiseData, 'registrationCount')); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',   
'rgba(54, 162, 235, 0.8)',
'rgba(255, 206, 86, 0.8)',
'rgba(75, 192, 192, 0.8)',
'rgba(153, 102, 255, 0.8)',
'rgba(255, 159, 64, 0.8)', 
            ],
            borderColor: [
                'rgba(255, 99, 132, 0.8)',    
'rgba(54, 162, 235, 0.8)',    
'rgba(255, 206, 86, 0.8)',    
'rgba(75, 192, 192, 0.8)',    
'rgba(153, 102, 255, 0.8)',   
'rgba(255, 159, 64, 0.8)',    
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Number of Registrations'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Referral Source'
                }
            }
        }
    }
});

</script>
        <br><br><br>

        <!-- footer starts  -->
        <footer class="bg-dark text-center text-white">
            

            <!-- Copyright -->
            <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
                © 2024
                <a class="text-white">Built For WTCC</a>
            </div>
            <!-- Copyright -->
        </footer>
        <!-- footer ends  -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
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
