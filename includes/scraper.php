<?php
// Product scraping functionality for affiliate sites

function scrapeProductData($url) {
    $host = parse_url($url, PHP_URL_HOST);
    
    // Determine the site and use appropriate scraping method
    if (strpos($host, 'amazon') !== false) {
        return scrapeAmazonProduct($url);
    } elseif (strpos($host, 'flipkart') !== false) {
        return scrapeFlipkartProduct($url);
    } elseif (strpos($host, 'meesho') !== false) {
        return scrapeMeeshoProduct($url);
    } elseif (strpos($host, 'myntra') !== false) {
        return scrapeMyntraProduct($url);
    }
    
    return false;
}

function scrapeAmazonProduct($url) {
    try {
        $html = getPageContent($url);
        if (!$html) return false;
        
        // Extract product information using simple HTML parsing
        $title = extractBetween($html, '<title>', '</title>');
        $title = strip_tags($title);
        $title = explode(':', $title)[0]; // Remove ": Amazon.in" part
        
        // Extract price (multiple possible selectors)
        $price_patterns = [
            '/<span class="a-price-whole">([^<]+)</span>/',
            '/<span class="a-price a-text-price a-size-medium apexPriceToPay">.*?₹([0-9,]+)/',
            '/₹([0-9,]+)/'
        ];
        
        $current_price = 0;
        foreach ($price_patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $current_price = (float)str_replace(',', '', $matches[1]);
                break;
            }
        }
        
        // Extract original price
        $original_price = $current_price;
        if (preg_match('/M\.R\.P\.:.*?₹([0-9,]+)/', $html, $matches)) {
            $original_price = (float)str_replace(',', '', $matches[1]);
        }
        
        // Extract image
        $image_url = '';
        if (preg_match('/"hiRes":"([^"]+)"/', $html, $matches)) {
            $image_url = $matches[1];
        } elseif (preg_match('/data-old-hires="([^"]+)"/', $html, $matches)) {
            $image_url = $matches[1];
        }
        
        // Extract category (simplified)
        $category = 'Electronics'; // Default category
        if (preg_match('/nav-breadcrumb.*?<a[^>]*>([^<]+)<\/a>/s', $html, $matches)) {
            $category = trim(strip_tags($matches[1]));
        }
        
        return [
            'title' => trim($title),
            'description' => 'Product from Amazon',
            'category' => $category,
            'original_price' => $original_price,
            'current_price' => $current_price,
            'image_url' => $image_url,
            'affiliate_link' => $url,
            'source_site' => 'amazon'
        ];
        
    } catch (Exception $e) {
        error_log("Error scraping Amazon product: " . $e->getMessage());
        return false;
    }
}

function scrapeFlipkartProduct($url) {
    try {
        $html = getPageContent($url);
        if (!$html) return false;
        
        // Extract title
        $title = '';
        if (preg_match('/<h1[^>]*class="[^"]*_35KyD6[^"]*"[^>]*>([^<]+)<\/h1>/', $html, $matches)) {
            $title = trim($matches[1]);
        } elseif (preg_match('/<title>([^<]+)<\/title>/', $html, $matches)) {
            $title = explode('(', $matches[1])[0];
        }
        
        // Extract price
        $current_price = 0;
        if (preg_match('/₹([0-9,]+)/', $html, $matches)) {
            $current_price = (float)str_replace(',', '', $matches[1]);
        }
        
        // Extract original price
        $original_price = $current_price;
        if (preg_match('/<div[^>]*class="[^"]*_3I9_wc[^"]*"[^>]*>₹([0-9,]+)<\/div>/', $html, $matches)) {
            $original_price = (float)str_replace(',', '', $matches[1]);
        }
        
        // Extract image
        $image_url = '';
        if (preg_match('/"url":"([^"]*\.jpg[^"]*)"/', $html, $matches)) {
            $image_url = str_replace('\\', '', $matches[1]);
        }
        
        return [
            'title' => trim($title),
            'description' => 'Product from Flipkart',
            'category' => 'Electronics',
            'original_price' => $original_price,
            'current_price' => $current_price,
            'image_url' => $image_url,
            'affiliate_link' => $url,
            'source_site' => 'flipkart'
        ];
        
    } catch (Exception $e) {
        error_log("Error scraping Flipkart product: " . $e->getMessage());
        return false;
    }
}

function scrapeMeeshoProduct($url) {
    try {
        $html = getPageContent($url);
        if (!$html) return false;
        
        // Basic extraction for Meesho
        $title = '';
        if (preg_match('/<title>([^<]+)<\/title>/', $html, $matches)) {
            $title = explode('|', $matches[1])[0];
        }
        
        // Extract price
        $current_price = 0;
        if (preg_match('/₹([0-9,]+)/', $html, $matches)) {
            $current_price = (float)str_replace(',', '', $matches[1]);
        }
        
        return [
            'title' => trim($title),
            'description' => 'Product from Meesho',
            'category' => 'Fashion',
            'original_price' => $current_price,
            'current_price' => $current_price,
            'image_url' => '',
            'affiliate_link' => $url,
            'source_site' => 'meesho'
        ];
        
    } catch (Exception $e) {
        error_log("Error scraping Meesho product: " . $e->getMessage());
        return false;
    }
}

function scrapeMyntraProduct($url) {
    try {
        $html = getPageContent($url);
        if (!$html) return false;
        
        // Basic extraction for Myntra
        $title = '';
        if (preg_match('/<title>([^<]+)<\/title>/', $html, $matches)) {
            $title = explode('|', $matches[1])[0];
        }
        
        // Extract price
        $current_price = 0;
        if (preg_match('/Rs\. ([0-9,]+)/', $html, $matches)) {
            $current_price = (float)str_replace(',', '', $matches[1]);
        }
        
        return [
            'title' => trim($title),
            'description' => 'Product from Myntra',
            'category' => 'Fashion',
            'original_price' => $current_price,
            'current_price' => $current_price,
            'image_url' => '',
            'affiliate_link' => $url,
            'source_site' => 'myntra'
        ];
        
    } catch (Exception $e) {
        error_log("Error scraping Myntra product: " . $e->getMessage());
        return false;
    }
}

function getPageContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $html !== false) {
        return $html;
    }
    
    return false;
}

function extractBetween($string, $start, $end) {
    $startPos = strpos($string, $start);
    if ($startPos === false) return '';
    
    $startPos += strlen($start);
    $endPos = strpos($string, $end, $startPos);
    if ($endPos === false) return '';
    
    return substr($string, $startPos, $endPos - $startPos);
}

function createProduct($product_data) {
    global $pdo;
    
    try {
        // Check if product already exists by URL
        $stmt = $pdo->prepare("SELECT id FROM products WHERE affiliate_link = ?");
        $stmt->execute([$product_data['affiliate_link']]);
        if ($stmt->fetch()) {
            return false; // Product already exists
        }
        
        // Calculate discount
        $discount = 0;
        if ($product_data['original_price'] > $product_data['current_price']) {
            $discount = round((($product_data['original_price'] - $product_data['current_price']) / $product_data['original_price']) * 100);
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO products (title, description, category, original_price, current_price, discount_percentage, image_url, affiliate_link, source_site) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $product_data['title'],
            $product_data['description'],
            $product_data['category'],
            $product_data['original_price'],
            $product_data['current_price'],
            $discount,
            $product_data['image_url'],
            $product_data['affiliate_link'],
            $product_data['source_site']
        ]);
        
        return $pdo->lastInsertId();
        
    } catch(PDOException $e) {
        error_log("Error creating product: " . $e->getMessage());
        return false;
    }
}
?>