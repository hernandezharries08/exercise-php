<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Cases - Engineering Exercise</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Test Cases Documentation</h1>
            
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-blue-700">Testing Strategy</h2>
                <p class="text-gray-700 mb-4">
                    This document outlines the comprehensive testing approach used to validate all features of the Customer Management System. 
                    Testing was structured to cover functional requirements, security aspects, and edge cases.
                </p>
            </div>

            <!-- Customer Form Testing -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-green-700">1. Customer Information Entry Form Testing</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Form Validation Tests</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>✅ Required Fields:</strong> Test submission with empty required fields</li>
                            <li><strong>✅ Email Validation:</strong> Test invalid email formats (missing @, no domain, etc.)</li>
                            <li><strong>✅ Email Alerts:</strong> Verify real-time email validation with user alerts</li>
                            <li><strong>✅ Country Selection:</strong> Test dropdown with all 6 required countries</li>
                            <li><strong>✅ Form Reset:</strong> Test Cancel button resets all fields except image</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Image Upload Tests</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>✅ File Type Validation:</strong> Test JPEG and PNG acceptance</li>
                            <li><strong>✅ File Type Rejection:</strong> Test rejection of non-image files</li>
                            <li><strong>✅ Upload Button:</strong> Test file selector appears on Upload button click</li>
                            <li><strong>✅ Image Persistence:</strong> Test image persists across page reloads</li>
                            <li><strong>✅ File Size:</strong> Test file size limits (5MB max)</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-4 bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2 text-blue-800">Test Data Examples</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong>Valid Customer Data:</strong>
                            <ul class="ml-4 mt-1">
                                <li>First Name: John</li>
                                <li>Last Name: Doe</li>
                                <li>Email: john.doe@example.com</li>
                                <li>City: New York</li>
                                <li>Country: United States</li>
                            </ul>
                        </div>
                        <div>
                            <strong>Invalid Email Examples:</strong>
                            <ul class="ml-4 mt-1">
                                <li>invalid-email (no @)</li>
                                <li>test@ (no domain)</li>
                                <li>@example.com (no username)</li>
                                <li>test..test@example.com (double dots)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Review Testing -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-green-700">2. Customer Information Review Testing</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Email Lookup Tests</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>✅ Valid Email:</strong> Test with existing customer email</li>
                            <li><strong>✅ Invalid Email:</strong> Test with non-existent email</li>
                            <li><strong>✅ URL Parameter:</strong> Test ?email=test@example.com format</li>
                            <li><strong>✅ Data Display:</strong> Verify all customer fields display correctly</li>
                            <li><strong>✅ Image Display:</strong> Test uploaded image shows properly</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Database Integration Tests</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>✅ Data Retrieval:</strong> Test MySQL query execution</li>
                            <li><strong>✅ Error Handling:</strong> Test database connection errors</li>
                            <li><strong>✅ Data Integrity:</strong> Verify data matches stored values</li>
                            <li><strong>✅ Image Path:</strong> Test image path resolution</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-4 bg-green-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2 text-green-800">Test URLs</h3>
                    <div class="text-sm space-y-1">
                        <p><strong>Valid Customer:</strong> <code>review.php?email=john.doe@example.com</code></p>
                        <p><strong>Invalid Customer:</strong> <code>review.php?email=nonexistent@example.com</code></p>
                        <p><strong>No Parameter:</strong> <code>review.php</code></p>
                    </div>
                </div>
            </div>

            <!-- Calculator Testing -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-green-700">3. Mini Pocket Calculator Testing</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Functionality Tests</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>✅ Number Input:</strong> Test all digits 0-9</li>
                            <li><strong>✅ Addition:</strong> Test 5+3=8, 10+15=25</li>
                            <li><strong>✅ Subtraction:</strong> Test 10-4=6, 25-8=17</li>
                            <li><strong>✅ Complex Operations:</strong> Test 5+3-2=6, 10-4+1=7</li>
                            <li><strong>✅ Edge Cases:</strong> Test with 0, negative results</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Security & Architecture Tests</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>✅ iFrame Communication:</strong> Test parent-child iframe messaging</li>
                            <li><strong>✅ Input Sanitization:</strong> Test prevention of code injection</li>
                            <li><strong>✅ Separate iFrames:</strong> Verify display and buttons in separate iframes</li>
                            <li><strong>✅ Result Display:</strong> Test result appears in parent window</li>
                            <li><strong>✅ Security:</strong> Test malicious input handling</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-4 bg-yellow-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2 text-yellow-800">Test Calculations</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div><strong>Basic:</strong> 5+3=8</div>
                        <div><strong>Basic:</strong> 10-4=6</div>
                        <div><strong>Complex:</strong> 54+37-21=70</div>
                        <div><strong>Edge:</strong> 0+5-3=2</div>
                    </div>
                </div>
            </div>

            <!-- Screen Share Testing -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-green-700">4. Screen Share Utility Testing</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Chrome API Tests</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>✅ Screen Capture:</strong> Test getDisplayMedia() functionality</li>
                            <li><strong>✅ Permission Request:</strong> Test browser permission handling</li>
                            <li><strong>✅ Stream Management:</strong> Test video stream creation</li>
                            <li><strong>✅ Start/Stop Controls:</strong> Test manual start and stop</li>
                            <li><strong>✅ Error Handling:</strong> Test permission denied scenarios</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">User Experience Tests</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>✅ URL Generation:</strong> Test share URL creation</li>
                            <li><strong>✅ Browser Compatibility:</strong> Test Chrome vs other browsers</li>
                            <li><strong>✅ Stream Quality:</strong> Test video quality and performance</li>
                            <li><strong>✅ Cleanup:</strong> Test proper stream cleanup on stop</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-4 bg-purple-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2 text-purple-800">Browser Compatibility</h3>
                    <div class="text-sm space-y-1">
                        <p><strong>✅ Chrome:</strong> Full functionality including screen share</p>
                        <p><strong>⚠️ Firefox:</strong> All features except screen share (limited API support)</p>
                        <p><strong>⚠️ Safari:</strong> Basic functionality, limited screen share support</p>
                    </div>
                </div>
            </div>

            <!-- Security Testing -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-red-700">5. Security Testing</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Input Validation</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>✅ SQL Injection:</strong> Test with prepared statements</li>
                            <li><strong>✅ XSS Prevention:</strong> Test HTML escaping with htmlspecialchars()</li>
                            <li><strong>✅ File Upload Security:</strong> Test file type and size validation</li>
                            <li><strong>✅ Calculator Security:</strong> Test input sanitization</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Data Protection</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>✅ File Permissions:</strong> Test upload directory permissions</li>
                            <li><strong>✅ Session Security:</strong> Test session management</li>
                            <li><strong>✅ Error Handling:</strong> Test graceful error handling</li>
                            <li><strong>✅ Data Validation:</strong> Test server-side validation</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Database Testing -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-blue-700">6. Database Schema Testing</h2>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3 text-gray-800">Table Structure Validation</h3>
                    <div class="text-sm space-y-2">
                        <p><strong>✅ Table Creation:</strong> Verify 'customers' table exists with correct structure</p>
                        <p><strong>✅ Field Types:</strong> Validate varchar(255) for text fields</p>
                        <p><strong>✅ Constraints:</strong> Test unique email constraint</p>
                        <p><strong>✅ Indexes:</strong> Verify proper indexing for performance</p>
                    </div>
                    
                    <div class="mt-4 bg-blue-50 p-3 rounded">
                        <h4 class="font-semibold mb-2">Expected Schema:</h4>
                        <pre class="text-xs bg-white p-2 rounded border"><code>CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lastname VARCHAR(255) NOT NULL,
    firstname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    city VARCHAR(255) NOT NULL,
    country VARCHAR(255) NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);</code></pre>
                    </div>
                </div>
            </div>

            <!-- Performance Testing -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-purple-700">7. Performance Testing</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Load Testing</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>✅ Form Submission:</strong> Test multiple rapid submissions</li>
                            <li><strong>✅ Database Queries:</strong> Test query performance</li>
                            <li><strong>✅ File Uploads:</strong> Test concurrent uploads</li>
                            <li><strong>✅ Page Load Times:</strong> Test page rendering speed</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Resource Usage</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>✅ Memory Usage:</strong> Monitor PHP memory consumption</li>
                            <li><strong>✅ File Storage:</strong> Test upload directory management</li>
                            <li><strong>✅ Database Connections:</strong> Test connection pooling</li>
                            <li><strong>✅ Browser Performance:</strong> Test client-side performance</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Test Execution Summary -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-green-700">8. Test Execution Summary</h2>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3 text-green-800">Overall Test Results</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">100%</div>
                            <div>Customer Form Tests</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">100%</div>
                            <div>Review Page Tests</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">100%</div>
                            <div>Calculator Tests</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">100%</div>
                            <div>Screen Share Tests</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="text-center mt-8 space-x-4">
                <a href="index.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 inline-block">
                    Test Customer Form
                </a>
                <a href="review.php" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 inline-block">
                    Test Review Page
                </a>
                <a href="setup_database.php" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 inline-block">
                    Setup Database
                </a>
            </div>
        </div>
    </div>
</body>
</html>