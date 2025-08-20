# 🚌 YETUGA - URBAN MOBILITY APP

**What This App Does & How It's Built**

---

## 🎯 **PURPOSE**

**Yetuga** helps city residents find optimal transport routes and report transport issues in real-time.

**Problem Solved**: Urban mobility challenges in Ethiopian cities (Addis Ababa, Adama) with inconsistent taxi routes and unclear fare systems.

---

## 👥 **USER ROLES**

### **🚶‍♂️ Regular Users (Residents)**
- Search for transport routes (bus, taxi, minibus, train)
- View fares, travel time, route types
- Submit reports (fare disputes, roadblocks, driver behavior)
- Access urban services along routes

### **👮‍♂️ Transport Officers**
- Manage transport routes
- Update fare information
- Respond to citizen reports
- Assign traffic police to busy routes

### **👑 Administrators**
- System management
- User oversight
- Business approvals

---

## 🏗️ **TECHNICAL ARCHITECTURE**

### **Frontend**
- HTML5, CSS3, JavaScript
- Responsive design for mobile/desktop
- Modern UI with glassmorphism effects

### **Backend**
- PHP with PDO database access
- MySQL database (name: `yetuga`)
- Secure session management
- Role-based access control

### **Security**
- CSRF protection on all forms
- XSS prevention with input sanitization
- SQL injection protection with prepared statements
- Rate limiting and session security

---

## 📁 **PROJECT STRUCTURE**

```
yetuga/
├── config/           # Security & database configs
├── public/           # Web-accessible files
│   ├── user/        # User dashboard & pages
│   ├── officer/     # Officer dashboard & pages  
│   ├── admin/       # Admin dashboard & pages
│   └── api/         # Secure API endpoints
├── assets/           # CSS, JS, images
└── temp/            # Rate limiting cache
```

---

## 🚀 **CURRENT STATUS**

- ✅ **Core functionality**: Complete
- ✅ **Security**: Production-ready
- ✅ **Database**: 14 tables, fully structured
- ✅ **Authentication**: Working with role-based access

