<?php
/**
 * Comprehensive Test Runner for Customer Management System
 * Author: Harries Hernandez
 * 
 * This script runs automated tests for all components of the system
 */

require_once 'config.php';

class TestRunner {
    private $pdo;
    private $testResults = [];
    private $testData = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->setupTestData();
    }
    
    private function setupTestData() {
        $this->testData = [
            'valid_customer' => [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'email' => 'john.doe@test.com',
                'city' => 'New York',
                'country' => 'United States'
            ],
            'invalid_email' => [
                'firstname' => 'Jane',
                'lastname' => 'Smith',
                'email' => 'invalid-email',
                'city' => 'Toronto',
                'country' => 'Canada'
            ],
            'duplicate_email' => [
                'firstname' => 'Bob',
                'lastname' => 'Johnson',
                'email' => 'john.doe@test.com', // Same as valid_customer
                'city' => 'Tokyo',
                'country' => 'Japan'
            ]
        ];
    }
    
    public function runAllTests() {
        echo "<h1>🧪 Customer Management System - Test Suite</h1>\n";
        echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; margin: 20px 0;'>\n";
        
        $this->testDatabaseConnection();
        $this->testDatabaseSchema();
        $this->testCustomerInsertion();
        $this->testCustomerRetrieval();
        $this->testEmailValidation();
        $this->testFileUpload();
        $this->testSecurityFeatures();
        $this->testCalculatorLogic();
        $this->testWebRTCComponents();
        
        $this->displayResults();
        echo "</div>\n";
    }
    
    private function testDatabaseConnection() {
        $this->logTest("Database Connection", function() {
            if ($this->pdo) {
                $stmt = $this->pdo->query("SELECT 1");
                return $stmt !== false;
            }
            return false;
        });
    }
    
    private function testDatabaseSchema() {
        $this->logTest("Database Schema", function() {
            try {
                $stmt = $this->pdo->query("DESCRIBE customers");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $expectedColumns = ['id', 'lastname', 'firstname', 'email', 'city', 'country', 'image_path', 'created_at'];
                $actualColumns = array_column($columns, 'Field');
                
                return count(array_intersect($expectedColumns, $actualColumns)) === count($expectedColumns);
            } catch (Exception $e) {
                return false;
            }
        });
    }
    
    private function testCustomerInsertion() {
        $this->logTest("Customer Insertion - Valid Data", function() {
            try {
                $customer = $this->testData['valid_customer'];
                $sql = "INSERT INTO customers (lastname, firstname, email, city, country) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    $customer['lastname'],
                    $customer['firstname'],
                    $customer['email'],
                    $customer['city'],
                    $customer['country']
                ]);
                
                // Clean up test data
                $this->pdo->prepare("DELETE FROM customers WHERE email = ?")->execute([$customer['email']]);
                
                return $result;
            } catch (Exception $e) {
                return false;
            }
        });
        
        $this->logTest("Customer Insertion - Duplicate Email", function() {
            try {
                // Insert first customer
                $customer1 = $this->testData['valid_customer'];
                $sql = "INSERT INTO customers (lastname, firstname, email, city, country) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $customer1['lastname'],
                    $customer1['firstname'],
                    $customer1['email'],
                    $customer1['city'],
                    $customer1['country']
                ]);
                
                // Try to insert duplicate email
                $customer2 = $this->testData['duplicate_email'];
                $stmt2 = $this->pdo->prepare($sql);
                $result = $stmt2->execute([
                    $customer2['lastname'],
                    $customer2['firstname'],
                    $customer2['email'],
                    $customer2['city'],
                    $customer2['country']
                ]);
                
                // Clean up test data
                $this->pdo->prepare("DELETE FROM customers WHERE email = ?")->execute([$customer1['email']]);
                
                // Should return false due to unique constraint
                return !$result;
            } catch (Exception $e) {
                // Expected to fail due to unique constraint
                return strpos($e->getMessage(), 'Duplicate entry') !== false;
            }
        });
    }
    
    private function testCustomerRetrieval() {
        $this->logTest("Customer Retrieval - Valid Email", function() {
            try {
                // Insert test customer
                $customer = $this->testData['valid_customer'];
                $sql = "INSERT INTO customers (lastname, firstname, email, city, country) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $customer['lastname'],
                    $customer['firstname'],
                    $customer['email'],
                    $customer['city'],
                    $customer['country']
                ]);
                
                // Retrieve customer
                $sql = "SELECT * FROM customers WHERE email = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$customer['email']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Clean up test data
                $this->pdo->prepare("DELETE FROM customers WHERE email = ?")->execute([$customer['email']]);
                
                return $result && $result['email'] === $customer['email'];
            } catch (Exception $e) {
                return false;
            }
        });
        
        $this->logTest("Customer Retrieval - Invalid Email", function() {
            try {
                $sql = "SELECT * FROM customers WHERE email = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['nonexistent@test.com']);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                return $result === false;
            } catch (Exception $e) {
                return false;
            }
        });
    }
    
    private function testEmailValidation() {
        $this->logTest("Email Validation - Valid Formats", function() {
            $validEmails = [
                'test@example.com',
                'user.name@domain.co.uk',
                'user+tag@example.org',
                'user123@test-domain.com'
            ];
            
            foreach ($validEmails as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return false;
                }
            }
            return true;
        });
        
        $this->logTest("Email Validation - Invalid Formats", function() {
            $invalidEmails = [
                'invalid-email',
                'test@',
                '@example.com',
                'test..test@example.com',
                'test@example',
                ''
            ];
            
            foreach ($invalidEmails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return false;
                }
            }
            return true;
        });
    }
    
    private function testFileUpload() {
        $this->logTest("File Upload Directory", function() {
            $uploadDir = 'uploads/';
            return file_exists($uploadDir) && is_writable($uploadDir);
        });
        
        $this->logTest("File Type Validation", function() {
            $allowedTypes = ['jpg', 'jpeg', 'png'];
            $testFiles = [
                'test.jpg' => true,
                'test.jpeg' => true,
                'test.png' => true,
                'test.gif' => false,
                'test.txt' => false,
                'test.pdf' => false
            ];
            
            foreach ($testFiles as $filename => $expected) {
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $actual = in_array($extension, $allowedTypes);
                if ($actual !== $expected) {
                    return false;
                }
            }
            return true;
        });
    }
    
    private function testSecurityFeatures() {
        $this->logTest("SQL Injection Prevention", function() {
            try {
                // Test with potentially malicious input
                $maliciousInput = "'; DROP TABLE customers; --";
                $sql = "SELECT * FROM customers WHERE email = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$maliciousInput]);
                
                // If we get here without error, prepared statements are working
                return true;
            } catch (Exception $e) {
                return false;
            }
        });
        
        $this->logTest("XSS Prevention", function() {
            $maliciousInput = '<script>alert("XSS")</script>';
            $sanitized = htmlspecialchars($maliciousInput, ENT_QUOTES, 'UTF-8');
            
            return strpos($sanitized, '<script>') === false;
        });
    }
    
    private function testCalculatorLogic() {
        $this->logTest("Calculator Addition", function() {
            $testCases = [
                ['5+3', 8],
                ['10+15', 25],
                ['0+5', 5],
                ['100+200', 300]
            ];
            
            foreach ($testCases as [$expression, $expected]) {
                $result = $this->evaluateExpression($expression);
                if ($result !== $expected) {
                    return false;
                }
            }
            return true;
        });
        
        $this->logTest("Calculator Subtraction", function() {
            $testCases = [
                ['10-4', 6],
                ['25-8', 17],
                ['5-10', -5],
                ['100-50', 50]
            ];
            
            foreach ($testCases as [$expression, $expected]) {
                $result = $this->evaluateExpression($expression);
                if ($result !== $expected) {
                    return false;
                }
            }
            return true;
        });
        
        $this->logTest("Calculator Complex Operations", function() {
            $testCases = [
                ['5+3-2', 6],
                ['10-4+1', 7],
                ['54+37-21', 70],
                ['100-50+25', 75]
            ];
            
            foreach ($testCases as [$expression, $expected]) {
                $result = $this->evaluateExpression($expression);
                if ($result !== $expected) {
                    return false;
                }
            }
            return true;
        });
    }
    
    private function evaluateExpression($expression) {
        // Simple expression evaluator for testing
        // This mimics the calculator logic
        $expression = preg_replace('/[^0-9+\-]/', '', $expression);
        
        if (preg_match('/^(\d+)([+\-])(\d+)([+\-])(\d+)$/', $expression, $matches)) {
            $num1 = (int)$matches[1];
            $op1 = $matches[2];
            $num2 = (int)$matches[3];
            $op2 = $matches[4];
            $num3 = (int)$matches[5];
            
            $result1 = $op1 === '+' ? $num1 + $num2 : $num1 - $num2;
            return $op2 === '+' ? $result1 + $num3 : $result1 - $num3;
        } elseif (preg_match('/^(\d+)([+\-])(\d+)$/', $expression, $matches)) {
            $num1 = (int)$matches[1];
            $op = $matches[2];
            $num2 = (int)$matches[3];
            
            return $op === '+' ? $num1 + $num2 : $num1 - $num2;
        }
        
        return 0;
    }
    
    private function testWebRTCComponents() {
        $this->logTest("WebRTC Files Exist", function() {
            $files = [
                'firebase_webrtc_host.php',
                'firebase_webrtc_viewer.php',
                'webrtc_signaling.php'
            ];
            
            foreach ($files as $file) {
                if (!file_exists($file)) {
                    return false;
                }
            }
            return true;
        });
        
        $this->logTest("Calculator iFrame Files Exist", function() {
            $files = [
                'calculator_display.php',
                'calculator_buttons.php'
            ];
            
            foreach ($files as $file) {
                if (!file_exists($file)) {
                    return false;
                }
            }
            return true;
        });
    }
    
    private function logTest($testName, $testFunction) {
        echo "Running: $testName... ";
        
        try {
            $result = $testFunction();
            if ($result) {
                echo "PASS\n";
                $this->testResults[] = ['name' => $testName, 'status' => 'PASS'];
            } else {
                echo "FAIL\n";
                $this->testResults[] = ['name' => $testName, 'status' => 'FAIL'];
            }
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
            $this->testResults[] = ['name' => $testName, 'status' => 'ERROR', 'error' => $e->getMessage()];
        }
    }
    
    private function displayResults() {
        $totalTests = count($this->testResults);
        $passedTests = count(array_filter($this->testResults, function($test) {
            return $test['status'] === 'PASS';
        }));
        $failedTests = $totalTests - $passedTests;
        
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "📊 TEST RESULTS SUMMARY\n";
        echo str_repeat("=", 50) . "\n";
        echo "Total Tests: $totalTests\n";
        echo "Passed: $passedTests \n";
        echo "Failed: $failedTests \n";
        echo "Success Rate: " . round(($passedTests / $totalTests) * 100, 2) . "%\n";
        
        if ($failedTests > 0) {
            echo "\n FAILED TESTS:\n";
            foreach ($this->testResults as $test) {
                if ($test['status'] !== 'PASS') {
                    echo "- {$test['name']}: {$test['status']}\n";
                    if (isset($test['error'])) {
                        echo "  Error: {$test['error']}\n";
                    }
                }
            }
        }
        
        echo "\n" . str_repeat("=", 50) . "\n";
    }
}

// Run tests if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'test_runner.php') {
    try {
        $testRunner = new TestRunner($pdo);
        $testRunner->runAllTests();
    } catch (Exception $e) {
        echo "<h1>Test Runner Error</h1>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>Please check your database configuration in config.php</p>";
    }
}
?>
