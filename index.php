<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get current page (simplified - no login/signup)
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed_pages = ['home', 'products', 'product-detail', 'help', 'contact', 'disclaimer'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

$title = ucfirst($page) . ' - FlipMart';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="theme-dark">
    <!-- Header -->
    <?php include 'includes/header.php'; ?>
    
    <!-- Main Content -->
    <main class="main-content">
        <?php
        $page_file = 'pages/' . $page . '.php';
        if (file_exists($page_file)) {
            include $page_file;
        } else {
            include 'pages/404.php';
        }
        ?>
    </main>
    
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/search.js"></script>
    
    <!-- No cache control for development -->
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            for(let registration of registrations) {
                registration.unregister();
            }
        });
    }
    </script>
</body>
</html>