<?php
// Include the database configuration file
require_once 'config.php';

// Fetch all events from the database
$sql = "SELECT * FROM eventdata";
$result = mysqli_query($conn, $sql);

// Check if there are any events
if (mysqli_num_rows($result) > 0) {
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
    <title>Events</title>
    <style>
        body {
                background: url('../images/2.png') center/cover no-repeat fixed;
                
            }
        /* Glassmorphism effect */
        .glass-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 20px;
            margin: 20px auto;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: rgba(0, 0, 0, 0.4);
            /* background: rgba(255, 255, 255, 0.15); */
            /* backdrop-filter: blur(10px); */
        }

        .pay-button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#">ZENTICKET</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
            </ul>
        </div>
    </nav>
<br><br><br><br>
<br><br><br><br>
    <!-- Content -->
    <section class="background-radial-gradient" style="margin-top: 70px;">
        <div class="glass-container">
            <h2 class="text-center">Events</h2>
            <table>
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through each row of event data
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['time']; ?></td>
                            <td><?php echo $row['price']; ?></td>
                            <!-- <td><button class="pay-button">Pay Now</button></td> -->
                            <td><button class="pay-button" onclick="pay('<?php echo $row['name']; ?>', '<?php echo $row['price']; ?>')">Pay Now</button></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
        <!-- Razorpay -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        function pay(eventName, amount) {
            var options = {
                "key": "YOUR_RAZORPAY_KEY_ID",
                "amount": amount * 100, // Amount is in currency subunits. Here, it's paise.
                "currency": "INR",
                "name": "ZenTicket",
                "description": "Payment for " + eventName,
                "image": "https://cdn.icon-icons.com/icons2/2429/PNG/512/zendesk_logo_icon_147198.png",
                "handler": function (response) {
                    // Redirect to register1.php on successful payment
                    window.location.href = 'register1.php';
                },
                "prefill": {
                    "name": "Zenticket",
                    "email": "Zenticket@gmail.com",
                    "contact": "YOUR_CONTACT_NUMBER"
                },
                "notes": {
                    "address": "Pune"
                },
                "theme": {
                    "color": "#3399cc"
                }
            };
            var rzp = new Razorpay(options);
            rzp.open();
        }
    </script>
</body>

</html>

    <?php
} else {
    echo "No events found";
}

// Close the database connection
mysqli_close($conn);
?>