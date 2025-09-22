<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Simple security check for setup page
$setup_key = $_GET['key'] ?? '';
$required_key = getSetting('setup_access_key') ?: 'flipmart-admin-2025';

if ($setup_key !== $required_key) {
    die('Access Denied. Add ?key=' . $required_key . ' to URL.');
}

// Telegram Bot Setup Page
$webhook_url = SITE_URL . '/api/telegram_webhook.php';
$bot_token = getSetting('telegram_bot_token');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save_settings') {
        $bot_token = sanitizeInput($_POST['bot_token']);
        $channel_username = sanitizeInput($_POST['channel_username']);
        $amazon_affiliate = sanitizeInput($_POST['amazon_affiliate']);
        $flipkart_affiliate = sanitizeInput($_POST['flipkart_affiliate']);
        
        // Save settings
        updateSetting('telegram_bot_token', $bot_token);
        updateSetting('telegram_channel_username', $channel_username);
        updateSetting('amazon_affiliate_tag', $amazon_affiliate);
        updateSetting('flipkart_affiliate_id', $flipkart_affiliate);
        
        $success_message = "Settings saved successfully!";
    }
    
    if ($action === 'set_webhook' && !empty($bot_token)) {
        $webhook_url_with_token = $webhook_url . '?token=' . urlencode($bot_token);
        $result = setTelegramWebhook($bot_token, $webhook_url_with_token);
        
        if ($result) {
            $success_message = "Webhook set successfully! Your bot is now ready to receive updates.";
        } else {
            $error_message = "Failed to set webhook. Please check your bot token.";
        }
    }
}

function getSetting($key) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : '';
    } catch(PDOException $e) {
        return '';
    }
}

function updateSetting($key, $value) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

function setTelegramWebhook($bot_token, $webhook_url) {
    $url = "https://api.telegram.org/bot{$bot_token}/setWebhook";
    $secret_token = bin2hex(random_bytes(16)); // Generate secure secret
    updateSetting('telegram_secret_token', $secret_token);
    
    $data = [
        'url' => $webhook_url,
        'secret_token' => $secret_token
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    return isset($result['ok']) && $result['ok'] === true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram Bot Setup - FlipMart</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .setup-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 1rem;
        }
        .btn:hover {
            background: #5a6fd8;
        }
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
        .info-box {
            background: #e3f2fd;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1>Telegram Bot Setup</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="info-box">
            <h3>Setup Instructions:</h3>
            <ol>
                <li>Create a Telegram bot by messaging @BotFather</li>
                <li>Get your bot token and enter it below</li>
                <li>Add your channel username (without @)</li>
                <li>Add your affiliate IDs for monetization</li>
                <li>Click "Set Webhook" to activate automation</li>
            </ol>
            <p><strong>Webhook URL:</strong> <?php echo $webhook_url; ?></p>
            <p><strong>Setup URL:</strong> Add <code>?key=<?php echo $required_key; ?></code> to access this page</p>
        </div>
        
        <form method="POST">
            <input type="hidden" name="action" value="save_settings">
            
            <div class="form-group">
                <label for="bot_token">Telegram Bot Token</label>
                <input type="text" id="bot_token" name="bot_token" 
                       value="<?php echo htmlspecialchars(getSetting('telegram_bot_token')); ?>" 
                       placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz" required>
            </div>
            
            <div class="form-group">
                <label for="channel_username">Channel Username (without @)</label>
                <input type="text" id="channel_username" name="channel_username" 
                       value="<?php echo htmlspecialchars(getSetting('telegram_channel_username')); ?>" 
                       placeholder="your_channel_name">
            </div>
            
            <div class="form-group">
                <label for="amazon_affiliate">Amazon Affiliate Tag</label>
                <input type="text" id="amazon_affiliate" name="amazon_affiliate" 
                       value="<?php echo htmlspecialchars(getSetting('amazon_affiliate_tag')); ?>" 
                       placeholder="your-affiliate-tag">
            </div>
            
            <div class="form-group">
                <label for="flipkart_affiliate">Flipkart Affiliate ID</label>
                <input type="text" id="flipkart_affiliate" name="flipkart_affiliate" 
                       value="<?php echo htmlspecialchars(getSetting('flipkart_affiliate_id')); ?>" 
                       placeholder="your-affiliate-id">
            </div>
            
            <button type="submit" class="btn">Save Settings</button>
        </form>
        
        <?php if (!empty(getSetting('telegram_bot_token'))): ?>
            <hr style="margin: 2rem 0;">
            <form method="POST">
                <input type="hidden" name="action" value="set_webhook">
                <button type="submit" class="btn">Set Webhook</button>
                <p style="margin-top: 1rem; color: #666;">
                    Click this after saving your bot token to activate the webhook.
                </p>
            </form>
        <?php endif; ?>
        
        <hr style="margin: 2rem 0;">
        <p><a href="../?page=home">&larr; Back to Website</a></p>
    </div>
</body>
</html>