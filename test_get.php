<?php
// ULTRA SIMPLE GET TEST - NO DEPENDENCIES
?>
<!DOCTYPE html>
<html>
<head>
    <title>GET Parameter Test</title>
</head>
<body style="font-family: Arial; padding: 40px; background: #f0f0f0;">
    <h1 style="color: #ff6b6b;">🔬 GET Parameter Test</h1>
    
    <div style="background: white; padding: 30px; border-radius: 8px; margin: 20px 0;">
        <h2>Test Form</h2>
        <form method="GET" action="test_get.php">
            <input type="text" name="testquery" placeholder="Type anything..." style="padding: 10px; font-size: 16px; width: 300px;">
            <button type="submit" style="padding: 10px 20px; font-size: 16px; background: #4CAF50; color: white; border: none; cursor: pointer;">Submit Test</button>
        </form>
    </div>
    
    <div style="background: #333; color: #0f0; padding: 30px; border-radius: 8px; font-family: monospace; margin: 20px 0;">
        <h2 style="color: #0f0;">RAW $_GET Array:</h2>
        <pre style="color: #0f0;"><?php print_r($_GET); ?></pre>
        
        <h2 style="color: #0f0; margin-top: 30px;">Individual Values:</h2>
        <?php if (!empty($_GET)): ?>
            <?php foreach ($_GET as $key => $value): ?>
                <div style="background: rgba(0,255,0,0.1); padding: 10px; margin: 5px 0; border-left: 3px solid #0f0;">
                    <strong>[<?= htmlspecialchars($key) ?>]</strong> = "<?= htmlspecialchars($value) ?>"
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="background: rgba(255,0,0,0.3); padding: 15px; border-left: 3px solid red; color: #ff6b6b;">
                ❌ NO GET PARAMETERS RECEIVED
            </div>
        <?php endif; ?>
        
        <h2 style="color: #0f0; margin-top: 30px;">Current URL:</h2>
        <div style="background: rgba(0,255,0,0.1); padding: 10px; word-break: break-all;">
            <?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'N/A') ?>
        </div>
        
        <h2 style="color: #0f0; margin-top: 30px;">Query String:</h2>
        <div style="background: rgba(0,255,0,0.1); padding: 10px; word-break: break-all;">
            <?= htmlspecialchars($_SERVER['QUERY_STRING'] ?? 'EMPTY') ?>
        </div>
    </div>
    
    <div style="background: #fff3cd; padding: 20px; border-radius: 8px; border-left: 5px solid #ffc107;">
        <h3>Instructions:</h3>
        <ol>
            <li>Type something in the input box above</li>
            <li>Click "Submit Test"</li>
            <li>Check if the green box shows your input</li>
            <li>If it DOES work here but NOT on contacts_list.php, we have a specific file issue</li>
            <li>If it DOESN'T work here either, we have a server/PHP configuration issue</li>
        </ol>
    </div>
</body>
</html>
