<!-- Header -->
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="?page=home">
                    <i class="fas fa-shopping-bag"></i>
                    <span>FlipMart</span>
                </a>
            </div>
            
            <div class="search-bar">
                <form method="GET" action="?page=products" class="search-form">
                    <input type="text" name="search" placeholder="Search for products..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            
            <div class="header-actions">
                <button class="theme-toggle" onclick="toggleTheme()">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Navigation Bar -->
    <nav class="navigation">
        <div class="container">
            <div class="nav-items">
                <a href="?page=home" class="nav-item <?php echo $page === 'home' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="?page=products" class="nav-item <?php echo $page === 'products' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Products
                </a>
                <a href="?page=help" class="nav-item <?php echo $page === 'help' ? 'active' : ''; ?>">
                    <i class="fas fa-question-circle"></i> Help
                </a>
                <a href="?page=contact" class="nav-item <?php echo $page === 'contact' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Contact
                </a>
                <a href="?page=disclaimer" class="nav-item <?php echo $page === 'disclaimer' ? 'active' : ''; ?>">
                    <i class="fas fa-info-circle"></i> Disclaimer
                </a>
            </div>
        </div>
    </nav>
</header>