<?php
// Global functions for the website

function getProducts($limit = 12, $search = '', $category = '') {
    global $pdo;
    
    $sql = "SELECT * FROM products WHERE is_active = 1";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (title LIKE ? OR description LIKE ? OR category LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($category)) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ?";
    $params[] = $limit;
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

function getProductById($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        return false;
    }
}

function getCategories() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT category FROM products WHERE is_active = 1 ORDER BY category");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(PDOException $e) {
        return [];
    }
}

function formatPrice($price) {
    return '₹' . number_format($price, 0);
}

function calculateDiscount($original_price, $current_price) {
    if ($original_price <= $current_price) return 0;
    return round((($original_price - $current_price) / $original_price) * 100);
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function getAffiliateLink($product) {
    // Ensure affiliate link is properly formatted
    $link = $product['affiliate_link'];
    if (!filter_var($link, FILTER_VALIDATE_URL)) {
        return '#';
    }
    return $link;
}

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

// Include scraper functionality
require_once __DIR__ . '/scraper.php';

// Telegram webhook functions
function processNewTelegramPost($postData) {
    // Process Telegram post data and extract products
    return true;
}
?>