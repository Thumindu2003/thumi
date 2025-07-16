<?php
//session_start();

// Check if user is logged in
$loggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$username = $loggedIn ? ($_SESSION['username'] ?? '') : '';
$email = $loggedIn ? ($_SESSION['email'] ?? '') : '';
$initial = $loggedIn && $email ? strtoupper(substr($email, 0, 1)) : '?';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Profile Dropdown</title>
    <style>
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .profile-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #4285F4;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            border: none;
            outline: none;
            transition: all 0.3s ease;
        }
        
        .profile-icon:hover {
            background-color: #3367D6;
            transform: scale(1.05);
        }
        
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 45px;
            right: 0;
            background-color: white;
            min-width: 220px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
            z-index: 1000;
            padding: 8px 0;
            overflow: hidden;
        }
        
        .dropdown-menu.show {
            display: block;
            animation: fadeIn 0.2s ease-in-out;
        }
        
        .user-info {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: #202124;
            margin-bottom: 4px;
        }
        
        .user-email {
            font-size: 13px;
            color: #5f6368;
            word-break: break-word;
        }
        
        .dropdown-menu a {
            display: block;
            padding: 10px 16px;
            text-decoration: none;
            color: #202124;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        
        .dropdown-menu a:hover {
            background-color: #f5f5f5;
        }
        
        .dropdown-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="profile-dropdown">
    <button class="profile-icon" onclick="toggleDropdown()">
        <?php echo $initial; ?>
    </button>
    <div id="dropdownMenu" class="dropdown-menu">
        <?php if($loggedIn): ?>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($username); ?></div>
                <div class="user-email"><?php echo htmlspecialchars($email); ?></div>
            </div>
            
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log out</a>
        <?php else: ?>
           
        <?php endif; ?>
    </div>
</div>

<script>
function toggleDropdown() {
    document.getElementById("dropdownMenu").classList.toggle("show");
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.querySelector('.profile-dropdown');
    if (!dropdown.contains(e.target)) {
        document.getElementById("dropdownMenu").classList.remove("show");
    }
});

// Close dropdown when pressing Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById("dropdownMenu").classList.remove("show");
    }
});
</script>

</body>
</html>