# 🧪 Testing Guide - Customer Management System

## Overview

This guide provides comprehensive instructions for testing the Customer Management System. The system includes automated test suites, manual testing procedures, and test data management tools.

## 🚀 Quick Start

### Prerequisites
- XAMPP running (Apache + MySQL)
- Modern web browser (Chrome recommended for full functionality)
- Database configured (see setup instructions below)

### 1. Database Setup
```bash
# Navigate to: http://localhost/engineering-excercise/setup_database.php
# This creates the database and customers table automatically
```

### 2. Configure Database Connection
Edit `config.php` with your MySQL credentials:
```php
$host = 'localhost';
$dbname = 'customer_db';
$username = 'root';
$password = ''; // Your MySQL password
```

## 🧪 Automated Testing

### Test Runner
The automated test suite covers all major functionality:

**URL:** `http://localhost/engineering-excercise/test_runner.php`

**What it tests:**
- ✅ Database connection and schema
- ✅ Customer data insertion and retrieval
- ✅ Email validation (valid/invalid formats)
- ✅ File upload functionality
- ✅ Security features (SQL injection, XSS prevention)
- ✅ Calculator logic (addition, subtraction, complex operations)
- ✅ WebRTC component file existence

**Expected Results:**
- All tests should pass with 100% success rate
- Any failures indicate configuration or code issues

### Test Data Manager
Manage test data for comprehensive testing:

**URL:** `http://localhost/engineering-excercise/test_data_setup.php`

**Features:**
- Insert 6 sample customers from different countries
- Clean up test data
- View current test data
- Generate test URLs for manual testing

## 📋 Manual Testing Procedures

### 1. Customer Form Testing (`index.php`)

#### Valid Data Testing
1. Navigate to `http://localhost/engineering-excercise/`
2. Fill form with valid data:
   - **First Name:** John
   - **Last Name:** Doe
   - **Email:** john.doe@example.com
   - **City:** New York
   - **Country:** United States
3. Upload a JPEG/PNG image
4. Click "Save"
5. **Expected:** Success message, data saved to database

#### Email Validation Testing
Test these invalid email formats:
- `invalid-email` (no @)
- `test@` (no domain)
- `@example.com` (no username)
- `test..test@example.com` (double dots)

**Expected:** Real-time validation errors, form submission blocked

#### File Upload Testing
- **Valid files:** JPEG, PNG images
- **Invalid files:** GIF, TXT, PDF files
- **Expected:** Valid files accepted, invalid files rejected

#### Cancel Button Testing
1. Fill form with data
2. Click "Cancel"
3. **Expected:** Form resets, image persists

### 2. Customer Review Testing (`review.php`)

#### Email Lookup Testing
Test these URLs:
- `review.php?email=john.doe@example.com` (valid customer)
- `review.php?email=nonexistent@example.com` (invalid customer)
- `review.php` (no email parameter)

**Expected:** Valid customers display all information, invalid emails show "not found"

#### Image Display Testing
1. Upload image in customer form
2. Search for customer in review page
3. **Expected:** Image displays correctly

### 3. Calculator Testing

#### Basic Operations
Test these calculations:
- `5 + 3 = 8`
- `10 - 4 = 6`
- `0 + 5 = 5`
- `25 - 8 = 17`

#### Complex Operations
Test these expressions:
- `5 + 3 - 2 = 6`
- `10 - 4 + 1 = 7`
- `54 + 37 - 21 = 70`
- `100 - 50 + 25 = 75`

#### Security Testing
Try entering malicious input:
- `<script>alert('XSS')</script>`
- `'; DROP TABLE customers; --`

**Expected:** Input sanitized, no code execution

### 4. Screen Share Testing

#### Chrome Browser Testing
1. Navigate to `review.php`
2. Click "Firebase + WebRTC Screen Sharing"
3. Click "Start Screen Share"
4. **Expected:** Permission prompt, screen sharing starts

#### Browser Compatibility
- **Chrome:** Full functionality ✅
- **Firefox:** Limited screen share support ⚠️
- **Safari:** Basic functionality, limited screen share ⚠️

## 🔧 Test Configuration

### Database Configuration
Ensure `config.php` has correct settings:
```php
$host = 'localhost';
$dbname = 'customer_db';
$username = 'root';
$password = 'your_password';
```

### File Permissions
Ensure uploads directory is writable:
```bash
chmod 755 uploads/
```

### Browser Settings
- Enable JavaScript
- Allow camera/microphone permissions for screen share
- Disable popup blockers

## 📊 Test Results Interpretation

### Automated Test Results
```
📊 TEST RESULTS SUMMARY
==================================================
Total Tests: 15
Passed: 15 ✅
Failed: 0 ❌
Success Rate: 100%
```

### Manual Test Checklist
- [ ] Customer form validation works
- [ ] Email validation prevents invalid formats
- [ ] File upload accepts JPEG/PNG only
- [ ] Customer data saves to database
- [ ] Customer review displays data correctly
- [ ] Calculator performs basic operations
- [ ] Calculator handles complex expressions
- [ ] Security features prevent injection attacks
- [ ] Screen share works in Chrome
- [ ] All pages load without errors

## 🐛 Troubleshooting

### Common Issues

#### Database Connection Failed
- Check XAMPP MySQL is running
- Verify credentials in `config.php`
- Run `setup_database.php` first

#### File Upload Not Working
- Check `uploads/` directory exists and is writable
- Verify file size limits (5MB max)
- Check file type restrictions

#### Calculator Not Working
- Ensure JavaScript is enabled
- Check browser console for errors
- Verify iframe communication

#### Screen Share Not Working
- Use Chrome browser
- Allow camera/microphone permissions
- Check for HTTPS requirement (some browsers)

### Debug Information
The system includes debug information in:
- Image upload responses
- Database error messages
- Browser console logs

## 📈 Performance Testing

### Load Testing
- Submit multiple forms rapidly
- Upload multiple images simultaneously
- Test with large image files (up to 5MB)

### Browser Performance
- Test page load times
- Monitor memory usage during screen share
- Test calculator responsiveness

## 🔒 Security Testing

### Input Validation
- Test SQL injection attempts
- Test XSS payloads
- Test file upload security
- Test calculator input sanitization

### File Security
- Verify only JPEG/PNG files accepted
- Check file size limits enforced
- Test secure file naming

## 📝 Test Reporting

### Automated Test Report
The test runner provides detailed results including:
- Test execution status
- Error messages for failed tests
- Success rate percentage
- Detailed failure analysis

### Manual Test Documentation
Document any issues found during manual testing:
- Browser used
- Steps to reproduce
- Expected vs actual behavior
- Screenshots if applicable

## 🎯 Test Coverage

The testing suite covers:
- **Functional Testing:** All features work as expected
- **Security Testing:** Input validation and injection prevention
- **Performance Testing:** Load times and responsiveness
- **Compatibility Testing:** Cross-browser functionality
- **Integration Testing:** Database and file system integration
- **User Experience Testing:** Form validation and error handling

## 📞 Support

If you encounter issues during testing:
1. Check the troubleshooting section above
2. Review browser console for JavaScript errors
3. Verify database connection and permissions
4. Check file system permissions for uploads
5. Ensure XAMPP services are running properly

---

**Happy Testing! 🚀**
