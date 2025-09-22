<?php
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
$products = getProducts(20, $search, $category);
$categories = getCategories();
?>

<div class="products-page">
    <div class="container">
        <!-- Filters -->
        <div class="filters">
            <div class="filter-section">
                <h3>Categories</h3>
                <div class="category-filters">
                    <a href="?page=products" class="filter-btn <?php echo empty($category) ? 'active' : ''; ?>">
                        All Categories
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="?page=products&category=<?php echo urlencode($cat); ?>" 
                           class="filter-btn <?php echo $category === $cat ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Results -->
        <div class="products-section">
            <div class="results-header">
                <h1>Products</h1>
                <?php if (!empty($search)): ?>
                    <p>Search results for: <strong>"<?php echo htmlspecialchars($search); ?>"</strong></p>
                <?php endif; ?>
                <?php if (!empty($category)): ?>
                    <p>Category: <strong><?php echo htmlspecialchars($category); ?></strong></p>
                <?php endif; ?>
                <p><?php echo count($products); ?> products found</p>
            </div>
            
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <i class="fas fa-search"></i>
                    <h3>No products found</h3>
                    <p>Try adjusting your search or browse our categories.</p>
                    <a href="?page=home" class="btn-primary">Back to Home</a>
                </div>
            <?php else: ?>
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
                                
                                <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                                
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
            <?php endif; ?>
        </div>
    </div>
</div>