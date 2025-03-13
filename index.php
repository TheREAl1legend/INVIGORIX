<?php

session_start();
if (isset($_SESSION["user"])) {
    if (empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

include("../connection.php");

$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
if ($userrow->num_rows > 0) {
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Invigorix Dashboard</title>
   
    <style>
    /* Sidebar Styling */
    .sidebar {
        height: 100%;
        width: 250px;
        position: fixed;
        top: 0;
        right: -250px; /* Hidden off-screen initially */
        background-color: #f8f9fa;
        padding-top: 20px;
        transition: right 0.3s ease;
        overflow-y: auto; /* Add scrolling if sidebar content overflows */
    }

    .sidebar.open {
        right: 0; /* Show sidebar when open */
    }

    /* Sidebar Close Button */
    .close-sidebar-btn {
        display: flex;
        justify-content: flex-end;
        padding-right: 20px;
        font-size: 20px;
        cursor: pointer;
        color: #0d6efd;
        background-color: transparent;
        border: none;
        margin-bottom: 20px;
    }

    /* Sidebar Menu Button Styling */
    .menu-btn {
        padding: 15px;
        cursor: pointer;
        display: flex; /* Flexbox for icon and text alignment */
        align-items: center; /* Vertically align items */
    }

    .menu-btn:hover {
        background-color: #d1e7dd; /* Lighter hover effect */
    }

    .menu-active {
        background-color: #0d6efd; /* Active background color */
        color: white; /* Active text color */
    }

    .menu-icon {
        width: 24px; /* Standard width for icons */
        height: 24px;
        margin-right: 10px; /* Space between icon and text */
    }

    .menu-text {
        color: inherit;
        font-size: 16px;
        white-space: nowrap; /* Prevent text from wrapping */
    }

    /* Navbar Styling */
    .navbar {
        background-color: #001B3D;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px;
    }

    .navbar a {
        color: white; /* Fixed color for navbar links */
        text-decoration: none; /* Remove underline */
    }

    .navbar a:hover {
        color: #d1e7dd; /* Change to a lighter shade on hover */
    }

    .navbar .navbar-links {
        display: flex;
        align-items: center;
        gap: 20px;
        padding-right: 20px;
    }

    .navbar .navbar-links .sidebar-toggle-btn {
        margin-left: 20px;
        font-size: 30px;
        background-color: transparent;
        border: none;
        cursor: pointer;
        color: white;
    }

    /* Fix dash body to cover the entire screen excluding navbar and footer */
    html, body {
    height: 100%; /* Ensures the body takes up the full viewport height */
    margin: 0; /* Removes any default margin */
    background-color: #D6EAF3; /* Sets the background color for the entire page */
}

    .dash-body {
    min-height: 100vh;
    background-color: #D6EAF3;
    margin: 15px auto;
    padding: 1px;
    width: 100%;
    max-width: 1900px;
    transition: margin 0.3s ease;
}


.dash-body.expanded {
    margin-right: 220px;  
}

/* Dashboard table and animation */
.dashbord-tables {
    animation: transitionIn-Y-over 0.5s;
}


    /* preimium ui */
    /* Chat Bot Section Container */
.chat-bot-section {
    width: 95%;
    max-width: 1900px;
    background-color: #0A1D30;
    border-radius: 16px;
    padding: 30px 20px;
    color: #ffffff;
    text-align: center;
    font-family: 'Segoe UI', sans-serif;
    position: relative;
    margin: 20px auto;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

/* Background pattern for visual interest */
.chat-bot-section::before {
    content: '';
    position: absolute;
    top: -40px;
    left: -40px;
    width: 160px;
    height: 160px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    border-radius: 50%;
    z-index: 0;
}

.chat-bot-section::after {
    content: '';
    position: absolute;
    bottom: -40px;
    right: -40px;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    border-radius: 50%;
    z-index: 0;
}

/* Title Styling */
.chat-bot-title {
    font-size: 28px;
    color: #FFD700;
    font-weight: bold;
    margin-bottom: 16px;
    z-index: 1;
    position: relative;
}

/* Chat Input Container */
.chat-input-container {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 15px;
    gap: 10px;
    z-index: 1;
    position: relative;
}

.chat-icon {
    font-size: 24px;
    color: #FFD700;
}

.chat-input {
    width: 70%;
    padding: 12px 16px;
    border-radius: 20px;
    border: 1px solid #FFD700;
    font-size: 16px;
    background-color: #ffffff;
    color: #0A1D30;
    outline: none;
}

.send-button {
    background-color: #FFD700;
    color: #0A1D30;
    border: none;
    padding: 10px 20px;
    border-radius: 20px;
    font-size: 18px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.send-button:hover {
    background-color: #FFA500;
}

/* Description Styling */
.chat-bot-description {
    font-size: 16px;
    color: #D3D3D3;
    margin-top: 15px;
    line-height: 1.8;
    padding: 0 10px;
    z-index: 1;
    position: relative;
}

/* Responsive adjustments */
@media screen and (max-width: 768px) {
    .chat-bot-section {
        padding: 20px 15px;
    }
    .chat-input {
        width: 60%;
    }
    .send-button {
        padding: 8px 16px;
    }
}

    /* Overall container for the Premium Services section */
.premium-services {
    width: 95%;
    max-width: 1900px;
    margin: 30px auto;
    background-color: #0A1D30;
    border-radius: 16px;
    color: #ffffff;
    padding: 40px 30px;
    text-align: center;
    font-family: 'Segoe UI', sans-serif;
    position: relative;
    overflow: hidden;
}

/* Background pattern for visual interest */
.premium-services::before {
    content: '';
    position: absolute;
    top: -50px;
    left: -50px;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    border-radius: 50%;
    z-index: 0;
}

.premium-services::after {
    content: '';
    position: absolute;
    bottom: -50px;
    right: -50px;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    border-radius: 50%;
    z-index: 0;
}

/* Main header */
.premium-services h2 {
    font-size: 32px;
    color: #FFD700;
    font-weight: bold;
    margin-bottom: 16px;
    z-index: 1;
    position: relative;
}

/* Description text */
.premium-services p.description {
    font-size: 18px;
    color: #D3D3D3;
    margin-bottom: 25px;
    padding: 0 15px;
    line-height: 1.8;
    z-index: 1;
    position: relative;
}

/* Benefit Card container */
.premium-services .benefit-cards {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    margin-top: 25px;
    z-index: 1;
    position: relative;
}

/* Individual benefit card with unique look */
.premium-services .benefit-card {
    background-color: #ffffff;
    color: #0A1D30;
    border-radius: 12px;
    padding: 25px 20px;
    width: 240px;
    text-align: left;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.premium-services .benefit-card::before {
    content: '';
    position: absolute;
    top: -30px;
    right: -30px;
    width: 80px;
    height: 80px;
    background: radial-gradient(circle, rgba(255, 223, 0, 0.15), rgba(255, 223, 0, 0));
    border-radius: 50%;
}

.premium-services .benefit-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
}

/* Heading for each benefit */
.premium-services .benefit-card h3 {
    font-size: 20px;
    color: #0A1D30;
    margin-bottom: 10px;
    font-weight: 600;
}

/* Text inside each benefit */
.premium-services .benefit-card p {
    font-size: 15px;
    color: #606060;
    line-height: 1.6;
}

/* Pricing and CTA */
.premium-services .highlight-text {
    font-weight: bold;
    color: #FFD700;
    font-size: 20px;
    margin-top: 20px;
}

.premium-services .small-text {
    font-size: 13px;
    color: #B0B0B0;
    margin-top: 5px;
}

.premium-services .btn-primary {
    background-color: #FFD700;
    color: #0A1D30;
    padding: 14px 36px;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
    margin-top: 25px;
}

.premium-services .btn-primary:hover {
    background-color: #FFA500;
}

/* Responsive adjustments */
@media screen and (max-width: 768px) {
    .premium-services .benefit-cards {
        flex-direction: column;
        align-items: center;
    }
    .premium-services .benefit-card {
        width: 100%;
        max-width: 300px;
    }
}


    .brand-title {
        margin-left:18px;
        font-size: 25px;
    }

    .filter-container {
        animation: transitionIn-Y-bottom 0.5s;
    }

    .sub-table,
    .anime {
        animation: transitionIn-Y-bottom 0.5s;
    }

    /* Footer Styling */
    .footer {
        text-align: center;
        padding: 5px;
        background-color: #0A1A24;
        color: grey;
        position: fixed;
        bottom: 0;
        width: 100%;
        margin-top: 20px; /* Space above footer */
    }
</style>


</head>
<body>
    <?php
    // session_start();

    if (isset($_SESSION["user"])) {
        if (($_SESSION["user"]) == "" or $_SESSION['usertype'] != 'p') {
            header("location: ../login.php");
        } else {
            $useremail = $_SESSION["user"];
        }
    } else {
        header("location: ../login.php");
    }

    include("../connection.php");
    $userrow = $database->query("select * from patient where pemail='$useremail'");
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];
    ?>

    <div class="navbar">
        <div class="brand-title">Invigorix</div>
        <div class="navbar-links">
            <a href="index.php">Home</a>
            <a href="doctors.php">Doctors</a>
            <a href="schedule.php">Sessions</a>
            <a href="appointment.php">Bookings</a>
            <a href="settings.php">Settings</a>
            <button class="sidebar-toggle-btn" onclick="toggleSidebar()">☰</button>
        </div>
    </div>

    <div class="sidebar" id="sidebar">
        <!-- Close Button in Sidebar -->
        <button class="close-sidebar-btn" onclick="toggleSidebar()">✖</button>
        <table class="menu-container" border="0">
            <tr>
                <td style="padding:10px" colspan="2">
                    <table border="0" class="profile-container">
                        <tr>
                            <td width="30%" style="padding-left:20px">
                                <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                            </td>
                            <td style="padding:0px;margin:0px;">
                                <p class="profile-title"><?php echo substr($username, 0, 13) ?>..</p>
                                <p class="profile-subtitle"><?php echo substr($useremail, 0, 22) ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-home menu-active">
                    <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">______Home</p></a></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-doctor">
                    <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">______Doctors</p></a></div>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-session">
                    <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">______Sessions</p></a></div>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-appoinment">
                    <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">_____Bookings</p></a></div>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-settings">
                    <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">______Settings</p></a></div>
                </td>
            </tr>
        </table>
    </div>

   <!-- Existing Dashboard Content -->
   <div class="dash-body" style="margin-top: 1px">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td colspan="1" class="nav-bar">
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">Home</p>
                    </td>
                    <td width="25%"></td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                                date_default_timezone_set('Asia/Kolkata');
                                $today = date('Y-m-d');
                                echo $today;

                                $patientrow = $database->query("select * from patient;");
                                $doctorrow = $database->query("select * from doctor;");
                                $appointmentrow = $database->query("select * from appointment where appodate >= '$today';");
                                $schedulerow = $database->query("select * from schedule where scheduledate = '$today';");
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;">
                            <img src="../img/calendar.svg" width="100%">
                        </button>
                    </td>
                </tr>

                <!-- Welcome Message -->
                <tr>
                    <td colspan="4">
                        <center>
                            <table class="filter-container doctor-header patient-header" style="border: none;width:95%" border="0">
                                <tr>
                                    <td>
                                        <h3>Welcome!</h3>
                                        <h1><?php echo $username ?>.</h1>
                                        <p>Haven't any idea about doctors? no problem let's jump to 
                                            <a href="doctors.php" class="non-style-link"><b>"All Doctors"</b></a> section or 
                                            <a href="schedule.php" class="non-style-link"><b>"Sessions"</b></a> <br>
                                            Track your past and future appointments history. Also find out the expected arrival time of your doctor or medical consultant.<br><br>
                                        </p>
                                        <h3>Channel a Doctor Here</h3>
                                        <form action="schedule.php" method="post" style="display: flex;">
                                            <input type="search" name="search" class="input-text" placeholder="Search Doctor and We will Find The Session Available" list="doctors" style="width:45%;">&nbsp;&nbsp;
                                            <?php
                                                echo '<datalist id="doctors">';
                                                $list11 = $database->query("select docname, docemail from doctor;");
                                                for ($y = 0; $y < $list11->num_rows; $y++) {
                                                    $row00 = $list11->fetch_assoc();
                                                    $d = $row00["docname"];
                                                    echo "<option value='$d'><br/>";
                                                };
                                                echo '</datalist>';
                                            ?>
                                            <input type="submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>

                <tr>
    <td colspan="4">
        <center>
            <div class="chat-bot-section">
                <h2 class="chat-bot-title">Start Chat with our Health AI Bot</h2>
                <div class="chat-input-container">
                    <div class="chat-icon">💬</div>
                    <input type="text" placeholder="Message Our Health AI Bot" class="chat-input">
<button class="send-button" onclick="redirectToBot()">➤</button>
                </div>
                <p class="chat-bot-description">
                    Our AI bot is here to assist you with your healthcare queries anytime, anywhere. Enjoy an interactive experience with our virtual assistant.
                </p>
            </div>
        </center>
    </td>
</tr>

<script>
    function redirectToBot() {
        window.location.href = "aibot.php";
    }
</script>

                <!-- premium -->
                <tr>
            <td colspan="4">
                <center>
                    <div class="premium-services">
                        <h2>Unlock Pro Membership</h2>
                        <p class="description">Experience next-level healthcare with our Pro Membership. Enjoy exclusive benefits, priority services, and a seamless health journey.</p>
                        
                        <div class="benefit-cards">
                            <!-- Card 1: Priority Access -->
                            <div class="benefit-card">
                                <h3>Priority Access</h3>
                                <p>Skip the queues and access healthcare services without waiting, whenever you need them.</p>
                            </div>

                            <!-- Card 2: Member Discounts -->
                            <div class="benefit-card">
                                <h3>Member Discounts</h3>
                                <p>Save on consultations, treatments, and healthcare packages as a Pro member.</p>
                            </div>

                            <!-- Card 3: 24/7 Support -->
                            <div class="benefit-card">
                                <h3>24/7 Support</h3>
                                <p>Get dedicated, around-the-clock support for all your healthcare needs.</p>
                            </div>
                        </div>

                        <p class="highlight-text">Start today for just ₹499/annum</p>
                        <p class="small-text">*Unlock extra benefits with an annual plan</p>
                        
                        <button class="btn-primary" onclick="window.location.href='https://buy.stripe.com/test_28obMK9XOfpraJy5kn'">Join Now</button>
            
                    </div>
                </center>
            </td>
        </tr>

                <!-- Status and Upcoming Booking -->
                <tr>
                    <td colspan="4">
                        <table border="0" width="100%">
                            <tr>
                                <td width="50%">
                                    <center>
                                        <table class="filter-container" style="border: none;" border="0">
                                            <tr>
                                                <td colspan="4">
                                                    <p class="heading-sub12" style="margin-left: 45px;font-weight: 600;">Status</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items bg-primary" style="padding: 20px;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $doctorrow->num_rows; ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">
                                                                Doctors & Consultants
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons">
                                                            <img src="../img/icons/doctors-hover.svg" alt="">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items bg-secondary" style="padding: 20px;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $patientrow->num_rows; ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">
                                                                Patients
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons">
                                                            <img src="../img/icons/patients-hover.svg" alt="">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items bg-warning" style="padding: 20px;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $appointmentrow->num_rows; ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">
                                                                New Booking
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons">
                                                            <img src="../img/icons/book-hover.svg" alt="">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items bg-success" style="padding: 20px;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $schedulerow->num_rows; ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">
                                                                Today Sessions
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons">
                                                            <img src="../img/icons/session-iceblue.svg" alt="">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </center>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

    <!-- Footer -->
    <div class="footer">
        Copyright © Invigorix.2024
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const dashBody = document.getElementById('dash-body');
            sidebar.classList.toggle('open');
            dashBody.classList.toggle('expanded');
        }
    </script>
</body>
</html>
