<?php
require_once 'config.php';

$customer = null;
$email = $_GET['email'] ?? '';

if ($email) {
    try {
        $sql = "SELECT * FROM customers WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Error retrieving customer data: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Information Review</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-3xl font-bold text-center mb-4 text-gray-800">Customer Information Review</h1>
                
                <form method="GET" class="max-w-md mx-auto">
                    <div class="flex space-x-2">
                        <input type="email" name="email" placeholder="Enter customer email" 
                               value="<?php echo htmlspecialchars($email); ?>"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" 
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Customer Details</h2>
                    
                    <?php if ($customer): ?>
                        <div class="space-y-4">
                            <?php if ($customer['image_path'] && file_exists($customer['image_path'])): ?>
                                <div class="text-center">
                                    <img src="<?php echo htmlspecialchars($customer['image_path']); ?>" 
                                         alt="Customer Photo" class="h-64 mx-auto object-cover">
                                </div>
                            <?php endif; ?>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="font-semibold text-gray-700">First Name:</label>
                                    <p class="text-gray-900"><?php echo htmlspecialchars($customer['firstname']); ?></p>
                                </div>
                                <div>
                                    <label class="font-semibold text-gray-700">Last Name:</label>
                                    <p class="text-gray-900"><?php echo htmlspecialchars($customer['lastname']); ?></p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="font-semibold text-gray-700">Email:</label>
                                <p class="text-gray-900"><?php echo htmlspecialchars($customer['email']); ?></p>
                            </div>
                            
                            <div>
                                <label class="font-semibold text-gray-700">City:</label>
                                <p class="text-gray-900"><?php echo htmlspecialchars($customer['city']); ?></p>
                            </div>
                            
                            <div>
                                <label class="font-semibold text-gray-700">Country:</label>
                                <p class="text-gray-900"><?php echo htmlspecialchars($customer['country']); ?></p>
                            </div>
                        </div>
                    <?php elseif ($email): ?>
                        <div class="text-center text-gray-500">
                            <p>No customer found with email: <?php echo htmlspecialchars($email); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-gray-500">
                            <p>Enter an email address to search for customer information</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Mini Calculator</h2>
                    
                    <div class="mb-4">
                        <iframe id="calculator-display" src="calculator_display.php" 
                                class="w-full h-16 border border-gray-300 rounded-md"></iframe>
                    </div>
                    
                    <div class="mb-4">
                        <iframe id="calculator-buttons" src="calculator_buttons.php" 
                                class="w-full h-64 border border-gray-300 rounded-md"></iframe>
                    </div>
                    
                    <div>
                        <label for="result" class="block text-sm font-medium text-gray-700 mb-2">Result:</label>
                        <input type="text" id="result" readonly 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-2xl font-bold mb-4 text-gray-800">Screen Share Utility</h2>
                <p class="text-gray-600 mb-4 text-center">
                    Real-time screen sharing using Firebase and WebRTC
                </p>
                <div class="text-center space-y-4">
                    <div>
                        <a href="firebase_webrtc_host.php" 
                           class="bg-blue-600 text-white px-8 py-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 inline-block text-lg font-semibold">
                            Firebase + WebRTC Screen Sharing (Debug)
                        </a>
                    </div>
                </div>
                <div class="mt-4 text-center text-sm text-gray-500">
                    <p><strong>Firebase + WebRTC Screen Sharing:</strong></p>
                    <p>1. Host starts screen sharing and gets a share URL</p>
                    <p>2. Share the URL with viewers on any device</p>
                    <p>3. Real-time peer-to-peer screen sharing between computers</p>
                </div>
            </div>

            <div class="text-center mt-6">
                <a href="index.php" class="text-blue-600 hover:text-blue-800 underline">Back to Customer Entry</a>
            </div>
        </div>
    </div>

    <script>
        // calculator communication
        let currentExpression = '';
        
        // listen for messages from calculator iframe - part of reqs
        window.addEventListener('message', function(event) {
            if (event.data.type === 'calculator-input') {
                currentExpression = event.data.value;
                updateDisplay();
            } else if (event.data.type === 'calculator-result') {
                document.getElementById('result').value = event.data.value;
            }
        });
        
        function updateDisplay() {
            const displayFrame = document.getElementById('calculator-display');
            displayFrame.contentWindow.postMessage({
                type: 'update-display',
                value: currentExpression
            }, '*');
        }
        
        // screen share join functionality
        document.getElementById('join-share').addEventListener('click', function() {
            const shareId = document.getElementById('share-id-input').value.trim();
            if (shareId) {
                window.location.href = 'screen_share_viewer.php?id=' + encodeURIComponent(shareId);
            } else {
                alert('Please enter a Share ID');
            }
        });
        
        // allow Enter key to join
        document.getElementById('share-id-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('join-share').click();
            }
        });
    </script>
</body>
</html>
