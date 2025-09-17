<?php
/**
 * Test Data Setup and Cleanup Script
 * Author: Harries Hernandez
 * 
 * This script sets up test data for the Customer Management System
 * and provides cleanup functionality
 */

require_once 'config.php';

class TestDataManager {
    private $pdo;
    private $testCustomers = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->setupTestCustomers();
    }
    
    private function setupTestCustomers() {
        $this->testCustomers = [
            [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'email' => 'john.doe@test.com',
                'city' => 'New York',
                'country' => 'United States',
                'image_path' => null
            ],
            [
                'firstname' => 'Jane',
                'lastname' => 'Smith',
                'email' => 'jane.smith@test.com',
                'city' => 'Toronto',
                'country' => 'Canada',
                'image_path' => null
            ],
            [
                'firstname' => 'Hiroshi',
                'lastname' => 'Tanaka',
                'email' => 'hiroshi.tanaka@test.com',
                'city' => 'Tokyo',
                'country' => 'Japan',
                'image_path' => null
            ],
            [
                'firstname' => 'Emma',
                'lastname' => 'Wilson',
                'email' => 'emma.wilson@test.com',
                'city' => 'London',
                'country' => 'United Kingdom',
                'image_path' => null
            ],
            [
                'firstname' => 'Pierre',
                'lastname' => 'Dubois',
                'email' => 'pierre.dubois@test.com',
                'city' => 'Paris',
                'country' => 'France',
                'image_path' => null
            ],
            [
                'firstname' => 'Klaus',
                'lastname' => 'Mueller',
                'email' => 'klaus.mueller@test.com',
                'city' => 'Berlin',
                'country' => 'Germany',
                'image_path' => null
            ]
        ];
    }
    
    public function insertTestData() {
        echo "<h2>📝 Inserting Test Data</h2>\n";
        echo "<div style='font-family: monospace; background: #f0f8ff; padding: 15px; margin: 10px 0;'>\n";
        
        $inserted = 0;
        $errors = 0;
        
        foreach ($this->testCustomers as $customer) {
            try {
                $sql = "INSERT INTO customers (firstname, lastname, email, city, country, image_path) 
                        VALUES (?, ?, ?, ?, ?, ?) 
                        ON DUPLICATE KEY UPDATE 
                        firstname = VALUES(firstname), 
                        lastname = VALUES(lastname), 
                        city = VALUES(city), 
                        country = VALUES(country)";
                
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    $customer['firstname'],
                    $customer['lastname'],
                    $customer['email'],
                    $customer['city'],
                    $customer['country'],
                    $customer['image_path']
                ]);
                
                if ($result) {
                    echo "✅ Inserted: {$customer['firstname']} {$customer['lastname']} ({$customer['email']})\n";
                    $inserted++;
                } else {
                    echo "⚠️ Skipped: {$customer['firstname']} {$customer['lastname']} (already exists)\n";
                }
            } catch (Exception $e) {
                echo "❌ Error inserting {$customer['firstname']} {$customer['lastname']}: " . $e->getMessage() . "\n";
                $errors++;
            }
        }
        
        echo "</div>\n";
        echo "<p><strong>Summary:</strong> $inserted customers inserted, $errors errors</p>\n";
        
        return $inserted;
    }
    
    public function cleanupTestData() {
        echo "<h2>🧹 Cleaning Up Test Data</h2>\n";
        echo "<div style='font-family: monospace; background: #fff0f0; padding: 15px; margin: 10px 0;'>\n";
        
        $deleted = 0;
        
        foreach ($this->testCustomers as $customer) {
            try {
                $sql = "DELETE FROM customers WHERE email = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$customer['email']]);
                
                if ($stmt->rowCount() > 0) {
                    echo "🗑️ Deleted: {$customer['firstname']} {$customer['lastname']} ({$customer['email']})\n";
                    $deleted++;
                } else {
                    echo "ℹ️ Not found: {$customer['firstname']} {$customer['lastname']} ({$customer['email']})\n";
                }
            } catch (Exception $e) {
                echo "❌ Error deleting {$customer['firstname']} {$customer['lastname']}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "</div>\n";
        echo "<p><strong>Summary:</strong> $deleted customers deleted</p>\n";
        
        return $deleted;
    }
    
    public function showTestData() {
        echo "<h2>📋 Current Test Data</h2>\n";
        echo "<div style='font-family: monospace; background: #f0fff0; padding: 15px; margin: 10px 0;'>\n";
        
        try {
            $sql = "SELECT * FROM customers WHERE email LIKE '%@test.com' ORDER BY created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($customers)) {
                echo "No test data found in database.\n";
            } else {
                echo "Found " . count($customers) . " test customers:\n\n";
                foreach ($customers as $customer) {
                    echo "ID: {$customer['id']}\n";
                    echo "Name: {$customer['firstname']} {$customer['lastname']}\n";
                    echo "Email: {$customer['email']}\n";
                    echo "Location: {$customer['city']}, {$customer['country']}\n";
                    echo "Created: {$customer['created_at']}\n";
                    echo str_repeat("-", 40) . "\n";
                }
            }
        } catch (Exception $e) {
            echo "❌ Error retrieving test data: " . $e->getMessage() . "\n";
        }
        
        echo "</div>\n";
    }
    
    public function generateTestUrls() {
        echo "<h2>🔗 Test URLs</h2>\n";
        echo "<div style='background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #007cba;'>\n";
        
        $baseUrl = "http://localhost/engineering-excercise";
        
        echo "<h3>Customer Review URLs:</h3>\n";
        foreach ($this->testCustomers as $customer) {
            $url = $baseUrl . "/review.php?email=" . urlencode($customer['email']);
            echo "<p><a href='$url' target='_blank'>Review {$customer['firstname']} {$customer['lastname']}</a></p>\n";
        }
        
        echo "<h3>Other Test URLs:</h3>\n";
        echo "<p><a href='{$baseUrl}/index.php' target='_blank'>Customer Entry Form</a></p>\n";
        echo "<p><a href='{$baseUrl}/review.php' target='_blank'>Customer Review (No Email)</a></p>\n";
        echo "<p><a href='{$baseUrl}/test_cases.php' target='_blank'>Test Cases Documentation</a></p>\n";
        echo "<p><a href='{$baseUrl}/test_runner.php' target='_blank'>Automated Test Runner</a></p>\n";
        
        echo "</div>\n";
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $testManager = new TestDataManager($pdo);
    
    switch ($action) {
        case 'insert':
            $testManager->insertTestData();
            break;
        case 'cleanup':
            $testManager->cleanupTestData();
            break;
        case 'show':
            $testManager->showTestData();
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Data Manager - Engineering Exercise</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">🧪 Test Data Manager</h1>
            
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-blue-700">Test Data Management</h2>
                <p class="text-gray-700 mb-4">
                    This tool helps you manage test data for the Customer Management System. 
                    Use it to insert sample customers, clean up test data, and generate test URLs.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <form method="POST" class="bg-green-50 p-4 rounded-lg">
                    <input type="hidden" name="action" value="insert">
                    <h3 class="text-lg font-semibold mb-2 text-green-800">Insert Test Data</h3>
                    <p class="text-sm text-gray-600 mb-3">Add 6 sample customers to the database</p>
                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Insert Test Customers
                    </button>
                </form>
                
                <form method="POST" class="bg-red-50 p-4 rounded-lg">
                    <input type="hidden" name="action" value="cleanup">
                    <h3 class="text-lg font-semibold mb-2 text-red-800">Cleanup Test Data</h3>
                    <p class="text-sm text-gray-600 mb-3">Remove all test customers from database</p>
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Cleanup Test Data
                    </button>
                </form>
                
                <form method="POST" class="bg-blue-50 p-4 rounded-lg">
                    <input type="hidden" name="action" value="show">
                    <h3 class="text-lg font-semibold mb-2 text-blue-800">Show Current Data</h3>
                    <p class="text-sm text-gray-600 mb-3">Display all test customers in database</p>
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Show Test Data
                    </button>
                </form>
            </div>

            <!-- Test Data Information -->
            <div class="bg-gray-50 p-6 rounded-lg mb-8">
                <h3 class="text-lg font-semibold mb-3 text-gray-800">Sample Test Customers</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <strong>John Doe</strong><br>
                        Email: john.doe@test.com<br>
                        Location: New York, United States
                    </div>
                    <div>
                        <strong>Jane Smith</strong><br>
                        Email: jane.smith@test.com<br>
                        Location: Toronto, Canada
                    </div>
                    <div>
                        <strong>Hiroshi Tanaka</strong><br>
                        Email: hiroshi.tanaka@test.com<br>
                        Location: Tokyo, Japan
                    </div>
                    <div>
                        <strong>Emma Wilson</strong><br>
                        Email: emma.wilson@test.com<br>
                        Location: London, United Kingdom
                    </div>
                    <div>
                        <strong>Pierre Dubois</strong><br>
                        Email: pierre.dubois@test.com<br>
                        Location: Paris, France
                    </div>
                    <div>
                        <strong>Klaus Mueller</strong><br>
                        Email: klaus.mueller@test.com<br>
                        Location: Berlin, Germany
                    </div>
                </div>
            </div>

            <!-- Generate Test URLs -->
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'show') {
                $testManager = new TestDataManager($pdo);
                $testManager->generateTestUrls();
            }
            ?>

            <!-- Navigation -->
            <div class="text-center mt-8 space-x-4">
                <a href="test_runner.php" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 inline-block">
                    Run Automated Tests
                </a>
                <a href="index.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 inline-block">
                    Customer Entry Form
                </a>
                <a href="review.php" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 inline-block">
                    Customer Review
                </a>
            </div>
        </div>
    </div>
</body>
</html>
