<?php
$products = getProducts(8);
$categories = getCategories();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Welcome to FlipMart</h1>
            <p>Discover amazing deals from top brands</p>
            <div class="hero-search">
                <form method="GET" action="?page=products">
                    <input type="text" name="search" placeholder="What are you looking for?">
                    <button type="submit">Search Now</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<?php if (!empty($categories)): ?>
<section class="categories">
    <div class="container">
        <h2>Shop by Category</h2>
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <a href="?page=products&category=<?php echo urlencode($category); ?>" class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-<?php echo getCategoryIcon($category); ?>"></i>
                    </div>
                    <span><?php echo htmlspecialchars($category); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Products -->
<section class="featured-products">
    <div class="container">
        <h2>Featured Products</h2>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>"
                             onerror="this.src='assets/images/placeholder.jpg'">
                        <?php if ($product['discount_percentage'] > 0): ?>
                            <span class="discount-badge"><?php echo $product['discount_percentage']; ?>% OFF</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-info">
                        <h3 class="product-title">
                            <a href="?page=product-detail&id=<?php echo $product['id']; ?>">
                                <?php echo htmlspecialchars($product['title']); ?>
                            </a>
                        </h3>
                        
                        <div class="product-price">
                            <span class="current-price"><?php echo formatPrice($product['current_price']); ?></span>
                            <?php if ($product['original_price'] > $product['current_price']): ?>
                                <span class="original-price"><?php echo formatPrice($product['original_price']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <a href="<?php echo getAffiliateLink($product); ?>" 
                           target="_blank" class="buy-now-btn">
                            <i class="fas fa-shopping-cart"></i> Buy Now
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="view-all">
            <a href="?page=products" class="btn-primary">View All Products</a>
        </div>
    </div>
</section>

<?php
function getCategoryIcon($category) {
    $icons = [
        'Electronics' => 'laptop',
        'Fashion' => 'tshirt',
        'Home' => 'home',
        'Sports' => 'futbol',
        'Books' => 'book',
        'Beauty' => 'heart'
    ];
    return isset($icons[$category]) ? $icons[$category] : 'tag';
}
?>