# 🔐 YETUGA AUTHENTICATION GUIDE

**Quick Reference for Developers**

---

## 🚀 **HOW TO USE**

### **1. Protect Any Page**
```php
<?php
require_once '../../config/session_handler.php';

// For user pages
$user_data = userSessionCheck('Page Name');

// For officer pages  
$user_data = officerSessionCheck('Page Name');

// For admin pages
$user_data = adminSessionCheck('Page Name');
?>
```

### **2. Add Session Timer**
```php
<?php echo getSessionTimer('../logout.php'); ?>
```

---

## 🔒 **SECURITY FEATURES**

### **✅ Session Management**
- **Timeout**: 30 minutes of inactivity
- **Auto-refresh**: Extends on user activity
- **Secure cookies**: HTTP-only, SameSite protection

### **✅ Role-Based Access**
- **Users**: Regular residents
- **Officers**: Transport officials  
- **Admins**: System administrators

### **✅ CSRF Protection**
```php
<?php echo csrfTokenField(); ?>
```

---

## 📁 **KEY FILES**

- `config/session_handler.php` - Main session functions
- `config/csrf_protection.php` - CSRF tokens
- `config/input_sanitization.php` - Input safety
- `config/secure_database.php` - Database security

---

**That's it! Simple and secure.** 🛡️
