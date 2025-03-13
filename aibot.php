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
    <title>Invigorix AI Bot</title>
   
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

/* ai-chat */

    /* Fullscreen overlay styling for the chatbot */
    #chatbotOverlay {
        position: fixed;
        top: 36px; /* Adds a 14px gap from the navbar */
        left: 0;
        width: 100vw;
        height: calc(100vh - 36px); /* Adjusts height for the top gap */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 20px;
        padding-bottom: 40px; /* Prevents overlap with footer */
        color: white;
        z-index: 1000;
        font-family: Arial, sans-serif;
        overflow-y: auto;
        box-sizing: border-box;
    }

    /* Styling for the main chat area */
    #chatContent {
        flex-grow: 1;
        overflow-y: auto;
        padding-right: 10px;
        padding-bottom: 20px; /* Adds space above input area */
        scrollbar-width: thin;
        scrollbar-color: #ccc transparent;
    }

    #chatContent::-webkit-scrollbar {
        width: 8px;
    }

    #chatContent::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 10px;
    }

    /* Styling for chat messages */
    .message {
        margin: 8px 0;
        padding: 12px 16px;
        border-radius: 12px;
        max-width: 75%;
        font-size: 0.95rem;
        line-height: 1.5;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        display: inline-block;
        clear: both;
    }

    /* Styling for user (sender) and bot messages */
    .user-message {
        float: right; /* User messages on the left */
        background-color: #708090;
        color: white;
        margin-top: 14px;
        text-align: left;
    }

    .bot-message {
        float: left; /* Bot messages on the right */
        background-color: #004B4D;
        color: #ddd;
        text-align: left;
    }

    /* Container for the input and button */
    #inputContainer {
        display: flex;
        gap: 20px;
        padding-top: 10px;
        padding-bottom: 20px; /* Ensures distance from footer */
        position: fixed;
        bottom: 20px;
        left: 0;
        width: 100%;
        padding-left: 20px;
        padding-right: 20px;
        box-sizing: border-box;
        background-color: rgba(10, 29, 48, 0.9); /* Overlay effect */
    }

    /* Styling for the input field */
    #chatInput {
        flex-grow: 1;
        padding: 14px;
        font-size: 1rem;
        border-radius: 20px;
        border: 1px solid #ddd;
        background-color: #fff;
        color: #0A1D30;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #chatInput:focus {
        outline: none;
        border-color: #4CAF50;
    }

    /* Styling for the send button */
    #sendButton {
        padding: 12px 20px;
        font-size: 1rem;
        border-radius: 20px;
        border: none;
        background-color: navy;
        color: white;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        transition: background-color 0.3s;
    }

    #sendButton:hover {
        background-color: #001B3D;
    }

    /* Animation for messages appearing */
    .message {
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* preimium ui */

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

    <!-- ai bot --> 
    <div id="chatbotOverlay">
    <!-- Chat messages -->
    <div id="chatContent"></div>

    <!-- Input for user messages -->
    <div id="inputContainer">
        <input type="text" id="chatInput" placeholder="Type your message here..." autofocus>
        <button id="sendButton">Send</button>
    </div>
</div>

<script>
    const chatContent = document.getElementById("chatContent");
    const chatInput = document.getElementById("chatInput");
    const sendButton = document.getElementById("sendButton");

    // Dataset with diseases, descriptions, medications, precautions, and workouts
    const diseaseData = {
    "headache": {
        "description": "A common condition that causes pain and discomfort in the head or neck region.",
        "medications": ["Paracetamol", "Ibuprofen"],
        "precautions": ["Avoid bright lights", "Stay hydrated", "Take rest", "Avoid stress"],
        "workout": ["Light stretching", "Yoga for relaxation"]
    },
    "fever": {
        "description": "A temporary increase in body temperature, often due to an illness.",
        "medications": ["Paracetamol", "Ibuprofen"],
        "precautions": ["Stay hydrated", "Get plenty of rest", "Avoid cold environments", "Eat light meals"],
        "workout": ["Rest is recommended during fever"]
    },
    "cough": {
        "description": "A reflex action to clear the airways of mucus or irritants.",
        "medications": ["Dextromethorphan", "Guaifenesin"],
        "precautions": ["Stay hydrated", "Avoid cold drinks", "Take warm fluids", "Rest as needed"],
        "workout": ["Gentle breathing exercises"]
    },
    "diarrhea": {
        "description": "A condition characterized by loose, watery stools occurring more frequently than usual.",
        "medications": ["Loperamide", "Oral Rehydration Solutions"],
        "precautions": ["Stay hydrated", "Eat bland foods", "Avoid dairy", "Wash hands frequently"],
        "workout": ["Rest is recommended until symptoms subside"]
    },
    "hypertension": {
        "description": "High blood pressure, often associated with heart and blood vessel strain.",
        "medications": ["Amlodipine", "Losartan"],
        "precautions": ["Limit salt intake", "Regular exercise", "Avoid smoking", "Reduce stress"],
        "workout": ["Aerobic exercises", "Brisk walking", "Yoga for stress relief"]
    },
    "diabetes": {
        "description": "A chronic condition that affects how your body processes blood sugar (glucose).",
        "medications": ["Metformin", "Insulin"],
        "precautions": ["Monitor blood sugar levels", "Limit sugary foods", "Regular exercise", "Stay hydrated"],
        "workout": ["Light cardio", "Strength training", "Stretching"]
    },
    "asthma": {
        "description": "A respiratory condition marked by spasms in the bronchi of the lungs, causing breathing difficulty.",
        "medications": ["Albuterol", "Inhaled corticosteroids"],
        "precautions": ["Avoid allergens", "Use inhalers as prescribed", "Regular check-ups"],
        "workout": ["Breathing exercises", "Light stretching", "Walking"]
    },
    "allergy": {
        "description": "An immune response to a foreign substance that isn’t typically harmful to your body.",
        "medications": ["Antihistamines", "Decongestants"],
        "precautions": ["Avoid allergens", "Stay hydrated", "Use air filters if needed"],
        "workout": ["Low-intensity activities like walking or yoga"]
    },
    "acne": {
        "description": "A skin condition that occurs when hair follicles become clogged with oil and dead skin cells.",
        "medications": ["Benzoyl peroxide", "Salicylic acid"],
        "precautions": ["Keep face clean", "Avoid heavy creams", "Stay hydrated"],
        "workout": ["Moderate physical activities, avoid excessive sweating if possible"]
    },
    "anemia": {
        "description": "A condition where you lack enough healthy red blood cells to carry adequate oxygen to your tissues.",
        "medications": ["Iron supplements", "Vitamin B12"],
        "precautions": ["Eat iron-rich foods", "Limit tea and coffee with meals", "Take iron supplements as prescribed"],
        "workout": ["Light exercise like walking or yoga, avoid intense activity until iron levels improve"]
    },
    "arthritis": {
        "description": "Inflammation of one or more joints, causing pain and stiffness.",
        "medications": ["Ibuprofen", "Naproxen"],
        "precautions": ["Avoid repetitive motion", "Use heat or ice therapy", "Practice gentle movements"],
        "workout": ["Low-impact exercises like swimming, yoga, and stretching"]
    },
    "bronchitis": {
        "description": "Inflammation of the lining of your bronchial tubes, which carry air to and from your lungs.",
        "medications": ["Cough suppressants", "Bronchodilators"],
        "precautions": ["Avoid smoking", "Stay hydrated", "Avoid irritants like dust"],
        "workout": ["Breathing exercises, light stretching"]
    },
    "cold": {
        "description": "A viral infection of your nose and throat (upper respiratory tract).",
        "medications": ["Decongestants", "Cough syrups"],
        "precautions": ["Stay hydrated", "Rest", "Avoid cold and damp places"],
        "workout": ["Rest is recommended, light stretching if feeling up to it"]
    },
    "flu": {
        "description": "A contagious respiratory illness caused by influenza viruses.",
        "medications": ["Antiviral medications like Tamiflu"],
        "precautions": ["Stay hydrated", "Rest", "Avoid public spaces to prevent spread"],
        "workout": ["Rest until fully recovered"]
    },
    "migraine": {
        "description": "A type of headache that can cause severe throbbing pain or a pulsing sensation, usually on one side of the head.",
        "medications": ["Sumatriptan", "Rizatriptan"],
        "precautions": ["Avoid bright lights", "Stay hydrated", "Limit caffeine"],
        "workout": ["Gentle yoga, meditation, and light stretching"]
    }
};

    // Real-time message sending and bot reply
    sendButton.addEventListener("click", () => {
        const userMessage = chatInput.value.trim();
        if (userMessage) {
            addMessage(userMessage, "user-message");
            chatInput.value = "";
            setTimeout(() => {
                botReply(userMessage);
            }, 500); // Short delay for bot response
        }
    });

    // Add message to chat display
    function addMessage(text, className) {
        const messageElement = document.createElement("div");
        messageElement.classList.add("message", className);
        messageElement.textContent = text;
        chatContent.appendChild(messageElement);
        chatContent.scrollTop = chatContent.scrollHeight; // Scroll to bottom
    }

    // Enhanced bot reply function using disease dataset
    function botReply(userMessage) {
        let botMessage = "I'm here to assist you! Could you please describe your symptoms or ask any health-related question you have?";
        let diseaseFound = false;

        // Check for diseases in user message
        for (const disease in diseaseData) {
            if (userMessage.toLowerCase().includes(disease)) {
                const data = diseaseData[disease];
                botMessage = `**${disease.charAt(0).toUpperCase() + disease.slice(1)}:** ${data.description}.  
*Medications:* ${data.medications.join(", ")}  
*Precautions:* ${data.precautions.join(", ")}  
*Recommended Workout:* ${data.workout.join(", ")}  
                `;
                diseaseFound = true;
                break;
            }
        }

        if (!diseaseFound) {
            botMessage = "I can provide health tips, explain symptoms, suggest precautions, and share information about common conditions. Feel free to describe your symptoms or ask any health-related question! Could you please describe your symptoms or ask any health-related question you have?";
        }

        if (userMessage.toLowerCase().includes("doctor")) {
            botMessage = "Based on your symptoms, I recommend consulting Dr. Pratibha, a general practitioner.";
        } else if (userMessage.toLowerCase().includes("health")) {
            botMessage = "For general health tips, remember to drink plenty of water, get regular exercise, and try to sleep for at least 7-8 hours each night.";
        } else if (userMessage.toLowerCase().includes("hey")|| userMessage.toLowerCase().includes("hello")) {
            botMessage = "Hey, I'm CareGenie, your personal health assistant! How can I help you today?";
        } 
                else if (userMessage.toLowerCase().includes("bye") || userMessage.toLowerCase().includes("thanks")) {
            botMessage = "You're welcome! Take care, and reach out anytime you have questions. Stay healthy!";
    }



        addMessage(botMessage, "bot-message");
    }
</script>

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


