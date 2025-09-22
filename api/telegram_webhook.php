<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Helper functions defined here for LSP
function getSetting($key) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : null;
    } catch(PDOException $e) {
        return null;
    }
}

// Telegram webhook endpoint for product automation
header('Content-Type: application/json');

// Get webhook data
$input = file_get_contents('php://input');
$update = json_decode($input, true);

// Log for debugging
error_log("Telegram webhook received: " . $input);

// Validate Telegram webhook secret token
$secret_token = getSetting('telegram_secret_token');
$request_secret = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '';

if (empty($secret_token) || $request_secret !== $secret_token) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Process the update
if (isset($update['message'])) {
    $message = $update['message'];
    
    // Check if message contains links (text or caption)
    $text = '';
    if (isset($message['text'])) {
        $text = $message['text'];
    } elseif (isset($message['caption'])) {
        $text = $message['caption'];
    }
    
    if (!empty($text)) {
        $channel_id = $message['chat']['id'];
        $message_id = $message['message_id'];
        
        // Extract product links
        $links = extractProductLinks($text);
        
        if (!empty($links)) {
            // Store telegram post
            $post_id = storeTelegramPost($channel_id, $message_id, $text, $links);
            
            // Process each link
            $products_created = 0;
            foreach ($links as $link) {
                $product_data = scrapeProductData($link);
                if ($product_data) {
                    $product_id = createProduct($product_data);
                    if ($product_id) {
                        $products_created++;
                    }
                }
            }
            
            // Update post with results
            updateTelegramPost($post_id, $products_created);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Processed successfully',
                'products_created' => $products_created
            ]);
        } else {
            echo json_encode([
                'status' => 'skipped',
                'message' => 'No product links found'
            ]);
        }
    }
} elseif (isset($update['channel_post'])) {
    // Handle channel posts
    $post = $update['channel_post'];
    
    // Handle channel posts (text or caption)
    $text = '';
    if (isset($post['text'])) {
        $text = $post['text'];
    } elseif (isset($post['caption'])) {
        $text = $post['caption'];
    }
    
    if (!empty($text)) {
        $channel_id = $post['chat']['id'];
        $message_id = $post['message_id'];
        
        // Extract product links
        $links = extractProductLinks($text);
        
        if (!empty($links)) {
            // Store telegram post
            $post_id = storeTelegramPost($channel_id, $message_id, $text, $links);
            
            // Process each link
            $products_created = 0;
            foreach ($links as $link) {
                $product_data = scrapeProductData($link);
                if ($product_data) {
                    $product_id = createProduct($product_data);
                    if ($product_id) {
                        $products_created++;
                    }
                }
            }
            
            // Update post with results
            updateTelegramPost($post_id, $products_created);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Channel post processed successfully',
                'products_created' => $products_created
            ]);
        } else {
            echo json_encode([
                'status' => 'skipped',
                'message' => 'No product links found in channel post'
            ]);
        }
    }
} else {
    echo json_encode([
        'status' => 'ignored',
        'message' => 'Update type not supported'
    ]);
}

// Helper functions
function extractProductLinks($text) {
    $links = [];
    $pattern = '/https?:\/\/(?:www\.)?(amazon\.in|amazon\.com|flipkart\.com|meesho\.com|myntra\.com|ajio\.com)\/\S+/i';
    
    preg_match_all($pattern, $text, $matches);
    
    if (!empty($matches[0])) {
        foreach ($matches[0] as $link) {
            // Clean the link (remove tracking parameters if needed)
            $clean_link = cleanAffiliateLink($link);
            $links[] = $clean_link;
        }
    }
    
    return array_unique($links);
}

function cleanAffiliateLink($link) {
    // Parse URL and add your affiliate ID
    $parsed = parse_url($link);
    $host = $parsed['host'];
    
    // Add affiliate parameters based on site
    if (strpos($host, 'amazon') !== false) {
        // Add Amazon affiliate tag
        $affiliate_tag = getSetting('amazon_affiliate_tag') ?: 'flipmart-21';
        if (strpos($link, 'tag=') === false) {
            $separator = strpos($link, '?') !== false ? '&' : '?';
            $link .= $separator . 'tag=' . $affiliate_tag;
        }
    } elseif (strpos($host, 'flipkart') !== false) {
        // Add Flipkart affiliate ID
        $affiliate_id = getSetting('flipkart_affiliate_id') ?: 'flipmart';
        if (strpos($link, 'affid=') === false) {
            $separator = strpos($link, '?') !== false ? '&' : '?';
            $link .= $separator . 'affid=' . $affiliate_id;
        }
    }
    
    return $link;
}

function storeTelegramPost($channel_id, $message_id, $text, $links) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO telegram_posts (channel_id, message_id, post_text, extracted_links) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $channel_id,
            $message_id,
            $text,
            json_encode($links)
        ]);
        
        return $pdo->lastInsertId();
    } catch(PDOException $e) {
        error_log("Error storing telegram post: " . $e->getMessage());
        return false;
    }
}

function updateTelegramPost($post_id, $products_created, $error = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE telegram_posts 
            SET processed = 1, products_created = ?, processed_at = NOW(), error_message = ?
            WHERE id = ?
        ");
        $stmt->execute([$products_created, $error, $post_id]);
        return true;
    } catch(PDOException $e) {
        error_log("Error updating telegram post: " . $e->getMessage());
        return false;
    }
}

?>