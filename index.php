<?php
require_once 'config.php';

// handle ajax image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'upload_image') {
    header('Content-Type: application/json');
    
    // show debug info
    $debug_info = [
        'files_received' => isset($_FILES['image']),
        'file_error' => isset($_FILES['image']) ? $_FILES['image']['error'] : 'No file',
        'upload_dir_exists' => file_exists('uploads/'),
        'upload_dir_writable' => is_writable('uploads/')
    ];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Store in session for persistence across page reloads
                session_start();
                $_SESSION['uploaded_image'] = $upload_path;
                
                echo json_encode(['success' => true, 'image_path' => $upload_path, 'debug' => $debug_info]);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file', 'debug' => $debug_info]);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG and PNG allowed.', 'debug' => $debug_info]);
            exit;
        }
    } else {
        $error_message = 'No file uploaded';
        if (isset($_FILES['image'])) {
            $error_message = 'File upload error: ' . $_FILES['image']['error'];
        }
        echo json_encode(['success' => false, 'message' => $error_message, 'debug' => $debug_info]);
        exit;
    }
}

// form submission here
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action'])) {
    $lastname = $_POST['lastname'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $email = $_POST['email'] ?? '';
    $city = $_POST['city'] ?? '';
    $country = $_POST['country'] ?? '';
    
    session_start();
    $image_path = $_SESSION['uploaded_image'] ?? '';
    
    try {
        $sql = "INSERT INTO customers (lastname, firstname, email, city, country, image_path) 
                VALUES (?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                lastname = VALUES(lastname), 
                firstname = VALUES(firstname), 
                city = VALUES(city), 
                country = VALUES(country), 
                image_path = VALUES(image_path)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$lastname, $firstname, $email, $city, $country, $image_path]);
        
        // clear session after saving
        unset($_SESSION['uploaded_image']);
        
        $success_message = "Customer information saved successfully!";
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

session_start();
$current_image = $_SESSION['uploaded_image'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Information Entry - Harries Hernandez</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Customer Information Entry</h1>
            
            <?php if (isset($success_message)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="firstname" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" id="firstname" name="firstname" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="lastname" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="lastname" name="lastname" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div id="email-error" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>
                
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" id="city" name="city" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <select id="country" name="country" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select a country</option>
                        <option value="United States">United States</option>
                        <option value="Canada">Canada</option>
                        <option value="Japan">Japan</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="France">France</option>
                        <option value="Germany">Germany</option>
                    </select>
                </div>
                
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Profile Picture (JPEG/PNG)</label>
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="text-sm text-gray-500 mt-1">Only JPEG and PNG files are allowed</div>

                    <button type="button" id="upload-btn" 
                            class="mt-2 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Upload Image
                    </button>
                    
                    <div id="upload-status" class="mt-2 text-sm hidden"></div>
                    
                    <div id="image-display" class="mt-4 <?php echo ($current_image && file_exists($current_image)) ? '' : 'hidden'; ?>">
                        <p class="text-sm font-medium text-gray-700 mb-2">Uploaded Image:</p>
                        <img id="uploaded-image" src="<?php echo htmlspecialchars($current_image); ?>" 
                             alt="Uploaded Image" class="w-32 h-32 object-cover rounded-md border">
                        <p class="text-xs text-gray-500 mt-1">This image will persist across page reloads</p>
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Save
                    </button>
                    <button type="button" id="cancel-btn" 
                            class="flex-1 bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                </div>
            </form>
            
            <div class="mt-8 text-center">
                <a href="review.php" class="text-blue-600 hover:text-blue-800 underline">View Customer Information</a>
            </div>
        </div>
    </div>

    <script>
        // validate email
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value;
            const emailError = document.getElementById('email-error');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                emailError.textContent = 'Please enter a valid email address';
                emailError.classList.remove('hidden');
                this.classList.add('border-red-500');
            } else {
                emailError.classList.add('hidden');
                this.classList.remove('border-red-500');
            }
        });
        
        // cancel button functionality
        document.getElementById('cancel-btn').addEventListener('click', function() {
            document.querySelector('form').reset();
            document.getElementById('email-error').classList.add('hidden');
            document.getElementById('email').classList.remove('border-red-500');
        });
        
        // image upload functionality
        document.getElementById('upload-btn').addEventListener('click', function() {
            const fileInput = document.getElementById('image');
            const file = fileInput.files[0];
            
            if (!file) {
                showUploadStatus('Please select a file first', 'error');
                return;
            }
            
            // validate file type - only accept jpeg and png
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                showUploadStatus('Only JPEG and PNG files are allowed', 'error');
                return;
            }
            
            // validate file size (max 5MB) - this can be adjusted
            if (file.size > 5 * 1024 * 1024) {
                showUploadStatus('File size must be less than 5MB', 'error');
                return;
            }
            
            uploadImage(file);
        });
        
        function uploadImage(file) {
            const formData = new FormData();
            formData.append('image', file);
            formData.append('action', 'upload_image');
            
            showUploadStatus('Uploading...', 'info');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showUploadStatus('Image uploaded successfully!', 'success');
                    // Update image display without reloading page to preserve form data
                    updateImageDisplay(data.image_path);
                } else {
                    let errorMsg = data.message || 'Upload failed';
                    if (data.debug) {
                        errorMsg += ' (Debug: ' + JSON.stringify(data.debug) + ')';
                    }
                    showUploadStatus(errorMsg, 'error');
                }
            })
            .catch(error => {
                showUploadStatus('Upload failed: ' + error.message, 'error');
            });
        }
        
        function showUploadStatus(message, type) {
            const statusDiv = document.getElementById('upload-status');
            statusDiv.textContent = message;
            statusDiv.classList.remove('hidden', 'text-green-600', 'text-red-600', 'text-blue-600');
            
            if (type === 'success') {
                statusDiv.classList.add('text-green-600');
            } else if (type === 'error') {
                statusDiv.classList.add('text-red-600');
            } else {
                statusDiv.classList.add('text-blue-600');
            }
        }
        
        function updateImageDisplay(imagePath) {
            const imageDisplay = document.getElementById('image-display');
            const uploadedImage = document.getElementById('uploaded-image');
            
            if (imagePath) {
                uploadedImage.src = imagePath;
                imageDisplay.classList.remove('hidden');
            } else {
                imageDisplay.classList.add('hidden');
            }
        }
    </script>
</body>
</html>

