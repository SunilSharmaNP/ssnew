<?php
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProductById($product_id);

if (!$product) {
    include 'pages/404.php';
    return;
}

$related_products = getProducts(4, '', $product['category']);
// Remove current product from related products
$related_products = array_filter($related_products, function($p) use ($product_id) {
    return $p['id'] != $product_id;
});
$related_products = array_slice($related_products, 0, 3);
?>

<div class="product-detail-page">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="?page=home">Home</a>
            <span>/</span>
            <a href="?page=products">Products</a>
            <span>/</span>
            <a href="?page=products&category=<?php echo urlencode($product['category']); ?>">
                <?php echo htmlspecialchars($product['category']); ?>
            </a>
            <span>/</span>
            <span><?php echo htmlspecialchars($product['title']); ?></span>
        </nav>
        
        <div class="product-detail">
            <div class="product-images">
                <div class="main-image">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>"
                         onerror="handleImageError(this)">
                    <?php if ($product['discount_percentage'] > 0): ?>
                        <span class="discount-badge"><?php echo $product['discount_percentage']; ?>% OFF</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['title']); ?></h1>
                
                <div class="product-meta">
                    <span class="category">
                        <i class="fas fa-tag"></i>
                        <a href="?page=products&category=<?php echo urlencode($product['category']); ?>">
                            <?php echo htmlspecialchars($product['category']); ?>
                        </a>
                    </span>
                    
                    <?php if ($product['source_site']): ?>
                        <span class="source">
                            <i class="fas fa-store"></i>
                            Available on <?php echo ucfirst($product['source_site']); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="product-price">
                    <span class="current-price"><?php echo formatPrice($product['current_price']); ?></span>
                    <?php if ($product['original_price'] > $product['current_price']): ?>
                        <span class="original-price"><?php echo formatPrice($product['original_price']); ?></span>
                        <span class="savings">
                            You save <?php echo formatPrice($product['original_price'] - $product['current_price']); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php if ($product['description']): ?>
                    <div class="product-description">
                        <h3>Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="product-actions">
                    <a href="<?php echo getAffiliateLink($product); ?>" 
                       target="_blank" class="buy-now-btn main">
                        <i class="fas fa-shopping-cart"></i>
                        Buy Now on <?php echo ucfirst($product['source_site'] ?: 'Store'); ?>
                    </a>
                    
                    <button onclick="shareProduct()" class="share-btn">
                        <i class="fas fa-share-alt"></i>
                        Share
                    </button>
                </div>
                
                <div class="product-notes">
                    <p><i class="fas fa-info-circle"></i> You will be redirected to the seller's website to complete your purchase</p>
                    <p><i class="fas fa-shield-alt"></i> Secure payment handled by the official retailer</p>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
            <div class="related-products">
                <h2>Related Products</h2>
                <div class="products-grid">
                    <?php foreach ($related_products as $related): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($related['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($related['title']); ?>"
                                     onerror="handleImageError(this)">
                                <?php if ($related['discount_percentage'] > 0): ?>
                                    <span class="discount-badge"><?php echo $related['discount_percentage']; ?>% OFF</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="?page=product-detail&id=<?php echo $related['id']; ?>">
                                        <?php echo htmlspecialchars($related['title']); ?>
                                    </a>
                                </h3>
                                
                                <div class="product-price">
                                    <span class="current-price"><?php echo formatPrice($related['current_price']); ?></span>
                                    <?php if ($related['original_price'] > $related['current_price']): ?>
                                        <span class="original-price"><?php echo formatPrice($related['original_price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <a href="<?php echo getAffiliateLink($related); ?>" 
                                   target="_blank" class="buy-now-btn">
                                    <i class="fas fa-shopping-cart"></i> Buy Now
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo addslashes($product['title']); ?>',
            text: 'Check out this amazing product on FlipMart!',
            url: window.location.href
        });
    } else {
        // Fallback to copy URL
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Product link copied to clipboard!');
        });
    }
}
</script>

<style>
.product-detail-page {
    padding: 2rem 0;
}

.breadcrumb {
    margin-bottom: 2rem;
    font-size: 0.9rem;
}

.breadcrumb a {
    color: #667eea;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb span {
    margin: 0 0.5rem;
    color: #6c757d;
}

.product-detail {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 4rem;
}

.main-image {
    position: relative;
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.theme-dark .main-image {
    background: #21262d;
    border: 1px solid #30363d;
}

.main-image img {
    width: 100%;
    height: 400px;
    object-fit: contain;
    border-radius: 8px;
}

.product-info {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.theme-dark .product-info {
    background: #21262d;
    border: 1px solid #30363d;
}

.product-info h1 {
    font-size: 1.8rem;
    margin-bottom: 1rem;
    color: #333;
    line-height: 1.4;
}

.theme-dark .product-info h1 {
    color: #c9d1d9;
}

.product-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.product-meta span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #6c757d;
}

.theme-dark .product-meta span {
    color: #8b949e;
}

.product-meta a {
    color: #667eea;
    text-decoration: none;
}

.product-meta a:hover {
    text-decoration: underline;
}

.product-price {
    margin-bottom: 2rem;
}

.current-price {
    font-size: 2rem;
    font-weight: 700;
    color: #28a745;
}

.original-price {
    font-size: 1.2rem;
    color: #6c757d;
    text-decoration: line-through;
    margin-left: 1rem;
}

.savings {
    display: block;
    font-size: 1rem;
    color: #28a745;
    margin-top: 0.5rem;
    font-weight: 600;
}

.product-description {
    margin-bottom: 2rem;
}

.product-description h3 {
    color: #333;
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.theme-dark .product-description h3 {
    color: #c9d1d9;
}

.product-description p {
    color: #6c757d;
    line-height: 1.6;
}

.theme-dark .product-description p {
    color: #8b949e;
}

.product-actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.buy-now-btn.main {
    flex: 1;
    font-size: 1.1rem;
    padding: 15px 25px;
}

.share-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: 15px 20px;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.share-btn:hover {
    background: #5a6268;
}

.product-notes {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.theme-dark .product-notes {
    background: #0d1117;
    border-color: #667eea;
}

.product-notes p {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: #6c757d;
}

.theme-dark .product-notes p {
    color: #8b949e;
}

.product-notes i {
    color: #667eea;
    margin-right: 0.5rem;
}

.related-products {
    margin-top: 4rem;
}

.related-products h2 {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2rem;
    color: #333;
}

.theme-dark .related-products h2 {
    color: #c9d1d9;
}

@media (max-width: 768px) {
    .product-detail {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .main-image img {
        height: 300px;
    }
    
    .product-actions {
        flex-direction: column;
    }
    
    .current-price {
        font-size: 1.5rem;
    }
}
</style>