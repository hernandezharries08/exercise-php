# Customer Management System - Engineering Exercise

A complete web application built with PHP, JavaScript, and Tailwind CSS for managing customer information with additional features.

## Features

### 1. Customer Information Entry Form (`index.php`)
- **Fields**: First Name, Last Name, Email, City, Country, Profile Picture
- **Validation**: Email format validation with real-time feedback
- **Country Dropdown**: United States, Canada, Japan, United Kingdom, France, Germany
- **Image Upload**: JPEG and PNG files with secure file handling
- **Form Controls**: Save and Cancel buttons with proper reset functionality

### 2. Customer Information Review Page (`review.php`)
- **Email Lookup**: Search customers by email address via URL parameter
- **Display**: Shows all customer information including uploaded image
- **Responsive Design**: Clean, modern interface using Tailwind CSS

### 3. Mini Pocket Calculator
- **Separate iFrames**: Display and buttons in separate iframes as required
- **Operations**: Addition and subtraction only
- **Security**: Input sanitization to prevent code injection
- **Communication**: Parent-child iframe communication for result display

### 4. Screen Share Utility
- **Chrome API**: Uses `getDisplayMedia()` for screen sharing
- **Real-time**: Live screen sharing with start/stop controls
- **Security**: Proper error handling and stream management

## Database Schema

### Table: `customers`
```sql
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lastname VARCHAR(255) NOT NULL,
    firstname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    city VARCHAR(255) NOT NULL,
    country VARCHAR(255) NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Setup Instructions

### Prerequisites
- XAMPP (Apache, MySQL, PHP)
- Modern web browser with Chrome API support

### Installation Steps

1. **Start XAMPP Services**
   ```bash
   # Start Apache and MySQL services in XAMPP Control Panel
   ```

2. **Setup Database**
   - Navigate to `http://localhost/engineering-excercise/setup_database.php`
   - This will create the database and table automatically

3. **Access Application**
   - Main entry form: `http://localhost/engineering-excercise/`
   - Customer review: `http://localhost/engineering-excercise/review.php`
   - Test cases: `http://localhost/engineering-excercise/test_cases.php`

### File Structure
```
engineering-excercise/
├── index.php                    # Customer entry form
├── review.php                   # Customer review page with calculator & screen share
├── calculator_display.php      # Calculator display iframe
├── calculator_buttons.php      # Calculator buttons iframe
├── screen_share_simple.php     # Screen share host (Google Chrome API)
├── screen_viewer_simple.php    # Screen share viewer
├── test_cases.php              # Comprehensive test cases
├── config.php                  # Database configuration
├── setup_database.php          # Database setup script
├── uploads/                    # Image upload directory
└── README.md                   # This file
```

## Testing Strategy

### Manual Testing Cases

#### 1. Customer Form Testing
- **Valid Data**: Submit form with all valid fields
- **Email Validation**: Test invalid email formats (missing @, no domain, etc.)
- **Required Fields**: Test submission with missing required fields
- **File Upload**: Test JPEG/PNG upload and invalid file rejection
- **Cancel Button**: Verify form reset functionality
- **Duplicate Email**: Test handling of duplicate email addresses

#### 2. Customer Review Testing
- **Email Lookup**: Test with valid and invalid email addresses
- **URL Parameter**: Test `?email=test@example.com` format
- **Image Display**: Verify uploaded images display correctly
- **No Results**: Test behavior when no customer found

#### 3. Calculator Testing
- **Basic Operations**: Test 5+3, 10-4, etc.
- **Complex Expressions**: Test 5+3-2, 10-4+1
- **Edge Cases**: Test with 0, negative results
- **Security**: Verify input sanitization prevents code injection
- **iFrame Communication**: Test display updates and result passing

#### 4. Screen Share Testing
- **Start Share**: Test screen sharing initiation
- **Stop Share**: Test manual stop functionality
- **Stream End**: Test automatic stop when user ends sharing
- **Error Handling**: Test behavior when permission denied

### Security Considerations

1. **SQL Injection Prevention**: Using prepared statements
2. **File Upload Security**: File type validation (JPEG/PNG only) and secure file naming
3. **XSS Prevention**: HTML escaping with `htmlspecialchars()`
4. **Calculator Security**: Input sanitization to prevent code execution
5. **Screen Share Security**: Proper stream handling and cleanup

## Browser Compatibility

- **Chrome/Edge**: Full functionality including screen share
- **Firefox**: All features except screen share (limited API support)
- **Safari**: Basic functionality, limited screen share support

## Technical Implementation

### PHP Features
- PDO for database operations
- Prepared statements for security
- File upload handling with validation
- Error handling and user feedback

### JavaScript Features
- Real-time form validation
- iFrame communication for calculator
- Screen sharing API integration
- Modern ES6+ features

### CSS Framework
- Tailwind CSS from CDN
- Responsive design
- Modern UI components
- Consistent styling

## Future Enhancements

1. **User Authentication**: Add login system
2. **Data Export**: CSV/PDF export functionality
3. **Advanced Calculator**: More mathematical operations
4. **Real-time Collaboration**: Enhanced screen sharing features
5. **Mobile Optimization**: Enhanced mobile experience
6. **API Integration**: RESTful API for data access
